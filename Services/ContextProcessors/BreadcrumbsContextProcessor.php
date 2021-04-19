<?php

namespace Prokl\StaticPageMakerBundle\Services\ContextProcessors;

use Prokl\StaticPageMakerBundle\Services\AbstractContextProcessor;

/**
 * Class BreadcrumbsContextProcessor
 * @package Prokl\StaticPageMakerBundle\Services\ContextProcessors
 *
 * @since 19.04.2021
 */
class BreadcrumbsContextProcessor extends AbstractContextProcessor
{
    /**
     * @inheritDoc
     */
    public function handle() : array
    {
        $GLOBALS['APPLICATION']->AddChainItem(
            $this->context['title'],
            $this->context['url']
        );

        return $this->context;
    }
}
