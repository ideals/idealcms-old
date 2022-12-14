<?php
/**
 * Ideal CMS (http://idealcms.ru/)
 *
 * @link      http://github.com/ideals/idealcms репозиторий исходного кода
 * @copyright Copyright (c) 2012-2018 Ideal CMS (http://idealcms.ru)
 * @license   http://idealcms.ru/license.html LGPL v3
 */

// @codingStandardsIgnoreFile
namespace Ideal\Core;

use Ideal\Field\Url;

abstract class Model
{

    public $fields;

    /** @var bool Флаг 404-ошибки */
    public $is404 = false;

    public $params;

    protected $_table;

    protected $module;

    protected $pageData;

    protected $pageNum;

    protected $pageNumTitle = ' | Страница [N]';

    protected $parentUrl;

    protected $path = array();

    protected $prevStructure;

    /** @var Model Используется только в Addon для обозначения модели-владельца аддона */
    protected $parentModel;
    protected $fieldsGroup = 'general';

    public function __construct($prevStructure)
    {
        $this->prevStructure = $prevStructure;

        $config = Config::getInstance();

        $parts = preg_split('/[_\\\\]+/', get_class($this));
        $this->module = $parts[0];
        $type = $parts[1]; // Structure или Addon
        $structureName = $parts[2];
        $structureFullName = $this->module . '_' . $structureName;

        if ($structureName === 'Home') {
            $type = 'Home';
        }

        switch ($type) {
            case 'Home':
                // Находим начальную структуру
                $structures = $config->structures;
                $structure = reset($structures);
                $type = $parts[1];
                $structureName = $structure['structure'];
                $structureName = substr($structureName, strpos($structureName, '_') + 1);
                $structure = $config->getStructureByName($structure['structure']);
                $className = $config->getStructureClass($structure['structure'], 'Config');
                $cfg = new $className();
                break;
            case 'Structure':
                $structure = $config->getStructureByName($structureFullName);
                $className = $config->getStructureClass($structure['structure'], 'Config');
                $cfg = new $className();
                break;
            case 'Addon':
                $className = $this->module . '\\Addon\\' . $structureName . '\\Config';
                $cfg = new $className();
                break;
            default:
                throw new \Exception('Неизвестный тип: ' . $type);
                break;
        }

        $this->params = $cfg::$params;
        $this->fields = $cfg::$fields;

        $this->_table = strtolower($config->db['prefix'] . $this->module . '_' . $type . '_' . $structureName);
    }

    /**
     * Определение сокращённого имени структуры Модуль_Структура по имени этого класса
     *
     * @return string Сокращённое имя структуры, используемое в БД
     */
    public static function getStructureName()
    {
        $parts = explode('\\', get_called_class());
        return $parts[0] . '_' . $parts[2];
    }

    public function __get($name)
    {
        if ($name == 'object') {
            throw new \Exception('Свойство object упразднено.');
        }
    }

    public function detectActualModel()
    {
        $config = Config::getInstance();
        $model = $this;
        $count = count($this->path);

        $class = get_class($this);
        if ($class == 'Ideal\\Structure\\Home\\Site\\Model') {
            // В случае если у нас открыта главная страница, не нужно переопределять модель как обычной страницы
            return $model;
        }

        if ($count > 1) {
            $end = $this->path[($count - 1)];

            // Некоторые конечные структуры могут не иметь выбора типа раздела.
            // Значит и не будет поля "structure", тогда отдаём ранее найденную модель.
            if (!isset($end['structure'])) {
                return $model;
            }
            
            $prev = $this->path[($count - 2)];

            $endClass = ltrim(Util::getClassName($end['structure'], 'Structure'), '\\');
            $thisClass = get_class($this);

            // Проверяем, соответствует ли класс объекта вложенной структуре
            if (strpos($thisClass, $endClass) === false) {
                // Если структура активного элемента не равна структуре предыдущего элемента,
                // то нужно инициализировать модель структуры активного элемента
                $name = explode('\\', get_class($this));
                $modelClassName = Util::getClassName($end['structure'], 'Structure') . '\\' . $name[3] . '\\Model';
                $prevStructure = $config->getStructureByName($prev['structure']);
                /* @var $model Model */
                $model = new $modelClassName($prevStructure['ID'] . '-' . $end['ID']);
                // Передача всех данных из одной модели в другую
                $model = $model->setVars($this);
            }
        }
        return $model;
    }

