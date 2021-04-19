<?php


namespace Prokl\StaticPageMakerBundle\Tests;

use Local\Services\AppKernel;
use LogicException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Kernel;

/**
 * Class BundleInitializationTest
 * @package Prokl\StaticPageMakerBundle\Tests
 *
 * @since 24.01.2021
 */
class BundleInitializationTest extends TestCase
{
    /**
     * @var AppKernel
     */
    private $kernel;

    /**
     * Get a kernel which you may configure with your bundle and services.
     *
     * @return AppKernel
     */
    protected function createKernel(): AppKernel
    {
        if (!class_exists(Kernel::class)) {
            throw new LogicException('You must install symfony/symfony to run the bundle test.');
        }

        return container()->get('kernel');
    }

    /**
     * @return ContainerInterface
     */
    protected function getContainer(): ContainerInterface
    {
        return $this->kernel->getContainer();
    }

    /**
     * @return void
     */
    public function testInitBundle() : void
    {
        $this->kernel = $this->createKernel();

        $container = $this->getContainer();

        $this->assertTrue($container->has('static_page_maker.assets.handler'));
        $this->assertTrue($container->has('static_page_maker.bitrix.pieces'));
        $this->assertTrue($container->has('Prokl\StaticPageMakerBundle\Services\TemplateControllerContainerAware'));
        $this->assertTrue($container->has('Prokl\StaticPageMakerBundle\Services\ContextProcessors\SeoContextProcessor'));
    }
}
