<?php

namespace JairForo\VATChecker\Tests\Unit\Api;

use JairForo\VATChecker\Api\ApiGateway;
use JairForo\VATChecker\Api\FakeGateway;
use PHPUnit\Framework\TestCase;

class FakeApiGatewayTest extends TestCase
{
    use ApiGatewayContractTest;

    protected function getApiGateway(): ApiGateway
    {
        return new FakeGateway();
    }
}
