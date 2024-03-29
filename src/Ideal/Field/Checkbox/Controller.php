<?php
/**
 * Ideal CMS (http://idealcms.ru/)
 *
 * @link      http://github.com/ideals/idealcms репозиторий исходного кода
 * @copyright Copyright (c) 2012-2018 Ideal CMS (http://idealcms.ru)
 * @license   http://idealcms.ru/license.html LGPL v3
 */

namespace Ideal\Field\Checkbox;

use Exception;
use Ideal\Core\Request;
use Ideal\Field\AbstractController;

/**
 * Отображение и редактирование поля, содержащего true/false (checkbox)
 *
 * Пример объявления в конфигурационном файле структуры:
 *     'is_active' => array(
 *         'label' => 'Отображать на сайте',
 *         'sql'   => 'bool',
 *         'type'  => 'Ideal_Checkbox'
 *     ),
 */
class Controller extends AbstractController
{

    /** {@inheritdoc} */
    protected static $instance;

    /**
     * Отображение html-элементов для редактирования этого поля
     *
     * @return string HTML-код группы редактирования для этого поля
     * @throws Exception
     */
    public function showEdit(): string
    {
        $input = $this->getInputText();
        return <<<HTML
        <div id="$this->htmlName-control-group" class="form-group checkbox">
            <div class="$this->inputClass $this->htmlName-controls">
                $input
                <div id="$this->htmlName-help"></div>
            </div>
        </div>
HTML;
    }

    /**
     * {@inheritdoc}
     * @throws Exception
     */
    public function getInputText(): string
    {
        $checked = ((int)$this->getValue() === 1) ? 'checked="checked"' : '';
        return '<label class="checkbox"><input type="checkbox" name="' . $this->htmlName
        . '" id="' . $this->htmlName . '" '
        . $checked . '> ' . $this->field['label'] . '</label>';
    }

    /**
     * {@inheritdoc}
     */
    public function getValueForList(array $values, string $fieldName): string
    {
        return ((int)$values[$fieldName] === 1) ? 'Да' : 'Нет';
    }

    /**
     * {@inheritdoc}
     */
    public function pickupNewValue(): string
    {
        $request = new Request();
        $fieldName = $this->groupName . '_' . $this->name;
        $this->newValue = ($request->$fieldName === '') ? 0 : 1;
        return $this->newValue;
    }
}
