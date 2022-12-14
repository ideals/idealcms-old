<?php
/**
 * Ideal CMS (http://idealcms.ru/)
 *
 * @link      http://github.com/ideals/idealcms репозиторий исходного кода
 * @copyright Copyright (c) 2012-2018 Ideal CMS (http://idealcms.ru)
 * @license   http://idealcms.ru/license.html LGPL v3
 */

namespace Ideal\Structure\Service\SiteData;

use Ideal\Core\Util;
use JsonException;

/**
 * Чтение, отображение и запись специального формата конфигурационных php-файлов
 * todo написать магические геттеры и сеттеры, чтобы переменные конфига можно было изменять без обращения к массивам
 */
class ConfigPhp
{

    /** @var array Массив для хранения считанных данных из php-файла */
    protected $params = array();

    /**
     * Геттер для защищённого поля $params
     * @return array Набор считанных из файла параметров
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * Замена значений настроек в $this->params на данные, введённые пользователем
     */
    public function pickupValues()
    {
        $response = array('res' => true, 'text' => '');
        $pageData = array();
        $applyChange = new ApplyChange();
        foreach ($this->params as $tabId => $tab) {
            foreach ($tab['arr'] as $field => $param) {
                $fieldName = $tabId . '_' . $field;
                $model = new MockModel('');
                $model->fields[$fieldName] = $param;
                $pageData[$fieldName] = $param['value'];
                $model->setPageData($pageData);

                $fieldClass = Util::getClassName($param['type'], 'Field') . '\\Controller';
                /** @noinspection PhpUndefinedMethodInspection */
                /** @var $fieldModel \Ideal\Field\AbstractController */
                $fieldModel = $fieldClass::getInstance();
                $fieldModel->setModel($model, $fieldName, 'general');

                // Получаем данные от пользователя
                $value = $fieldModel->pickupNewValue();

                // Если нужно сделать ещё какие-нибудь действия после изменения данного поля,
                // то вызываем соответствующий метод
                if (method_exists($applyChange, $field . 'Change') && $param["value"] != $value) {
                    $methodName = $field . 'Change';
                    $applyChange->setValue($value);
                    $applyChange->$methodName();
                }

                // Обработка данных введённых пользователем
                $item = $fieldModel->parseInputValue(false);

                if (!empty($item['message'])) {
                    $response = array('res' => false, 'text' => $item['message']);
                    return $response;
                }
                $this->params[$tabId]['arr'][$field]['value'] = $value;
            }
        }
        return $response;
    }

    /**
     * Сохранение обработанных конфигурационных данных в файл
     *
     * @param string $fileName Название php-файла, в который сохраняются данные
     * @return false|int Возвращает количество записанных в файл байт или false
     * @throws JsonException
     */
    public function saveFile(string $fileName)
    {
        // Изменяем постоянные настройки сайта
        $file = "<?php\n" . '/** @noinspection ALL */' . "\n// @codingStandardsIgnoreFile\nreturn [\n";
        foreach ($this->params as $tabId => $tab) {
            $pad = 4;
            if ($tabId !== 'default') {
                $file .= "    '{$tabId}' => [ // {$tab['name']}\n";
                $pad = 8;
            }
            foreach ($tab['arr'] as $field => $param) {
                $options = (defined('JSON_UNESCAPED_UNICODE')) ? JSON_UNESCAPED_UNICODE : 0;
                $values = '';
                if ($param['type'] === 'Ideal_Select') {
                    $values =  ' | ' . json_encode($param['values'], JSON_THROW_ON_ERROR | $options);
                }

                // Экранируем переводы строки для сохранения в файле
                $param['value'] = str_replace("\r", '', $param['value']);
                $param['value'] = str_replace("\n", '\n', $param['value']);

                $file .= str_repeat(' ', $pad) . "'" . $field . "' => " . '"' . $param['value'] . '", '
                    . '// ' . $param['label'] . ' | ' . $param['type'] . $values . "\n";
            }
            if ($tabId !== 'default') {
                $file .= "    ],\n";
            }
        }

        $file .= "];\n";

        return file_put_contents($fileName, $file, FILE_USE_INCLUDE_PATH);
    }

    /**
     * Изменение настроек на введённые пользователем и сохранение их в файл
     *
     * @param string $fileName Название php-файла, в который сохраняются данные
     * @param bool $res Флаг отражающий наличие ошибок на момент передачи работы методу
     * @param string $class Набор классов для информирующего блока
     * @param string $text Текст для информирующего блока
     *
     * @return string Сообщение об успешности сохранения данных в файл
     * @throws JsonException
     */
    public function changeAndSave(
        string $fileName,
        bool   $res = true,
        string $class = '',
        string $text = 'Настройки сохранены!'
    ): string {
        if (empty($class)) {
            $class = 'alert alert-block alert-success';
        }
        // Заменяем настройки на введённые пользователем
        $response = $this->pickupValues();
        if ($response['res'] === false) {
            $text = $response['text'];
            $class = 'alert alert-danger';
        } elseif ($res && $this->saveFile($fileName) === false) {
            $text = 'Не получилось сохранить настройки в файл ' . $fileName;
            $class = 'alert alert-danger';
        }

        return <<<DONE
        <div class="{$class} fade in">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <span class="alert-heading">{$text}</span></div>
DONE;
    }