    /**
     * Установка свойств объекта по данным из массива $vars
     *
     * Вызывается при копировании данных из одной модели в другую
     *
     * @param object $model Массив переменных объекта
     * @return $this Либо ссылка на самого себя, либо новый объект модели
     */
    public function setVars($model)
    {
        $vars = get_object_vars($model);
        foreach ($vars as $k => $v) {
            if (in_array($k, array('_table', 'module', 'params', 'fields', 'prevStructure'))) {
                continue;
            }
            $this->$k = $v;
        }
        return $this;
    }

    // Получаем информацию о странице

    /**
     * Определение пути с помощью prev_structure по инициализированному $pageData
     *
     * @return array Массив, содержащий элементы пути к $pageData
     */
    public function detectPath()
    {
        $config = Config::getInstance();

        // Определяем локальный путь в этой структуре
        $localPath = $this->getLocalPath();

        // По первому элементу в локальном пути, опеределяем, какую структуру нужно вызвать
        if (isset($localPath[0])) {
            $first = $localPath[0];
        } else {
            $first['prev_structure'] = $this->prevStructure;
        }

        list($prevStructureId, $prevElementId) = explode('-', $first['prev_structure']);
        $structure = $config->getStructureByPrev($first['prev_structure']);

        if ($prevStructureId == 0) {
            // Если предыдущая структура стартовая — заканчиваем
            array_unshift($localPath, $structure);
            return $localPath;
        }

        // Если предыдущая структура не стартовая —
        // инициализируем её модель и продолжаем определение пути в ней
        $className = Util::getClassName($structure['structure'], 'Structure') . '\\Site\\Model';

        $structure = new $className('');
        $structure->setPageDataById($prevElementId);

        $path = $structure->detectPath();
        $path = array_merge($path, $localPath);

        return $path;
    }

    // Устанавливаем информацию о странице

    /**
     * Построение пути в рамках одной структуры.
     * Этот метод обязательно должен быть переопределён перед использованием.
     * Если он не будет переопределён, то будет вызвано исключение.
     *
     * @throws \Exception
     */
    protected function getLocalPath()
    {
        throw new \Exception('Вызов не переопределённого метода getLocalPath');
    }

    /**
     * @param int $page Номер отображаемой страницы
     * @return array Полученный список элементов
     */
    public function getList($page = null)
    {
        $db = Db::getInstance();

        if (!empty($this->filter)) {
            $_sql = $this->filter->getSql();
        } else {
            $where = ($this->prevStructure !== '') ? "e.prev_structure='{$this->prevStructure}'" : '';
            $where = $this->getWhere($where);
            $order = $this->getOrder();
            $_sql = "SELECT e.* FROM {$this->_table} AS e {$where} {$order}";
        }

        if (is_null($page)) {
            $this->setPageNum($page);
        } else {
            // Определяем кол-во отображаемых элементов на основании названия класса
            $class = strtolower(get_class($this));
            $class = explode('\\', trim($class, '\\'));
            $nameParam = ($class[3] == 'admin') ? 'elements_cms' : 'elements_site';
            $onPage = $this->params[$nameParam];

            $page = $this->setPageNum($page);
            $start = ($page - 1) * $onPage;

            $_sql .= " LIMIT {$start}, {$onPage}";
        }

        $list = $db->select($_sql);

        return $list;
    }

    /**
     * Добавление к where-запросу фильтра по category_id
     *
     * @param string $where Исходная WHERE-часть
     * @return string Модифицированная WHERE-часть, с расширенным запросом, если установлена GET-переменная category
     */
    protected function getWhere($where)
    {
        if ($where != '') {
            // Убираем из строки начальные команды AND или OR
            $where = trim($where);
            $where = preg_replace('/(^AND)|(^OR)/i', '', $where);
            $where = 'WHERE ' . $where;
        }
        return $where;
    }

    /**
     * Формирование ORDER-части запроса
     *
     * @return string Сформированная ORDER-часть
     */
    protected function getOrder()
    {
        $request = new Request();
        $order = 'ORDER BY e.';
        if ($request->asc) {
            $order .= $request->asc;
        } elseif ($request->desc) {
            $order .= $request->desc . ' DESC';
        } else {
            $order .= $this->params['field_sort'];
        }
        return $order;
    }

    /**
     * Получение из БД данных открытой страницы (в том числе и подключённых аддонов)
     *
     * @return mixed
     * @throws \Exception
     */
    public function getPageData()
    {
        if (is_null($this->pageData)) {
            $this->initPageData();
        }
        return $this->pageData;
    }

    public function setPageData($pageData)
    {
        $this->pageData = $pageData;
    }

