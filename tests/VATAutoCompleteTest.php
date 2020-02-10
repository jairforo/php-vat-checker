<?php

namespace JairForo\VATAutoComplete;

use PHPUnit\Framework\TestCase;

class VATAutoCompleteTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function testShouldNotHaveAnInvalidCountryCode(): void
    {
        $this->expectException(\Exception::class);
        new VATAutoComplete('BR', '854502130B01');
    }

    public function testShouldReturnAValidEntity(): void
    {
        $vatInfo = (new VATAutoComplete('NL', '854502130B01'))->get();

        $this->assertArrayHasKey('country_code', $vatInfo);
        $this->assertArrayHasKey('vat_number', $vatInfo);
        $this->assertArrayHasKey('valid', $vatInfo);
        $this->assertArrayHasKey('company_name', $vatInfo);
        $this->assertArrayHasKey('address', $vatInfo);
    }

    public function testShouldReturnAnInvalidEntity(): void
    {
        $this->expectException(\Exception::class);
        (new VATAutoComplete('NL', '854502130B02'))->get();
    }

    public function testShouldReturnAGermanEntityWithoutNameAndAddressData(): void
    {
        $this->expectException(\Exception::class);
        (new VATAutoComplete('DE', '811191002'))->get();
    }
}

