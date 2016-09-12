<?php

namespace tests\Nicofuma\SwaggerBundle\FosRest;

use FOS\RestBundle\Request\ParamFetcherInterface;
use FR3D\SwaggerAssertions\SchemaManager;
use JsonSchema\Constraints\Factory;
use Nicofuma\SwaggerBundle\Exception\NoValidatorException;
use Nicofuma\SwaggerBundle\FosRest\ParamFetcher;
use Nicofuma\SwaggerBundle\Validator\Validator;
use Nicofuma\SwaggerBundle\Validator\ValidatorMap;
use Prophecy\Argument;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use tests\Nicofuma\SwaggerBundle\SwaggerTestCase;

/**
 * @covers \Nicofuma\SwaggerBundle\FosRest\ParamFetcher
 */
class ParamFetcherTest extends SwaggerTestCase
{
    /**
     * @var SchemaManager
     */
    protected $schemaManager;

    protected function setUp()
    {
        $this->schemaManager = SchemaManager::fromUri('file://'.__DIR__.'/../fixtures/swagger.json');
    }

    public function testNoSwagger()
    {
        $fosParamFetcher = $this->prophesize(ParamFetcherInterface::class);
        $fosParamFetcher->setController(Argument::any())->shouldBeCalled();
        $fosParamFetcher->all(true)->shouldBeCalled();
        $fosParamFetcher->get('param', true)->shouldBeCalled();

        $request = new Request();
        $requestStack = $this->prophesize(RequestStack::class);
        $requestStack->getCurrentRequest()->willreturn($request);
        $requestStack->getMasterRequest()->willreturn($request);

        $map = $this->prophesize(ValidatorMap::class);
        $map->getValidator($request)->will(function () {
            throw new NoValidatorException();
        });

        $paramFetcher = new ParamFetcher($fosParamFetcher->reveal(), $map->reveal(), $requestStack->reveal());
        $paramFetcher->setController(function () {
        });
        $paramFetcher->get('param', true);
        $paramFetcher->all(true);
    }

    public function dataGet()
    {
        yield [10, 'count', []];
        yield ['1', 'count', ['count' => '1']];
        yield [null, 'other', []];
    }

    /**
     * @dataProvider dataGet
     */
    public function testGet($expected, $parameter, $query)
    {
        $paramFetcher = $this->createParamFetcher('GET', '/api/v1/users', $query);
        $paramFetcher->setController(function () {
        });
        $result = $paramFetcher->get($parameter);

        self::assertSame($expected, $result);
    }

    public function testGetMissingParameter()
    {
        $this->expectException(\InvalidArgumentException::class);

        $paramFetcher = $this->createParamFetcher('GET', '/api/v1/users', []);
        $paramFetcher->setController(function () {
        });
        $paramFetcher->get('none');
    }

    public function testAll()
    {
        $paramFetcher = $this->createParamFetcher('GET', '/api/v1/users', ['foo' => 'bar', 'start_index' => 4]);
        $paramFetcher->setController(function () {
        });
        $result = $paramFetcher->all();

        $expected = [
            'count' => 10,
            'start_index' => 4,
            'other' => null,
        ];

        sort($result);
        sort($expected);

        static::assertSame($expected, $result);
    }

    private function createParamFetcher($method, $path, $query)
    {
        $fosParamFetcher = $this->prophesize(ParamFetcherInterface::class);
        $fosParamFetcher->get(Argument::any(), Argument::any())->shouldNotBeCalled();

        $request = $this->createMockRequest($method, $path, [], '', $query);
        $requestStack = $this->prophesize(RequestStack::class);
        $requestStack->getCurrentRequest()->willreturn($request);
        $requestStack->getMasterRequest()->willreturn($request);

        $validator = new Validator(new Factory(), $this->schemaManager, true);
        $map = $this->prophesize(ValidatorMap::class);
        $map->getValidator($request)->willReturn($validator);

        return new ParamFetcher($fosParamFetcher->reveal(), $map->reveal(), $requestStack->reveal());
    }
}
