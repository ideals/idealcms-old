<?php
/**
 * Ideal CMS (http://idealcms.ru/)
 *
 * @link      http://github.com/ideals/idealcms репозиторий исходного кода
 * @copyright Copyright (c) 2012-2018 Ideal CMS (http://idealcms.ru/)
 * @license   http://idealcms.ru/license.html LGPL v3
 */

namespace Ideal\Field\Select;

use Exception;
use Ideal\Field\AbstractController;
use Ideal\Medium\AbstractModel;

/**
 * Поле для выбора значения из списка вариантов
 *
 * Варианты значений можно либо задать непосредственно при описании поля
 * в конфигурационном файле, присвоив полю values массив со списком значений:
 *     'currency' => array(
 *         'label'  => 'Валюта',
 *         'sql'    => 'tinyint',
 *         'type'   => 'Ideal_Select',
 *         'values' => array('руб', 'usd')
 *     ),
 *
 * Либо присвоить полю medium название медиума для получения списка значений
 *     'structure' => array(
 *         'label' => 'Тип раздела',
 *         'sql'   => 'varchar(30) not null',
 *         'type'  => 'Ideal_Select',
 *         'medium'=> '\\Ideal\\Medium\\StructureList\\Model'
 *     ),
 */
class Controller extends AbstractController
{

    /** @inheritdoc */
    protected static $instance;

    /** @var array Список вариантов выбора для select */
    protected array $list = [];

    /**
     * {@inheritdoc}
     * @throws Exception
     */
    public function getInputText(): string
    {
        $html = '<select class="form-control" name="' . $this->htmlName . '" id="' . $this->htmlName . '">';
        $value = $this->getValue();
        foreach ($this->list as $k => $v) {
            $selected = '';
            if ($k === $value) {
                $selected = ' selected="selected"';
            }
            $html .= '<option value="' . $k . '"' . $selected . '>' . $v . '</option>';
        }
        $html .= '</select>';
        return $html;
    }

    /**
     * {@inheritdoc}
     */
    public function getValue(): string
    {
        $value = parent::getValue();
        if ($value === '') {
            // Если значение не указано, то будет выбран первый элемент из списка
            $keys = array_keys($this->list);
            $value = $keys[0];
        }
        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function getValueForList(array $values, string $fieldName): string
    {
        $value = $values[$fieldName];

        if (isset($this->field['values'])) {
            // Если значения select заданы с помощью массива в поле values
            $value = $this->field['values'][$value];
        }

        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function setModel($model, string $fieldName, string $groupName = 'general'): void
    {
        parent::setModel($model, $fieldName, $groupName);

        if (isset($this->field['values'])) {
            // Если значения select заданы с помощью массива в поле values
            $this->list = $this->field['values'];
            return;
        }

        // Загоняем в $this->list список значений select
        $className = $this->field['medium'];
        /** @var AbstractModel $medium */
        $medium = new $className($this->model, $fieldName);
        $this->list = $medium->getList();
    }
}
