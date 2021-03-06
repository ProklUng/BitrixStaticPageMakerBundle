<?php

namespace Prokl\StaticPageMakerBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Bitrix\Main\Page\Asset;

/**
 * Class AssetsExtension
 * @package Prokl\StaticPageMakerBundle\Twig
 *
 * @since 23.01.2021
 */
class AssetsExtension extends AbstractExtension
{
    /**
     * Return extension name
     *
     * @return string
     */
    public function getName(): string
    {
        return 'static_page_maker.assets_handler_extension';
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions() : array
    {
        return [
            new TwigFunction('add_css', [$this, 'addCss']),
            new TwigFunction('add_js', [$this, 'addJs']),
            new TwigFunction('add_string', [$this, 'addString']),
        ];
    }

    /**
     * Добавить CSS.
     *
     * @param string $path Путь.
     *
     * @return void
     */
    public function addCss(string $path) : void
    {
        Asset::getInstance()->addCss($path);
    }

    /**
     * Добавить JS.
     *
     * @param string $path Путь.
     *
     * @return void
     */
    public function addJs(string $path) : void
    {
        Asset::getInstance()->addJs($path);
    }

    /**
     * Добавить строку в header.
     *
     * @param string $value Строка.
     *
     * @return void
     */
    public function addString(string $value) : void
    {
        Asset::getInstance()->addString($value);
    }
}