    /**
     * Установка pageData по ID элемента
     *
     * @param int $id ID элемента
     * @throws \Exception В случае, если нет элемента с указанным ID
     */
    public function initPageDataById($id)
    {
        $id = (int)$id;

        $db = Db::getInstance();
        $result = $db->select('SELECT * FROM ' . $this->_table . ' WHERE ID=:id', array('id' => $id));
        if (empty($result[0])) {
            throw new \Exception('Элемент не найден');
        }

        $this->initPageData($result[0]);
    }

    public function initPageData($pageData = null)
    {
        if ($pageData === null) {
            $this->pageData = end($this->path);
        } else {
            $this->pageData = $pageData;
        }

        // Получаем переменные шаблона
        $config = Config::getInstance();
        foreach ($this->fields as $k => $v) {
            // Пропускаем все поля, которые не являются аддоном
            if (strpos($v['type'], '_Addon') === false) {
                continue;
            }

            // В случае, если 404 ошибка, и нужной страницы в БД не найти
            if (!isset($this->pageData[$k])) {
                continue;
            }

            // Определяем структуру на основании названия класса
            $structure = $config->getStructureByClass(get_class($this));

            if ($structure === false) {
                // Не удалось определить структуру из конфига (Home)
                // Определяем структуру, к которой принадлежит последний элемент пути
                $prev = count($this->path) - 2;
                if ($prev >= 0) {
                    $prev = $this->path[$prev];
                    $structure = $config->getStructureByName($prev['structure']);
                } else {
                    throw new \Exception('Не могу определить структуру для шаблона');
                }
            }

            // Обходим все аддоны, подключенные к странице
            $addonsInfo = json_decode($this->pageData[$k]);

            if (is_array($addonsInfo)) {
                foreach ($addonsInfo as $addonInfo) {
                    // Инициализируем модель аддона
                    $class = strtolower(get_class($this));
                    $class = explode('\\', trim($class, '\\'));
                    $modelName = ($class[3] == 'admin') ? '\\AdminModel' : '\\SiteModel';
                    $className = Util::getClassName($addonInfo[1], 'Addon') . $modelName;
                    $prevStructure = $structure['ID'] . '-' . $this->pageData['ID'];
                    $addon = new $className($prevStructure);
                    $addon->setParentModel($this);
                    list(, $fildsGroup) = explode('_', $addonInfo[1]);
                    $addon->setFieldsGroup(strtolower($fildsGroup) . '-' . $addonInfo[0]);
                    $pageData = $addon->getPageData();
                    if (!empty($pageData)) {
                        $this->pageData['addons'][] = $pageData;
                    }
                }
            }
        }
    }

    /**
     * Получение листалки для шаблона и стрелок вправо/влево
     *
     * @param string $pageName Название get-параметра, содержащего страницу
     * @return mixed
     */
    public function getPager($pageName)
    {
        // По заданному названию параметра страницы определяем номер активной страницы
        $request = new Request();
        $page = $this->setPageNum($request->{$pageName});

        // Строка запроса без нашего параметра номера страницы
        $query = $request->getQueryWithout($pageName);

        // Определяем кол-во отображаемых элементов на основании названия класса
        $class = strtolower(get_class($this));
        $class = explode('\\', trim($class, '\\'));
        $nameParam = ($class[3] == 'admin') ? 'elements_cms' : 'elements_site';
        $onPage = $this->params[$nameParam];

        $countList = $this->getListCount();

        if (($countList > 0) && (ceil($countList / $onPage) < $page)) {
            // Если для запрошенного номера страницы нет элементов - выдать 404
            $this->is404 = true;
            return false;
        }

        $pagination = new Pagination();
        // Номера и ссылки на доступные страницы
        $pager['pages'] = $pagination->getPages($countList, $onPage, $page, $query, $pageName);
        $pager['prev'] = $pagination->getPrev(); // ссылка на предыдущю страницу
        $pager['next'] = $pagination->getNext(); // cсылка на следующую страницу
        $pager['total'] = $countList; // общее количество элементов в списке
        $pager['num'] = $onPage; // количество элементов на странице

        return $pager;
    }

    /**
     * Получить общее количество элементов в списке
     *
     * @return int Количество элементов в списке
     */
    public function getListCount()
    {
        $db = Db::getInstance();

        if (!empty($this->filter)) {
            $_sql = $this->filter->getSqlCount();
        } else {
            $where = ($this->prevStructure !== '') ? "e.prev_structure='{$this->prevStructure}'" : '';
            $where = $this->getWhere($where);

            // Считываем все элементы первого уровня
            $_sql = "SELECT COUNT(e.ID) FROM {$this->_table} AS e {$where}";
        }
        $list = $db->select($_sql);

        return $list[0]['COUNT(e.ID)'];
    }

