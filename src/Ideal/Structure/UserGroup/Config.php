<?php
/**
 * Ideal CMS (http://idealcms.ru/)
 *
 * @link      http://github.com/ideals/idealcms репозиторий исходного кода
 * @copyright Copyright (c) 2012-2018 Ideal CMS (http://idealcms.ru)
 * @license   http://idealcms.ru/license.html LGPL v3
 */

namespace Ideal\Structure\UserGroup;

// Справочник "Группа пользователей"
class Config
{
    public static array $params = [
        'in_structures' => ['Ideal_DataList'], // в каких структурах можно создавать эту структуру
        'elements_cms' => 10, // количество элементов в списке в CMS
        'elements_site' => 15, // количество элементов в списке на сайте
        'field_name' => '', // поле для входа в список потомков
        'field_sort' => 'name', // поле, по которому проводится сортировка в CMS по умолчанию
        'field_list' => ['name'],
    ];

    public static array $fields = [
        'ID' => [
            'label' => 'Идентификатор',
            'sql' => 'int(4) unsigned not null auto_increment primary key',
            'type' => 'Ideal_Hidden'
        ],
        'prev_structure' => [
            'label' => 'ID родительских структур',
            'sql' => 'char(15)',
            'type' => 'Ideal_Hidden'
        ],
        'name' => [
            'label' => 'Название',
            'sql' => 'text not null',
            'type' => 'Ideal_Text'
        ],
    ];
}
