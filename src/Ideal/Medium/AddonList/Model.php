<?php
/**
 * Ideal CMS (http://idealcms.ru/)
 *
 * @link      http://github.com/ideals/idealcms репозиторий исходного кода
 * @copyright Copyright (c) 2012-2014 Ideal CMS (http://idealcms.ru)
 * @license   http://idealcms.ru/license.html LGPL v3
 */
namespace Ideal\Medium\AddonList;

use Ideal\Core\Util;
use Ideal\Medium\AbstractModel;

/**
 * Медиум для получения списка шаблонов, которые можно создавать для структуры $obj
 */
class Model extends AbstractModel
{
    /**
     * {@inheritdoc}
     */
    public function getList(): array
    {
        $addons = $this->obj->fields[$this->fieldName]['available'];
        $list = [];
        foreach ($addons as $addon) {
            $class = Util::getClassName($addon, 'Addon') . '\\Config';
            /** @noinspection PhpUndefinedVariableInspection */
            $list[$addon] = $class::$params['name'];
        }
        return $list;
    }
}
