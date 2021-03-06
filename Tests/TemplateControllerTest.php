<?php

namespace Prokl\StaticPageMakerBundle\Tests;

use Exception;
use Faker\Factory;
use Faker\Generator;
use Prokl\StaticPageMakerBundle\Services\TemplateController;
use LogicException;
use PHPUnit\Framework\TestCase;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Loader\FilesystemLoader;

/**
 * Class TemplateControllerTest
 * @package Prokl\StaticPageMakerBundle\Tests
 *
 * @since 25.01.2021
 */
class TemplateControllerTest extends TestCase
{
    /**
     * @var TemplateController $testObject
     */
    private $testObject;

    /**
     * @var Generator | null $faker
     */
    private $faker;

    /**
     * @throws Exception
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->faker = Factory::create();

        $loader = new FilesystemLoader(
            [__DIR__ . '/templates']
        );

        $twig = new Environment($loader);

        $this->testObject = new TemplateController(
            $twig
        );
    }

    /**
     *
     * @return void
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function testNoTwigPassed() : void
    {
        $this->testObject = new TemplateController();

        $this->expectException(LogicException::class);
        $this->testObject->templateAction(
          $this->faker->sentence,
        );
    }

    /**
     * Выставляет ли код ответа.
     *
     * @return void
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function testSetStatusCode() : void
    {
        $maxAge = $this->faker->numberBetween(200, 400);
        $sharedAge = $this->faker->numberBetween(200, 400);
        $private = true;

        $statusCode = 222;

        $result = $this->testObject->templateAction(
            './void.twig',
            $maxAge,
            $sharedAge,
            $private,
            [],
            $statusCode
        );

        $this->assertSame(
            $statusCode,
            $result->getStatusCode()
        );
    }

    /**
     * Выставляет ли код ответа по умолчанию.
     *
     * @return void
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function testSetDefaultStatusCode() : void
    {
        $maxAge = $this->faker->numberBetween(200, 400);
        $sharedAge = $this->faker->numberBetween(200, 400);
        $private = true;

        $result = $this->testObject->templateAction(
            './void.twig',
            $maxAge,
            $sharedAge,
            $private,
        );

        $this->assertSame(
            200,
            $result->getStatusCode()
        );
    }

    /**
     * Выставляет ли заголовки.
     *
     * @return void
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function testSetHeaders() : void
    {
        $maxAge = $this->faker->numberBetween(200, 400);
        $sharedAge = $this->faker->numberBetween(200, 400);
        $private = true;

        $result = $this->testObject->templateAction(
            './void.twig',
            $maxAge,
            $sharedAge,
            $private
        );

        $this->assertSame(
            (string)$maxAge,
            $result->headers->getCacheControlDirective('max-age')
        );

        $this->assertSame(
            (string)$sharedAge,
            $result->headers->getCacheControlDirective('s-maxage')
        );

        $this->assertTrue(
            $result->headers->getCacheControlDirective('private')
        );
    }
}