    /**
     * Считывание данных из php-файла
     *
     * @param string $fileName Имя php-файла из которого читается конфигурация
     * @return bool Флаг успешного считывания данных из файла
     */
    public function loadFile($fileName)
    {
        if (!stream_resolve_include_path($fileName)) {
            return false;
        }

        $cfg = file($fileName, FILE_USE_INCLUDE_PATH);

        // Убираем служебные символы (пробелы, табуляцию) из начала и из конца строк
        array_walk(
            $cfg,
            function (&$value) {
                $value = trim($value);
            }
        );

        $skip = [
            '<?php',
            '/** @noinspection ALL */',
            '// @codingStandardsIgnoreFile',
            'return [',
            '];'
        ];

        $params['default'] = array(
            'arr' => array(),
            'name' => 'Основное'
        );

        $c = count($cfg);
        // Проходимся по всем строчкам php-файла и заполняем массив $params
        for ($i = 0; $i < $c; $i++) {
            $v = $cfg[$i];
            if (in_array($v, $skip)) {
                continue;
            }
            if (strpos($v, "', // ")) {
                $cols = explode("', // ", $v);
            } else {
                $cols = explode('", // ', $v);
            }
            $other = $cols[0];
            $label = isset($cols[1]) ? $cols[1] : null;
            if (is_null($label)) {
                // Комментария в нужном формате нет, значит это массив
                preg_match('/\'(.*)\'\s*=>\s*\[\s*\/\/\s*(.*)/i', $other, $match);
                if (!isset($match[1]) || !isset($match[2])) {
                    echo "Ошибка парсинга файла {$fileName} в строке $i<br />";
                    exit;
                }
                $array = array();
                while ($cfg[++$i] != '],') {
                    $v = $cfg[$i];
                    $param = $this->parseStr($v);
                    $array = array_merge($array, $param);
                }
                // Записываем массив данных в соответствующем формате
                $params[$match[1]] = array(
                    'arr' => $array,
                    'name' => $match[2]
                );
            } else {
                // Считываем и записываем переменную первого уровня
                $param = $this->parseStr($v);
                $params['default']['arr'] = array_merge($params['default']['arr'], $param);
            }
        }
        $this->params = $params;
        return true;
    }

    /**
     * Парсим одну строку конфига в массив данных
     *
     * @param string $str Строка конфига
     *
     * @return array
     */
    protected function parseStr($str)
    {
        if (strpos($str, "', // ")) {
            list ($other, $label) = explode("', // ", $str);
        } else {
            list ($other, $label) = explode('", // ', $str);
        }
        $label = chop($label);
        $fields = explode(' | ', $label);
        $label = $fields[0];
        $type = $fields[1];
        if ($type == '') {
            $type = 'Ideal_Text';
        }
        if (strpos($other, " => '")) {
            list ($name, $value) = explode(" => '", $other);
        } else {
            list ($name, $value) = explode(' => "', $other);
        }
        $value = str_replace('\n', "\n", $value); // заменяем переводы строки на правильные символы
        $fieldName = trim($name, ' \''); // убираем стартовые пробелы и кавычку у названия поля
        $param[$fieldName] = array(
            'label' => $label,
            'value' => $value,
            'type' => $type,
            'sql' => '',
        );
        if ($type === 'Ideal_Select') {
            $param[$fieldName]['values'] = json_decode($fields[2], true, 512, JSON_THROW_ON_ERROR);
        }
        return $param;
    }

    /**
     * Сеттер для защищённого поля $this->params
     * @param array $params Модифицированный набор полей для сохранения в конфигурационном файле
     */
    public function setParams($params)
    {
        $this->params = $params;
    }

    /**
     * Отображение считанных конфигурационных данных в виде полей ввода с подписями
     *
     * @return string Сгенерированный HTML-код
     */
    public function showEdit()
    {
        $tabs = '<ul class="nav nav-tabs">';
        $tabsContent = '<div class="tab-content">';
        $first = true;
        foreach ($this->params as $tabId => $tab) {
            if (!empty($tab['arr'])) {
                if ($first) {
                    $active = 'active';
                    $first = false;
                } else {
                    $active = '';
                }
                $tabs .= '<li class="' . $active . '">'
                    . '<a href="#' . $tabId . '" data-toggle="tab">' . $tab['name'] . '</a>'
                    . '</li>';
                $tabsContent .= '<div class="tab-pane well ' . $active . '" id="' . $tabId . '">';
                $pageData = array();
                foreach ($tab['arr'] as $field => $param) {
                    $fieldName = $tabId . '_' . $field;
                    $model = new MockModel('');
                    $model->fields[$fieldName] = $param;
                    $pageData[$fieldName] = $param['value'];
                    $model->setPageData($pageData);

                    $fieldClass = Util::getClassName($param['type'], 'Field') . '\\Controller';
                    /** @noinspection PhpUndefinedMethodInspection */
                    /** @var $fieldModel \Ideal\Field\AbstractController */
                    $fieldModel = $fieldClass::getInstance();
                    $fieldModel->setModel($model, $fieldName, 'general');
                    $fieldModel->labelClass = '';
                    $fieldModel->inputClass = '';
                    $tabsContent .= $fieldModel->showEdit();
                }
                $tabsContent .= '</div>';
            }
        }
        $tabs .= '</ul>';
        $tabsContent .= '</div>';
        if (count($this->params) == 1) {
            // Если вкладка только одна, то вкладки не надо отображать
            $tabs = '';
        }
        return $tabs . $tabsContent;
    }
}
