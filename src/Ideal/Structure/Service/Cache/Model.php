<?php
/**
 * Ideal CMS (http://idealcms.ru/)
 *
 * @link      http://github.com/ideals/idealcms репозиторий исходного кода
 * @copyright Copyright (c) 2012-2018 Ideal CMS (http://idealcms.ru)
 * @license   http://idealcms.ru/license.html LGPL v3
 */

namespace Ideal\Structure\Service\Cache;

use Ideal\Core\FileCache;
use Ideal\Core\View;
use Ideal\Structure\Service\SiteData\ConfigPhp;
use JsonException;

/**
 * Класс для обработки кэша
 *
 * Реакция на изменения настроек файлового кэширования, кэширование twig-шаблонов и кэширование запросов к бд
 */
class Model
{

    protected ConfigPhp $configFileClass;

    /**
     * При инициализации модели сохраняем класс ConfigPhp в отдельную переменную
     *
     * @param ConfigPhp $configFileClass Экземпляр класса "ConfigPhp"
     */
    public function __construct(ConfigPhp $configFileClass)
    {
        $this->configFileClass = $configFileClass;
    }

    /**
     * Отвечает за реакции системы на изменения настроек файлового кэширования,
     * кэширования twig-шаблонов и кэширования запросов к бд
     *
     * @return array Массив содержащий флаг успешности проверки настроек,
     *               а так же текст и набор классов в случае обнаружения ошибок
     * @throws JsonException
     */
    public function checkSettings(): array
    {
        $response = ['res' => true, 'text' => '', 'class' => ''];
        $oldParams = $this->configFileClass->getParams();
        $responseGV = $this->configFileClass->pickupValues();
        if ($responseGV['res'] !== false) {
            $params = $this->configFileClass->getParams();
            // Запускаем очистку кэша если он отключен
            if (!$params['cache']['arr']['fileCache']['value']) {
                FileCache::clearFileCache();
            }

            // Перезаписываем данные в исключениях кэша
            $responseCEP = self::cacheExcludeProcessing($params['cache']['arr']['excludeFileCache']['value']);
            /** @noinspection PhpStrictComparisonWithOperandsOfDifferentTypesInspection */
            if ($responseCEP['res'] === false) {
                $response['res'] = false;
                $response['text'] = $responseCEP['text'];
                $response['class'] = 'alert alert-danger';
            }

            // Запускаем очистку кэша twig-шаблонов
            $siteTwigCache = $params['cache']['arr']['templateSite']['value'];
            $siteTwigCacheOld = $oldParams['cache']['arr']['templateSite']['value'];
            $adminTwigCache = $params['cache']['arr']['templateAdmin']['value'];
            $adminTwigCacheOld = $oldParams['cache']['arr']['templateAdmin']['value'];
            if ((($siteTwigCache !== $siteTwigCacheOld) && !$siteTwigCache)
                || (($adminTwigCache !== $adminTwigCacheOld) && !$adminTwigCache)) {
                View::clearTwigCache();
            }

            // Сбрасываем минифицированные js и css-файлы
            $jsFile = DOCUMENT_ROOT . '/js/all.min.js';
            if (file_exists($jsFile)) {
                unlink($jsFile);
            }
            $cssFile = DOCUMENT_ROOT . '/css/all.min.css';
            if (file_exists($cssFile)) {
                unlink($cssFile);
            }

            // Перед включением "кэширования запросов к БД" проверяем доступность класса "Memcache"
            if ($params['cache']['arr']['memcache']['value'] && !class_exists('Memcache')) {
                $response['res'] = false;
                $response['text'] = 'Класс "Memcache" не доступен. Кэширование запросов к БД не может быть включено!';
                $response['class'] = 'alert alert-danger';
            }
        } else {
            $response['res'] = false;
            $response['text'] = $responseGV['text'];
            $response['class'] = 'alert alert-danger';
        }

        return $response;
    }

    /**
     * Обрабатывает список исключений из настроек кэша
     *
     * @param string $string Значение поля "Адреса для исключения из кэша"
     *
     * @return array Массив содержащий флаг успешности проверки настроек, а так же текст в случае обнаружения ошибок
     * @throws JsonException
     */
    private static function cacheExcludeProcessing(string $string): array
    {
        $response = ['res' => true];

        // Экранируем переводы строки для обработки каждой строки
        $string = str_replace("\r", '', $string);
        $lines = explode("\n", $string);

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) {
                // Пропускаем пустые линии в списке исключений из кэша
                continue;
            }
            if (!FileCache::addExcludeFileCache($line)) {
                $response['res'] = false;
                $response['text'] = 'Не получилось сохранить настройки исключений в файл';
            }
        }

        return $response;
    }
}
