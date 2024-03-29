<?php
/**
 * Ideal CMS (http://idealcms.ru/)
 * @link      http://github.com/ideals/idealcms репозиторий исходного кода
 * @copyright Copyright (c) 2012-2018 Ideal CMS (http://idealcms.ru)
 * @license   http://idealcms.ru/license.html LGPL v3
 */

namespace Ideal\Structure\Tag\Site;

use Exception;
use Ideal\Core\Config;
use Ideal\Core\Db;
use Ideal\Core\Pagination;
use Ideal\Core\Request;

/**
 * Class ModelAbstract
 * @package Ideal\Structure\Tag\Site
 */
class ModelAbstract extends \Ideal\Structure\Part\Site\ModelAbstract
{
    /** @var bool Флаг отображения списка тегов (false), или списка элементов, которым присвоен тег (true) */
    protected bool $countSql = false;
    /**
     * @var int
     */
    protected int $countElements;

    /**
     * Определение страницы по URL
     *
     * Если передаётся один URL, то выводится только один тэг. Дополнительные теги передаются через параметр tags,
     * через запятую.
     *
     * @param array $path Разобранная часть URL
     * @param array $url Оставшаяся, неразобранная часть URL
     * @return $this
     */
    public function detectPageByUrl(array $path, array $url)
    {
        parent::detectPageByUrl($path, $url);

        $request = new Request();
        $request->action = 'detail';

        return $this;
    }

    /**
     * Получение списка элементов, которым присвоен тег (в случае, если тег присваивается нескольким структурам)
     *
     * @param int|null $page Номер отображаемой страницы списка
     * @param string $fieldNames Перечень извлекаемых полей, общих для всех структур
     * @param string $orderBy Поле, присутствующее во всех структурах, по которому проводится сортировка списка
     * @return array Список элементов, которым присвоен тег из $this->pageData
     * @throws Exception
     */
    public function getElements(?int $page = null, string $fieldNames = 'ID,name,url', string $orderBy = 'date_create'): array
    {
        $config = Config::getInstance();
        $db = Db::getInstance();
        $id = $this->pageData['ID'];
        $tableTag = $config->db['prefix'] . 'ideal_medium_taglist';

        // Считываем все связи этого тега
        $sql = "SELECT * FROM $tableTag WHERE tag_id=$id";
        $listTag = $db->select($sql);

        // Раскладываем айдишники элементов по разделам
        $tables = [];
        foreach ($listTag as $v) {
            $tables[$v['structure_id']][] = $v['part_id'];
        }

        // Построение запросов для извлечения данных из таблиц структур
        $order = (empty($orderBy)) ? '' : ',' . $orderBy;
        $elements = [];
        foreach ($tables as $structureId => $parts) {
            $structure = $config->getStructureById($structureId);
            $tableStructure = $config->getTableByName($structure['structure']);
            $structure = explode('_', $structure['structure']);
            $class = '\\' . $structure[0] . '\\Structure\\' . $structure[1] . '\\Site\\Model';

            // Проверяем нет ли у модели структуры метода для получения элементов привязанных к определённому тегу
            if (method_exists($class, 'tagElementsList')) {
                $classModel = new $class('');
                $structureElementList = $classModel->tagElementsList($parts);
                foreach ($structureElementList as $item) {
                    $elements[] = $item;
                }
                unset($tables[$structureId]);
            } else {
                $ids = '(' . implode(',', $parts) . ')';
                $sql = "SELECT $fieldNames$order, '$class' as class_name
                      FROM $tableStructure WHERE $tableStructure.is_active=1 AND $tableStructure.ID IN $ids";
                $tables[$structureId] = $sql;
            }
        }

        $this->countElements = count($elements);

        // Получаем часть массива для отображения на странице
        $start = ($page > 1) ? ($page - 1) * $this->params['elements_site'] : 0;
        return array_slice($elements, $start, $this->params['elements_site']);
    }

