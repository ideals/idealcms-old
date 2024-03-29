<?php
/**
 * Ideal CMS (http://idealcms.ru/)
 *
 * @link      http://github.com/ideals/idealcms репозиторий исходного кода
 * @copyright Copyright (c) 2012-2018 Ideal CMS (http://idealcms.ru)
 * @license   http://idealcms.ru/license.html LGPL v3
 */

namespace Ideal\Field\Password;

use Ideal\Core\Request;
use Ideal\Field\AbstractController;

/**
 * Поле для хранения и редактирования пароля
 *
 * Пароль хранится в зашифрованном с помощью функции password_hash() виде.
 *
 * Пример объявления в конфигурационном файле структуры:
 *     'password' => array(
 *         'label' => 'Пароль',
 *         'sql'   => 'varchar(255) NOT NULL',
 *         'type'  => 'Ideal_Password'
 *      ),
 */
class Controller extends AbstractController
{

    /** {@inheritdoc} */
    protected static $instance;

    /**
     * {@inheritdoc}
     */
    public function getInputText(): string
    {
        return '<script type="text/javascript" src="?mode=ajax&action=script&controller=\\Ideal\\Field\\Password" /><div class="row">'
        . '<div class="col-xs-3"><input type="password" class="form-control" id="' . $this->htmlName
        . '" name="' . $this->htmlName
        . '" ></div>'
        . '<div class="col-xs-1" style="width: 23px; padding: 2px 0 0 0; font-size: 24px;">'
        . '<i id="' . $this->htmlName . '-ico' . '" class="glyphicon">' . '</i></div>'
        . '<div class="col-xs-3">'
        . '<input type="password" class="form-control" id="' . $this->htmlName . '-check'
        . '" name="' . $this->htmlName . '-check'
        . '" ></div></div>';
    }

    /**
     * {@inheritdoc}
     */
    public function parseInputValue(bool $isCreate): array
    {
        $this->newValue = $this->pickupNewValue();

        $request = new Request();
        $fieldName = $this->groupName . '_' . $this->name . '-check';
        $newCheckValue = $request->$fieldName;

        $item = [];
        $item['fieldName'] = $this->htmlName;

        if ($this->newValue === '') {
            $item['value'] = null;
        } else {
            $item['value'] = password_hash($this->newValue, PASSWORD_DEFAULT);
        }

        $item['message'] = '';

        if ($this->newValue !== $newCheckValue) {
            $item['message'] = 'пароли не совпадают';
        }

        if (empty($this->newValue) && $isCreate) {
            // При создании элемента поле с паролем всегда должно быть заполнено
            $item['message'] = 'необходимо заполнить это поле';
        }

        $item['sqlAdd'] = $this->sqlAdd;

        return $item;
    }
}
