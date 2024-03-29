<?php
/**
 * Ideal CMS (http://idealcms.ru/)
 * @link      http://github.com/ideals/idealcms репозиторий исходного кода
 * @copyright Copyright (c) 2012-2018 Ideal CMS (http://idealcms.ru)
 * @license   http://idealcms.ru/license.html LGPL v3
 */

namespace Ideal\Structure\Tag\Widget;

use Ideal\Core\Config;
use Ideal\Core\Db;
use Ideal\Core\Widget;

class Tags extends Widget
{

    public function getData(): array
    {
        return [];
    }

    public static function getTags($id): array
    {
        $db = Db::getInstance();
        $config = Config::getInstance();
        $table = $config->db['prefix'] . 'ideal_structure_tag';
        $tableList = $config->db['prefix'] . 'ideal_medium_taglist';
        $sql = "SELECT tags.* FROM $table AS tags
                  INNER JOIN $tableList AS tag ON (tag.tag_id = tags.ID)
                  WHERE tag.part_id=$id";
        return $db->select($sql);
    }
}