    /**
     * Получение листалки для шаблона и стрелок вправо/влево
     *
     * @param string $pageName Название get-параметра, содержащего страницу
     * @return array
     */
    public function getElementsPager(string $pageName): array
    {
        // По заданному названию параметра страницы определяем номер активной страницы
        $request = new Request();
        $page = $this->setPageNum($request->{$pageName});

        // Строка запроса без нашего параметра номера страницы
        $query = $request->getQueryWithout($pageName);

        // Определяем кол-во отображаемых элементов на основании названия класса
        $class = strtolower(get_class($this));
        $class = explode('\\', trim($class, '\\'));
        $nameParam = ($class[3] === 'admin') ? 'elements_cms' : 'elements_site';
        $onPage = $this->params[$nameParam];

        $countList = $this->countElements;

        if (($countList > 0) && (ceil($countList / $onPage) < $page)) {
            // Если для запрошенного номера страницы нет элементов - выдать 404
            $this->is404 = true;
            return [];
        }

        $pagination = new Pagination();
        // Номера и ссылки на доступные страницы
        $pager['pages'] = $pagination->getPages($countList, $onPage, $page, $query, $pageName);
        $pager['prev'] = $pagination->getPrev(); // ссылка на предыдущую страницу
        $pager['next'] = $pagination->getNext(); // ссылка на следующую страницу
        $pager['total'] = $countList; // общее количество элементов в списке
        $pager['num'] = $onPage; // количество элементов на странице

        return $pager;
    }

    /**
     * Получение списка элементов, которым присвоен тег (в случае, если тег присваивается одной структуре)
     *
     * @param int $page Номер отображаемой страницы списка
     * @param string $structureName Название структуры, из которой выбираются элементы с указанным тегом
     * @param string $prefix Префикс для формирования URL
     * @return array Список элементов, которым присвоен тег из $this->pageData
     * @throws Exception
     */
    public function getElementsByStructure(int $page, string $structureName, string $prefix): array
    {
        $config = Config::getInstance();
        $db = Db::getInstance();
        $id = $this->pageData['ID'];
        $tableTag = $config->db['prefix'] . 'ideal_medium_taglist';
        $structure = $config->getStructureByName($structureName);
        $tableStructure = $config->getTableByName($structureName);

        // Определяем по какому полю нужно проводить сортировку
        $orderBy = '';
        if (isset($structure['params']['field_sort'])
            && $structure['params']['field_sort'] !== ''
        ) {
            $orderBy = 'ORDER BY e.' . $structure['params']['field_sort'];
        }

        $this->countSql = "FROM $tableStructure AS e
                  INNER JOIN $tableTag AS tag ON (tag.part_id = e.ID)
                  WHERE tag.tag_id=$id AND tag.structure_id={$structure['ID']}";

        $sql = 'SELECT e.* ' . $this->countSql . $orderBy . $this->getSqlLimit($page);

        $result = $db->select($sql);

        // Формируем правильные ссылки
        foreach ($result as $k => $v) {
            $result[$k]['link'] = \Ideal\Field\Url\Model::getUrlWithPrefix($v, $prefix);
        }

        return $result;
    }

    /**
     * Построение LIMIT части sql-запроса
     *
     * @param null|int $page Номер отображаемой страницы
     * @return string LIMIT часть sql-запроса (например 'LIMIT 10, 10'
     */
    protected function getSqlLimit(?int $page): string
    {
        if ($page === null) {
            $this->setPageNum($page);
            return '';
        }

        // Определяем кол-во отображаемых элементов на основании названия класса
        $class = strtolower(get_class($this));
        $class = explode('\\', trim($class, '\\'));
        $nameParam = ($class[3] === 'admin') ? 'elements_cms' : 'elements_site';
        $onPage = $this->params[$nameParam];

        $page = $this->setPageNum($page);
        $start = ($page - 1) * $onPage;

        return " LIMIT $start, $onPage";
    }
}
