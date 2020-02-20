<?php

namespace JairForo\VATChecker\Tests\Unit;

use JairForo\VATChecker\Api\FakeGateway;
use JairForo\VATChecker\Api\ViesGateway;
use JairForo\VATChecker\VATChecker;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionException;

class VATCheckerTest extends TestCase
{
    /**
     * @test
     * @throws ReflectionException
     */
    public function should_be_able_to_swap_api_gateways()
    {
        $checker = new VATChecker();
        $reflector = new ReflectionClass($checker);
        $reflector_property = $reflector->getProperty('gateway');
        $reflector_property->setAccessible(true);
        $this->assertInstanceOf(ViesGateway::class, $reflector_property->getValue($checker));

        $checker->setApiGateway(new FakeGateway());

        $this->assertInstanceOf(FakeGateway::class, $reflector_property->getValue($checker));
    }
}
