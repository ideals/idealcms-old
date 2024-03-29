<?php
/**
 * Ideal CMS (http://idealcms.ru/)
 *
 * @link      http://github.com/ideals/idealcms репозиторий исходного кода
 * @copyright Copyright (c) 2012-2018 Ideal CMS (http://idealcms.ru)
 * @license   http://idealcms.ru/license.html LGPL v3
 */

namespace Ideal\Structure\Part\Admin;

use Ideal\Core\Config;
use Ideal\Core\Db;
use Ideal\Core\Util;
use Ideal\Field\Cid;
use RuntimeException;

class ModelAbstract extends \Ideal\Core\Admin\Model
{
    public string $cid;

    public function delete(): bool
    {
        parent::delete();
        $lvl = $this->pageData['lvl'] + 1;
        $cid = new Cid\Model($this->params['levels'], $this->params['digits']);
        $cid = $cid->getCidByLevel($this->pageData['cid'], $this->pageData['lvl'], false);
        $_sql = "SELECT ID FROM $this->_table WHERE lvl=$lvl AND cid LIKE '$cid%'";
        $db = Db::getInstance();
        $res = $db->select($_sql);
        if (count($res) > 0) {
            // TODO сделать проверку успешности удаления
            throw new RuntimeException('Не могу удалить элемент, т.к. запросу соответствуют несколько элементов');
        }
        $db->delete($this->_table)->where('ID=:id', ['id' => $this->pageData['ID']]);
        $db->exec();

        return true;
    }

    /**
     * Определение пути по ID элементов пути
     *
     * @param array $path Начальная, уже найденная часть пути
     * @param       $par
     * @return $this
     * @noinspection MultipleReturnStatementsInspection
     */
    public function detectPageByIds($path, $par): ModelAbstract
    {
        /* @var Db $db */
        $db = Db::getInstance();

        if (count($par) === 0) {
            $this->path = $path;
            return $this;
        }

        $ids = implode(',', $par);
        $_sql = "SELECT * FROM $this->_table
                          WHERE ID IN ($ids) AND prev_structure='$this->prevStructure' ORDER BY cid";
        $result = $db->select($_sql);

        if (count($result) === 0) {
            // Обработка случая, когда ничего не нашлось — 404
            $this->is404 = true;
            return $this;
        }

        // Проверка найденных элементов из БД на соответствие последовательности ID в par
        // и последовательности cid адресов
        $cidModel = new Cid\Model($this->params['levels'], $this->params['digits']);
        $cidPrev = $cidModel->getBlock($cidModel->reconstruct('0'), 0); // в начале разбора параметров не существует сида
        $trueResult = [];
        $parElement = reset($par);
        foreach ($result as $v) {
            if ($v['ID'] !== $parElement) {
                // Если ID найденного элемента не соответствует ID в переданной строке par
                continue;
            }
            $cidCurr = $cidModel->getBlock($v['cid'], $v['lvl'] - 1); // находим блок cid предыдущего уровня
            if ($cidPrev !== $cidCurr) {
                // Если предыдущий блок cid не равен предыдущему блоку этого cid
                continue;
            }
            $trueResult[] = $v;
            $parElement = next($par);
            $cidPrev = $cidModel->getBlock($v['cid'], $v['lvl']); // запоминаем блок cid пройденного уровня
        }

        $par = array_slice($par, count($trueResult)); // отрезаем найденную часть пути от $par

        $this->path = array_merge($path, $trueResult);

        $config = Config::getInstance();
        if (count($par) !== 0) {
            // Ещё остались неопределённые элементы пути. Запускаем вложенную структуру.
            $trueResult = $this->path;
            $end = array_pop($trueResult);
            $prev = array_pop($trueResult);
            $structure = $config->getStructureByName($prev['structure']);
            $modelClassName = Util::getClassName($end['structure'], 'Structure') . '\\Admin\\Model';
            /* @var $structure Model */
            $structure = new $modelClassName($structure['ID'] . '-' . $end['ID']);
            if (get_class($structure) === get_class($this)) {
                // Получается, что вложенный элемент находится в той же структуре, поэтому 404
                $this->is404 = true;
                return $this;
            }

            // Запускаем определение пути и активного объекта по $par
            return $structure->detectPageByIds($this->path, $par);
        }

        return $this;
    }

    /**
     * Считываем наибольший cid на уровне $lvl для родительского $cid
     *
     * @param string $cid Родительский cid
     * @param int $lvl Уровень, на котором нужно получить макс. cid
     * @return string Максимальный cid на уровне $lvl
     */
    public function getNewCid(string $cid, int $lvl): string
    {
        /* @var Db $db */
        $db = Db::getInstance();

        $cidModel = new Cid\Model($this->params['levels'], $this->params['digits']);
        $parentCid = $cidModel->getCidByLevel($cid, $lvl - 1, false);
        $par = [
            'parentCid' => $parentCid . '%',
            'lvl' => $lvl,
        ];
        $_sql = "SELECT cid FROM $this->_table WHERE cid LIKE :parentCid AND lvl=:lvl ORDER BY cid DESC LIMIT 1";
        $cidArr = $db->select($_sql, $par);
        if (count($cidArr) > 0) {
            // Если элементы на этом уровне есть, берём cid последнего
            $cid = $cidArr[0]['cid'];
        }
        // Если элементов на этом уровне нет, берём id родителя

        // Прибавляем единицу в cid на нашем уровне
        return $cidModel->setBlock($cid, $lvl, '+1', true);
    }

    /**
     * Инициализирует переменную $pageData данными по умолчанию для нового элемента
     */
    public function setPageDataNew(): void
    {
        parent::setPageDataNew();
        $path = $this->getPath();
        $c = count($path);
        $end = end($path);
        if ($c === 1 || ($c > 1 && $end['structure'] !== $path[$c - 2]['structure'])) {
            $prevStructure = $this->prevStructure;
            $lvl = 1;
        } else {
            // Остаёмся в рамках того же модуля
            $lvl = $end['lvl'] + 1;
            $prevStructure = $end['prev_structure'];
        }
        $pageData['lvl'] = $lvl;
        $pageData['prev_structure'] = $prevStructure;
        $this->setPageData($pageData);
    }

    protected function getWhere(string $where): string
    {
        $path = $this->getPath();
        $c = count($path);
        $end = end($path);
        if ($c === 1 || ($c > 1 && $end['structure'] !== $path[$c - 2]['structure'])) {
            // Считываем все элементы первого уровня
            $where .= ' AND lvl=1';
        } else {
            // Считываем все элементы последнего уровня из пути
            $lvl = $end['lvl'] + 1;
            $cidModel = new Cid\Model($this->params['levels'], $this->params['digits']);
            $cid = $cidModel->getCidByLevel($end['cid'], $end['lvl'], false);
            $where .= "AND lvl=$lvl AND cid LIKE '$cid%'";
        }

        return parent::getWhere($where);
    }
}
