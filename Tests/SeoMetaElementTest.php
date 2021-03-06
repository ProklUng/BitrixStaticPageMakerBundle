<?php

namespace Prokl\StaticPageMakerBundle\Tests;

use CIBlockElement;
use CIBlockResult;
use Faker\Factory;
use Faker\Generator;
use Prokl\StaticPageMakerBundle\Services\Bitrix\ElementValuesProxy;
use Prokl\StaticPageMakerBundle\Services\Bitrix\SeoMetaElement;
use Mockery;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * Class SeoMetaElementTest
 * @package Prokl\StaticPageMakerBundle\Tests
 *
 * @since 24.01.2021
 */
class SeoMetaElementTest extends TestCase
{
    /**
     * @var SeoMetaElement $testObject
     */
    private $testObject;

    /**
     * @var Generator | null $faker
     */
    protected $faker;

    protected function setUp(): void
    {
        Mockery::resetContainer();
        parent::setUp();

        $this->faker = Factory::create();
    }

    /**
     * @inheritDoc
     */
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    /**
     * data(). URL not found.
     *
     * @return void
     */
    public function testData() : void
    {
        $this->testObject = new SeoMetaElement(
            $this->getMockCIblockElement([], 'GetList', 'Fetch'),
            $this->getMockElementValues(
                []
            ),
            $this->faker->numberBetween(10, 200)
        );

        $this->expectException(RuntimeException::class);
        $this->testObject->data($this->faker->url);
    }

    /**
     * title(). Found.
     *
     * @return void
     */
    public function testTitle() : void
    {
        $title = $this->faker->sentence(10);
        $idElement = $this->faker->numberBetween(10, 200);

        $this->testObject = new SeoMetaElement(
            $this->getMockCIblockElement(['ID' => $idElement], 'GetList', 'Fetch'),
            $this->getMockElementValues(
                [
                    'ELEMENT_META_TITLE' => ['VALUE' => $title]
                ]
            ),
            $this->faker->numberBetween(10, 200)
        );

        $result = $this->testObject->data($this->faker->url)->title();

        $this->assertSame(
            $title,
            $result,
            '???????????? ???????????????? title.'
        );
    }

    /**
     * title(). Not found.
     *
     * @return void
     */
    public function testTitleNotFound() : void
    {
        $idElement = $this->faker->numberBetween(10, 200);

        $this->testObject = new SeoMetaElement(
            $this->getMockCIblockElement(['ID' => $idElement], 'GetList', 'Fetch'),
            $this->getMockElementValues(
                [
                    'ELEMENT_META_TITLE' => ['VALUE' => null]
                ]
            ),
            $this->faker->numberBetween(10, 200)
        );

        $result = $this->testObject->data($this->faker->url)->title();

        $this->assertEmpty(
            $result,
            '???????????? ???????????????? title.'
        );
    }

    /**
     * description(). Found.
     *
     * @return void
     */
    public function testDescription() : void
    {
        $description = $this->faker->sentence(10);
        $idElement = $this->faker->numberBetween(10, 200);

        $this->testObject = new SeoMetaElement(
            $this->getMockCIblockElement(['ID' => $idElement], 'GetList', 'Fetch'),
            $this->getMockElementValues(
                [
                    'ELEMENT_META_DESCRIPTION' => ['VALUE' => $description]
                ]
            ),
            $this->faker->numberBetween(10, 200)
        );

        $result = $this->testObject->data($this->faker->url)->description();

        $this->assertSame(
            $description,
            $result,
            '???????????? ???????????????? description.'
        );
    }

    /**
     * description(). Not found.
     *
     * @return void
     */
    public function testDescriptionNotFound() : void
    {
        $idElement = $this->faker->numberBetween(10, 200);

        $this->testObject = new SeoMetaElement(
            $this->getMockCIblockElement(['ID' => $idElement], 'GetList', 'Fetch'),
            $this->getMockElementValues(
                [
                    'ELEMENT_META_DESCRIPTION' => ['VALUE' => null]
                ]
            ),
            $this->faker->numberBetween(10, 200)
        );

        $result = $this->testObject->data($this->faker->url)->description();

        $this->assertEmpty(
            $result,
            '???????????? ???????????????? description.'
        );
    }

    /**
     * ?????? CIBlockElement.
     *
     * @param string $method
     * @param string $methodRetrieveData
     * @param array  $fixture
     *
     * @return mixed
     */
    private function getMockCIblockElement(
        array $fixture = [],
        string $method = 'GetList',
        string $methodRetrieveData = 'GetNext'
    ) {
        $resultQuery = $this->getMockCIBlockResult($methodRetrieveData, $fixture);

        return Mockery::mock(CIBlockElement::class)
            ->makePartial()
            ->shouldReceive($method)
            ->andReturn(
                $resultQuery
            )
            ->getMock();
    }

    /**
     * ?????? ElementValuesProxy.
     *
     * @param mixed $returnValue
     *
     * @return mixed
     */
    private function getMockElementValues($returnValue)
    {
        return Mockery::mock(ElementValuesProxy::class)
            ->makePartial()
            ->shouldReceive('queryValues')
            ->andReturn(
                $returnValue
            )
            ->getMock();
    }

    /**
     * ?????? CIBlockResult. ?????????? CIBlockElement -> GetList.
     *
     * @param string $method
     * @param array  $fixture
     *
     * @return mixed
     */
    private function getMockCIBlockResult(
        string $method = 'GetNext',
        array $fixture = []
    ) {
        if ($method === 'GetNextElement') {
            return $this->getMockCIblockElement($fixture);
        }

        static $count = 0;

        /**
         * ?????????????? ?????????????? ????????????????, ?????????????? ???????? ?? ????????????????.
         * ?????????????????? ???????????? ?????????????????? false ?????? null, ??????????
         * ????????????????????????.
         */
        $mock =  Mockery::mock(
            CIBlockResult::class
        )
            ->shouldReceive($method)
            ->andReturnUsing(function () use ($fixture, &$count) {
                if ($count >= count($fixture)) {
                    return false;
                }

                $count++;

                return !empty($fixture[$count]) ? $fixture[$count] : $fixture;
            });

        $count = 0;

        return $mock->getMock();
    }
}
