<?php

namespace JairForo\VATChecker\Tests\Unit\Api;

use JairForo\VATChecker\Api\ApiGateway;
use JairForo\VATChecker\Api\ViesGateway;
use PHPUnit\Framework\TestCase;

class ViesGatewayTest extends TestCase
{
    use ApiGatewayContractTest;

    protected function getApiGateway(): ApiGateway
    {
        return new ViesGateway();
    }
}
