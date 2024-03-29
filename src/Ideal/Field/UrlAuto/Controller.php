<?php
/**
 * Ideal CMS (http://idealcms.ru/)
 *
 * @link      http://github.com/ideals/idealcms репозиторий исходного кода
 * @copyright Copyright (c) 2012-2018 Ideal CMS (http://idealcms.ru)
 * @license   http://idealcms.ru/license.html LGPL v3
 */

namespace Ideal\Field\UrlAuto;

use Ideal\Field\Url;

/**
 * Поле, содержащее финальный сегмент URL для редактируемого элемента
 *
 * Отличается от своего предка тем, что визуальная часть содержит кнопку для включения/отключения
 * автоматической генерации URL на основе названия элемента.
 *
 * Пример объявления в конфигурационном файле структуры:
 *
 *     'url' => array(
 *         'label' => 'URL',
 *         'sql'   => 'varchar(255) not null',
 *         'type'  => 'Ideal_UrlAuto',
 *         'nameField' => 'name' // имя поля по которому происходит генерация url
 *     ),
 */
class Controller extends Url\Controller
{

    /** @inheritdoc */
    protected static $instance;

    /**
     * {@inheritdoc}
     */
    public function getInputText(): string
    {
        $url = new Url\Model();
        $value = ['url' => htmlspecialchars($this->getValue())];
        $link = Url\Model::getUrlWithPrefix($value, $this->model->getParentUrl());
        $link = $url->cutSuffix($link);
        // Проверяем, является ли url этого объекта частью пути
        $addOn = '';
        if (($link !== '') && ($link[0] === '/') && ($value['url'] !== $link)) {
            // Выделяем из ссылки путь до этого объекта и выводим его перед полем input
            $path = substr($link, 0, strrpos($link, '/'));
            $addOn = '<span class="input-group-addon">' . $path . '/</span>';
        }
        $nameField = $this->field['nameField'] ?? 'name';
        return
            '<script type="text/javascript" src="?mode=ajax&action=script&controller=\Ideal\Field\UrlAuto" />'
            . '<div class="input-group">' . $addOn
            . '<input type="text" class="form-control" name="' . $this->htmlName . '" id="' . $this->htmlName
            . '" value="' . $value['url'] . '" data-field="' . $nameField . '"><span class="input-group-btn">'
            . '<button id="UrlAuto" type="button" class="btn btn-danger" onclick="setTranslit(this)">'
            . 'auto url off</button>'
            . '</span></div>';
    }
}
