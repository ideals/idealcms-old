<?php
/**
 * Ideal CMS (http://idealcms.ru/)
 *
 * @link      http://github.com/ideals/idealcms репозиторий исходного кода
 * @copyright Copyright (c) 2012-2018 Ideal CMS (http://idealcms.ru)
 * @license   http://idealcms.ru/license.html LGPL v3
 */

namespace Ideal\Field\Integer;

use Exception;
use Ideal\Field\AbstractController;

/**
 * Поле, которое может содержать только числовое значение типа Integer
 *
 * Пример объявления в конфигурационном файле структуры:
 *     'count' => array(
 *         'label' => 'Количество',
 *         'sql'   => 'int',
 *         'type'  => 'Ideal_Integer'
 *     ),
 */
class Controller extends AbstractController
{

    /** {@inheritdoc} */
    protected static $instance;

    /**
     * {@inheritdoc}
     * @throws Exception
     */
    public function getInputText(): string
    {
        $value = (int)$this->getValue();
        return
            '<input type="number" class="form-control" name="' . $this->htmlName
            . '" id="' . $this->htmlName
            . '" value="' . $value . '">';
    }

    /**
     * {@inheritdoc}
     */
    public function getValue(): string
    {
        return (int)parent::getValue();
    }

    /**
     * {@inheritdoc}
     */
    public function pickupNewValue(): string
    {
        return (int)parent::pickupNewValue();
    }
}