    /**
     * Получение номера отображаемой страницы
     *
     * @return int Номер отображаемой страницы
     */
    public function getPageNum()
    {
        return isset($this->pageNum) ? $this->pageNum : 1;
    }

    public function getParentUrl()
    {
        if ($this->parentUrl != '') {
            return $this->parentUrl;
        }

        $url = new Url\Model();
        $this->parentUrl = $url->setParentUrl($this->path);

        return $this->parentUrl;
    }

    public function getPath()
    {
        return $this->path;
    }

    /**
     * Получение названия основной таблицы модели
     *
     * @return string
     */
    public function getTableName()
    {
        return $this->_table;
    }

    public function setPath($path)
    {
        $this->path = $path;
        $end = end($path);
        if (empty($end['prev_structure'])) {
            if (empty($end['ID'])) {
                // это 404-ая страница, prev_structure устанавливать не надо
            } else {
                // непонятно, когда такой случай может быть, скорее всего это ошибка
                throw new \Exception('prev_structure don`t set');
            }
        } else {
            $this->prevStructure = $end['prev_structure'];
        }
    }

    public function getPrevStructure()
    {
        return $this->prevStructure;
    }

    public function setPrevStructure($prevStructure)
    {
        $this->prevStructure = $prevStructure;
    }

    public function setPageDataById($id)
    {
        $db = Db::getInstance();

        $_sql = "SELECT * FROM {$this->_table} WHERE ID=:id";
        $pageData = $db->select($_sql, array('id' => $id));
        if (isset($pageData[0]['ID'])) {
            // TODO сделать обработку ошибки, когда по ID ничего не нашлось
            $this->setPageData($pageData[0]);
        }
    }

    /**
     * Установка номера отображаемой страницы
     *
     * С номером страницы всё понятно, а вот $pageNumTitle позволяет изменить стандартный шаблон
     * суффикса для листалки " | Страница [N]" на любой другой суффикс, где
     * вместе [N] будет подставляться номер страницы.
     *
     * @param int $pageNum Номер отображаемой страницы
     * @param null $pageNumTitle Строка для замены стандартного суффикса листалки в тайтле
     * @return int Безопасный номер страницы
     */
    public function setPageNum($pageNum, $pageNumTitle = null)
    {
        if (isset($this->pageNum)) {
            return $this->pageNum;
        }
        $this->pageNum = 0;
        if ($pageNum !== null) {
            $page = intval(substr($pageNum, 0, 10)); // отсекаем всякую ерунду и слишком большие числа в листалке
            // Если номер страницы отрицательный или ноль, то устанавливаем первую страницу
            $this->pageNum = ($page <= 0) ? 1 : $page;
            if ($pageNum != 0 && $this->pageNum != $pageNum) {
                // Если корректный номер страницы не совпадает с переданным - 404 ошибка
                $this->is404 = true;
            }
        }

        if (!is_null($pageNumTitle)) {
            $this->pageNumTitle = $pageNumTitle;
        }

        return $this->pageNum;
    }

    /**
     * Метод используется только в моделях Addon для установки модели владельца этого аддона
     *
     * @param $model
     */
    public function setParentModel($model)
    {
        $this->parentModel = $model;
    }

    /**
     * Метод используется в полях аддонов для получения доступа к модели владельца аддона
     *
     * @return Model
     */
    public function getParentModel()
    {
        return $this->parentModel;
    }

    public function setFieldsGroup($name)
    {
        $this->fieldsGroup = $name;
    }

    /**
     * "Умная" установка 404: если флаг уже установлен в true, то не сбрасываем его
     *
     * @param bool $is404 Устанавливаемый флаг 404-ой ошибки
     *
     * @return $this
     */
    public function set404(bool $is404): self
    {
        $this->is404 = $this->is404 || $is404;

        return $this;
    }

    /**
     * Формирование prev_structure из текущего элемента
     *
     * @return string Строка prev_structure
     * @throws \Exception
     */
    public function getSelfStructure()
    {
        $data = $this->getPageData();
        if (isset($data['prev_structure'])) {
            $config = Config::getInstance();
            $structure = $config->getStructureByClass(get_class($this));
            $prevStructure = $structure['ID'] . '-' . $data['ID'];
        } else {
            throw new \Exception('No prev_structure in data');
        }
        return $prevStructure;
    }
}
