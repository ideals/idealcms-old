<?php
/**
 * Ideal CMS (http://idealcms.ru/)
 *
 * @link      http://github.com/ideals/idealcms репозиторий исходного кода
 * @copyright Copyright (c) 2012-2018 Ideal CMS (http://idealcms.ru)
 * @license   http://idealcms.ru/license.html LGPL v3
 */

namespace Ideal\Structure\Part\Widget;

use Exception;
use Ideal\Core\Db;
use Ideal\Core\Config;
use Ideal\Core\Widget;
use Ideal\Field\Url\Model;

/**
 * Виджет, отображающий двухуровневое меню.
 * Пункты у которых нет второго уровня по умолчанию не попадают в список.
 *
 * Пример использования:
 *
 *     $menu2 = new Menu2($model);
 *     $menu2->setPrevStructure('0-1');
 *     $vars['secondMenu'] = $menu2->getData();
 */
class Menu2 extends Widget
{
    /** @var bool Флаг необходимости показа элементов меню не имеющих дочерних элементов */
    protected bool $showNoChildren = false;

    public function setShowNoChildren($showNoChildren): void
    {
        $this->showNoChildren = $showNoChildren;
    }

    /**
     * @throws Exception
     */
    public function getData(): array
    {
        // Определяем кол-во разрядов на один уровень cid
        $config = Config::getInstance();
        $category = $config->getStructureByName('Ideal_Part');
        $digits = $category['params']['digits'];
        $table = $config->db['prefix'] . 'ideal_structure_part';
        $prevStructure = empty($this->prevStructure) ? '' : "AND prev_structure='$this->prevStructure'";

        $db = Db::getInstance();
        $_sql = "SELECT * FROM $table
                    WHERE (lvl = 1 OR lvl = 2) AND is_active=1 $prevStructure AND is_not_menu=0 ORDER BY cid";
        $menuList = $db->select($_sql);

        // Раскладываем считанное меню во вложенные массивы по cid и lvl
        $num = 0;
        $menu = [];
        $parent = [];
        $url = new Model();
        foreach ($menuList as $v) {
            if ((int)$v['lvl'] === 1) {
                // Если нет второго уровня меню, то и первый не нужно выводить в список
                if (!$this->showNoChildren && isset($menu[$num]) && empty($menu[$num]['subMenu'])) {
                    unset($menu[$num]);
                }
                $num = substr($v['cid'], 0, $digits);
                $parent = $v;
                if (isset($v['url_full']) && strlen($v['url_full']) > 1) {
                    $v['link'] = 'href="' . $v['url_full'] . $config->urlSuffix . '"';
                } else {
                    $v['link'] = 'href="' . Model::getUrlWithPrefix($v, $this->prefix) . '"';
                }
                $v['subMenu'] = [];
                $menu[$num] = $v;
            }
            if ((int)$v['lvl'] === 2) {
                if (isset($v['url_full']) && strlen($v['url_full']) > 1) {
                    $v['link'] = 'href="' . $v['url_full'] . $config->urlSuffix . '"';
                } else {
                    $parentUrl = $url->setParentUrl(['0' => $parent]);
                    $prefix = $this->prefix . '/' . $parentUrl;
                    $v['link'] = 'href="' . Model::getUrlWithPrefix($v, $prefix) . '"';
                }
                $menu[$num]['subMenu'][] = $v;
            }
        }
        unset($menuList);

        // Определение активных пунктов меню
        $object = $this->model->getPageData();
        if (isset($object['prev_structure']) && $object['prev_structure'] === $this->prevStructure) {
            $activeUrl = substr($object['cid'], 0, $digits);
            if (!isset($menu[$activeUrl])) {
                return $menu;
            }
            $menu[$activeUrl]['activeUrl'] = 1;
            $menu[$activeUrl]['classActiveUrl'] = 'activeMenu';
            foreach ($menu[$activeUrl]['subMenu'] as $k => $elem) {
                $elem['cid'] = rtrim($elem['cid'], '0');
                $cid = substr($object['cid'], 0, strlen($elem['cid']));
                if ($elem['cid'] === $cid) {
                    $menu[$activeUrl]['subMenu'][$k]['activeUrl'] = 1;
                }
            }
        }

        return $menu;
    }
}
