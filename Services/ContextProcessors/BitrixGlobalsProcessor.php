<?php

namespace Prokl\StaticPageMakerBundle\Services\ContextProcessors;

use Prokl\StaticPageMakerBundle\Services\AbstractContextProcessor;
use RuntimeException;

/**
 * Class BitrixGlobalsProcessor
 * @package Prokl\StaticPageMakerBundle\Services\ContextProcessors
 *
 * @since 12.08.2021
 */
class BitrixGlobalsProcessor extends AbstractContextProcessor
{
    /**
     * @inheritDoc
     */
    public function handle() : array
    {
        global $APPLICATION;
        global $USER;

        $this->context['APPLICATION'] = $APPLICATION;
        $this->context['USER'] = $USER;

        return $this->context;
    }
}
