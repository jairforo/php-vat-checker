<?php

namespace JairForo\VATChecker;

use PHPUnit\Framework\TestCase;

class VATCheckerTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function testShouldNotHaveAnInvalidCountryCode(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new VATChecker('BR', '854502130B01');
    }

    public function testShouldHaveValidCountryCode(): void
    {
        $checker = new VATChecker('NL', '854502130B01');
        $this->assertEquals($checker->getVatNumber(), '854502130B01');
        $this->assertEquals($checker->getCountryCode(), 'NL');
    }

    public function testShouldReturnAValidEntity(): void
    {
        $vatInfo = (new VATChecker('NL', '854502130B01'))->checkVAT();
        $this->assertArrayHasKey('country_code', $vatInfo);
        $this->assertArrayHasKey('vat_number', $vatInfo);
        $this->assertArrayHasKey('valid', $vatInfo);
        $this->assertArrayHasKey('company_name', $vatInfo);
        $this->assertArrayHasKey('address', $vatInfo);
    }
}

