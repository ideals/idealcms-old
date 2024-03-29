<?php
/**
 * Ideal CMS (http://idealcms.ru/)
 *
 * @link      http://github.com/ideals/idealcms репозиторий исходного кода
 * @copyright Copyright (c) 2012-2018 Ideal CMS (http://idealcms.ru)
 * @license   http://idealcms.ru/license.html LGPL v3
 */

namespace Ideal\Structure\Service\Admin;

use Ideal\Core\Config;

/**
 * Класс для построения бокового меню в разделе Сервис и запуска скриптов выбранного пункта
 */
class ModelAbstract extends \Ideal\Core\Admin\Model
{
    /** @var array Массив с пунктами бокового меню */
    protected array $menu = [];

    /**
     * {@inheritdoc}
     */
    public function detectPageByIds($path, $par)
    {
        $menu = $this->getMenu();
        // Если par не указан, то активен первый пункт бокового меню
        $item = reset($menu);

        $first = reset($par);
        if ($first) {
            // Если $par указан, то находим активный пункт бокового меню
            foreach ($menu as $item) {
                if ($item['ID'] === $first) {
                    break;
                }
            }
        }

        $this->setPageData($item);
        $path[] = $item;
        $this->path = $path;

        return $this;
    }

    /**
     * Получение списка пунктов бокового меню
     *
     * @return array Массив с пунктами бокового меню
     */
    public function getMenu(): array
    {
        if (count($this->menu) > 0) {
            return $this->menu;
        }

        $config = Config::getInstance();

        // Считываем конфиги из папки Ideal/Service и Custom/Service
        $actions = $config->services;

        // Сортируем экшены по полю pos
        usort(
            $actions,
            static function ($a, $b) {
                return ($a['pos'] - $b['pos']);
            }
        );

        $this->menu = $actions;

        return $actions;
    }

    /**
     * Получение пунктов бокового меню на основе содержимого папок Structure\Service
     *
     * @param string $folder Путь к папке в которой ищем вложенные папки с экшенами пункта Сервис
     * @return array Массив с пунктами бокового меню
     */
    protected function getActions(string $folder): array
    {
        $config = Config::getInstance();
        $actions = [];
        $dir = stream_resolve_include_path($config->cmsFolder . '/' . $folder);
        if ($handle = opendir($dir)) {
            while (($file = readdir($handle)) !== false) {
                if ($file === '.' || $file === '..' || $file === 'Admin') {
                    continue;
                }
                if (!is_dir($dir . '/' . $file)) {
                    continue;
                } // пропускаем файлы, работаем только с папками

                $file = $dir . '/' . $file . '/config.php';
                if (!file_exists($file)) {
                    // Если конфигурационного файла нет, то никакого пункта в меню Сервис не добавляем
                    continue;
                }
                /** @noinspection UsingInclusionReturnValueInspection */
                $action = include($file);
                $actions[$action['ID']] = $action;
            }
        }
        return $actions;
    }

    /**
     * Получение пунктов бокового меню из подключенных модулей
     *
     * @param string $folder Путь к папке в которой ищем вложенные папки с экшенами пункта Сервис
     * @return array Массив с пунктами бокового меню
     */
    protected function getModulesActions(string $folder): array
    {
        $config = Config::getInstance();
        $actions = [];
        $dir = stream_resolve_include_path($config->cmsFolder . '/' . $folder);
        if ($handle = opendir($dir)) {
            while (($file = readdir($handle)) !== false) {
                if ($file === '.' || $file === '..' || $file === '.hg') {
                    continue;
                }
                if (!is_dir($dir . '/' . $file)) {
                    continue;
                } // пропускаем файлы, работаем только с папками

                $actionList = $this->getActions($folder . '/' . $file . '/Structure/Service');
                foreach ($actionList as $item) {
                    $actions[] = $item;
                }
            }
        }

        return $actions;
    }
}
