<?php
/**
 * Ideal CMS (http://idealcms.ru/)
 *
 * @link      http://github.com/ideals/idealcms репозиторий исходного кода
 * @copyright Copyright (c) 2012-2018 Ideal CMS (http://idealcms.ru/)
 * @license   http://idealcms.ru/license.html LGPL v3
 */

namespace Ideal\Field;

use Exception;
use Ideal\Core\Admin\Model;
use Ideal\Core\Request;

/**
 * Абстрактный класс, реализующий большинство необходимых методов для
 * отображения полей редактирования и сохранения данных из них
 */
abstract class AbstractController
{

    /** @var  mixed Хранит в себе копию соответствующего объекта поля (паттерн singleton) */
    protected static $instance;

    /** @var string Название поля, используемое для полей ввода в html-коде */
    public string $htmlName;

    /** @var string CSS-класс для определения ширины поля ввода */
    public string $inputClass = 'col-xs-9';

    /** @var string CSS-класс для определения ширины поля для подписи к полю ввода */
    public string $labelClass = 'col-xs-3';

    /** @var string Новое значение поля, полученное от пользователя (при редактировании в браузере) */
    public string $newValue;

    /** @var array Параметры поля, взятые из конфигурационного файла структуры */
    protected array $field;

    /** @var string Название вкладки, в которой находится поле в окне редактирования */
    protected string $groupName;

    /** @var Model Модель данных, в которой находится редактируемое поле */
    protected $model;

    /** @var string Название поля */
    protected string $name;

    /** @var string Дополнительный sql-код, генерируемый полем для сохранения всех своих данных */
    protected string $sqlAdd = '';

    /**
     * Обеспечение паттерна singleton
     *
     * Особенность — во всех потомках нужно обязательно определять свойство
     * protected static $instance
     *
     * @return mixed
     */
    public static function getInstance()
    {
        // PHP53 Late static binding
        if (empty(static::$instance)) {
            $className = static::class;
            static::$instance = new $className();
        }
        return static::$instance;
    }

    /**
     * Определение значения этого поля на основании данных из модели
     *
     * В случае, если в модели ещё нет данных, то значение берётся из поля default
     * в настройках структуры (fields) для соответствующего поля
     *
     * @return string
     * @throws Exception
     */
    public function getValue(): string
    {
        // TODO сделать определение значения по умолчанию, если для этого указан геттер
        //      и убрать этот функционал из selectField
        $value = '';
        $pageData = $this->model->getPageData();
        if (isset($pageData[$this->name]) && $pageData[$this->name] !== null) {
            $value = $pageData[$this->name];
        } elseif (isset($this->field['default'])) {
            // Если поле ещё не заполнено, берём его значение по умолчанию из описания полей структуры
            $value = $this->field['default'];
        }
        return $value;
    }

    /**
     * Форматирование значения поля для отображения значения в списке элементов
     *
     * @param array $values Массив значений объекта
     * @param string $fieldName Название поля, из которого надо взять значение
     * @return string Строка со значением для отображения в списке
     */
    public function getValueForList(array $values, string $fieldName): string
    {
        return $values[$fieldName] ?? '';
    }

    /**
     * Получение данных, введённых пользователем, их обработка с уведомлением об ошибках ввода
     *
     * @param bool $isCreate Флаг создания или редактирования элемента
     * @return array
     * @noinspection MultipleReturnStatementsInspection
     */
    public function parseInputValue(bool $isCreate): array
    {
        $this->newValue = $this->pickupNewValue();

        $item = [
            'fieldName' => $this->htmlName,
            'value' => $this->newValue,
            'message' => '',
            'sqlAdd' => ''
        ];

        // В первой версии только на правильность данных и их наличие, если в описании бд указано not null
        if (($this->name === 'ID') && $isCreate) {
            if (!empty($this->newValue)) {
                $item['message'] = 'При создании элемента поле ID не может быть заполнено';
            }
            return $item;
        }

        if (($this->name === 'ID') && empty($this->newValue)) {
            // Если это поле ID и оно пустое, значит элемент создаётся — не нужно на нём ошибку отображать
            return $item;
        }

        // Поле sql должно быть всегда, поэтому если тут генерируется Notice,
        // значит неправильно сформировано поле редактирования
        $sql = strtolower($this->field['sql']);

        if (($this->newValue === '') && (strpos($sql, 'not null') !== false) && (strpos($sql, 'default') === false)) {
            // Установлен NOT NULL и нет DEFAULT и $value пустое
            $item['message'] = 'необходимо заполнить это поле';
        }

        $item['sqlAdd'] = $this->sqlAdd;

        return $item;
    }

    /**
     * Получение нового значения поля из данных, введённых пользователем
     *
     * @return string
     */
    public function pickupNewValue(): string
    {
        $request = new Request();
        $fieldName = $this->groupName . '_' . $this->name;
        $this->newValue = $request->$fieldName;
        return $this->newValue;
    }

    /**
     * Установка модели редактируемого объекта, частью которого является редактируемое поле
     *
     * Полю необходимо получать сведения о состоянии объекта и о других полях, т.к.
     * его значения и поведение может зависеть от значений других полей
     *
     * @param Model $model Модель редактируемого объекта
     * @param string $fieldName Редактируемое поле
     * @param string $groupName Вкладка, к которой принадлежит редактируемое поле
     */
    public function setModel($model, string $fieldName, string $groupName = 'general'): void
    {
        $this->name = $fieldName;
        $this->model = $model;
        $this->field = $model->fields[$fieldName];
        $this->groupName = $groupName;
        $this->htmlName = $this->groupName . '_' . $this->name;
    }

    /**
     * Отображение html-элементов для редактирования этого поля
     *
     * @return string HTML-код группы редактирования для этого поля
     */
    public function showEdit(): string
    {
        $label = $this->getLabelText();
        $input = $this->getInputText();
        return <<<HTML
        <div id="$this->htmlName-control-group" class="form-group">
            <label class="$this->labelClass control-label" for="$this->htmlName">$label</label>
            <div class="$this->inputClass $this->htmlName-controls">
                $input
                <div id="$this->htmlName-help"></div>
            </div>
        </div>
HTML;
    }

    /**
     * Получение текста, подписывающего это поле ввода (тег label)
     *
     * @return string Строка содержащая текст подписи
     */
    public function getLabelText(): string
    {
        return $this->field['label'] . ':';
    }

    /**
     * Возвращает строку, содержащую html-код элементов ввода для редактирования поля
     *
     * @return string Html-код элементов ввода
     */
    abstract public function getInputText(): string;
}
