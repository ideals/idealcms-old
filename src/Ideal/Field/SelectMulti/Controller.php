<?php
/**
 * Ideal CMS (http://idealcms.ru/)
 *
 * @link      http://github.com/ideals/idealcms репозиторий исходного кода
 * @copyright Copyright (c) 2012-2018 Ideal CMS (http://idealcms.ru/)
 * @license   http://idealcms.ru/license.html LGPL v3
 */

namespace Ideal\Field\SelectMulti;

use Exception;
use Ideal\Field\AbstractController;
use Ideal\Medium\AbstractModel;

/**
 * Поле для выбора значения из списка вариантов
 *
 * Пример объявления в конфигурационном файле структуры:
 *
 *     'structure' => array(
 *         'label'  => 'Тип раздела',
 *         'sql'    => 'varchar(30) not null',
 *         'type'   => 'Ideal_SelectMulti',
 *         'medium' => '\\Ideal\\Medium\\StructureList\\Model'
 *     ),
 *
 * Полю medium присваивается название медиума для получения списка значений через промежуточную таблицу
 *
 * Для редактирования подключается jQuery-плагин bootstrap-multiselect.js
 *
 */
class Controller extends AbstractController
{

    /** @inheritdoc */
    protected static $instance;

    /** @var  AbstractModel Объект доступа к редактируемым данным */
    protected $medium;

    /**
     * {@inheritdoc}
     * @throws Exception
     */
    public function getInputText(): string
    {
        $list = $this->medium->getList();
        $variants = $this->medium->getValues();
        $html = <<<HTML
<link href="Ideal/Library/bootstrapMultiselect/dist/css/bootstrap-multiselect.css" rel="stylesheet">
<script type="text/javascript" src="Ideal/Library/bootstrapMultiselect/dist/js/bootstrap-multiselect.js"></script>
<!-- Initialize the bootstrap multiselect plugin: -->
<script type="text/javascript">
    $(document).ready(function() {
        $('#$this->htmlName').multiselect({
        'onDropdownShown':function() {
            const modalBody = $('.modal-body'); 
            modalBody.height(modalBody.height() + $('.multiselect-container.dropdown-menu').height())
        }
        });
    });
</script>
<style>
.btn-group{
width:100%;
}
.multiselect.dropdown-toggle{width: 100%;text-align: left}
.multiselect-container li {
padding: 0;
float: left;
}
</style>
HTML;
        $html .= '<select multiple="multiple" class="form-control" name="' . $this->htmlName
            . '[]" id="' . $this->htmlName . '">';
        foreach ($list as $k => $v) {
            $selected = '';
            if (in_array($k, $variants, true)) {
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
    public function parseInputValue(bool $isCreate): array
    {
        // При сохранении выбранных значений при использовании промежуточной таблицы,
        // потребуются дополнительные запросы к БД, которые генерирует медиум

        $this->newValue = null;
        $newValue = $this->pickupNewValue();

        return [
            'fieldName' => $this->htmlName,
            'value' => null,
            'message' => '',
            'sqlAdd' => $this->medium->getSqlAdd($newValue)
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function setModel($model, string $fieldName, string $groupName = 'general'): void
    {
        parent::setModel($model, $fieldName, $groupName);

        // Инициализируем медиум для получения списка значений select и сохранения данных
        $className = $this->field['medium'];
        $this->medium = new $className($this->model, $fieldName);
    }
}
