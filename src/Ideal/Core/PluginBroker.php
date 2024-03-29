<?php
/**
 * Ideal CMS (http://idealcms.ru/)
 *
 * @link      http://github.com/ideals/idealcms репозиторий исходного кода
 * @copyright Copyright (c) 2012-2018 Ideal CMS (http://idealcms.ru)
 * @license   http://idealcms.ru/license.html LGPL v3
 */

namespace Ideal\Core;

class PluginBroker
{

    private static $instance;

    protected array $_events = [];

    public static function getInstance()
    {
        if (empty(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function makeEvent($eventName, $params)
    {
        if (count($this->_events) === 0 || !isset($this->_events[$eventName])) {
            return $params;
        }

        foreach ($this->_events[$eventName] as $event) {
            $plugin = new $event();
            $params = $plugin->$eventName($params);
        }
        return $params;
    }

    public function registerPlugin(string $eventName, string $pluginClassName): void
    {
        $this->_events[$eventName][] = $pluginClassName;
    }
}