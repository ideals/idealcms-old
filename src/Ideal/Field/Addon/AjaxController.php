<?php
/**
 * Ideal CMS (http://idealcms.ru/)
 *
 * @link      http://github.com/ideals/idealcms репозиторий исходного кода
 * @copyright Copyright (c) 2012-2014 Ideal CMS (http://idealcms.ru/)
 * @license   http://idealcms.ru/license.html LGPL v3
 */

namespace Ideal\Field\Addon;

use Ideal\Core\Request;
use JsonException;

/**
 * Класс AjaxController отвечает за операции по редактированию списка подключённых аддонов
 *
 */
class AjaxController extends \Ideal\Core\Admin\AjaxController
{
    /**
     * Добавление аддона к списку
     * @throws JsonException
     */
    public function addAction()
    {
        $request = new Request();

        if ($request->id == 0) {
            // Если аддон подключается к ещё несозданному элементу, то данные модели из БД взять не получится
            $this->model->setPageData([]);
        } else {
            $this->model->setPageDataById($request->id);
        }

        $addonModel = new Model();
        $field = substr($request->addonField, strlen($request->groupName) + 1);
        $addonModel->setModel($this->model, $field, $request->groupName);

        // Получаем html-код новой вкладки, её заголовок и название
        $result = $addonModel->getTab($request->newId, $request->addonName);

        // Возвращаем информацию только о новом подключенном аддоне
        $json = [];
        $json[] = [$request->newId, $request->addonName, $result['name']];

        $options = (defined('JSON_UNESCAPED_UNICODE')) ? JSON_UNESCAPED_UNICODE : 0;
        $result['list'] = json_encode($json, JSON_THROW_ON_ERROR | $options);

        return json_encode($result, JSON_THROW_ON_ERROR | $options);
    }

    /**
     * @return string
     */
    public function scriptAction(): string
    {
        $this->setContentType('application/javascript');

        return (string)file_get_contents(__DIR__ . '/script.js');
    }
}
