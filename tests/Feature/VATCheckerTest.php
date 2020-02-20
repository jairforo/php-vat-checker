<?php

namespace JairForo\VATChecker\Tests\Feature;

use DateTime;
use JairForo\VATChecker\Api\FakeGateway;
use JairForo\VATChecker\Exceptions\InvalidVATException;
use JairForo\VATChecker\Objects\VATResponse;
use JairForo\VATChecker\VATChecker;
use PHPUnit\Framework\TestCase;

class VATCheckerTest extends TestCase
{
    /** @var VATChecker */
    private $checker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->checker = new VATChecker();
        $this->checker->setApiGateway(new FakeGateway());
    }

    /** @test */
    public function should_return_a_valid_vat_response()
    {
        $countryCode = 'NL';
        $vatNumber = '854502130B01';

        $response = $this->checker->check($countryCode, $vatNumber);

        $this->assertInstanceOf(VATResponse::class, $response);
        $this->assertEquals('NL', $response->country_code);
        $this->assertEquals('854502130B01', $response->vat_number);
        $this->assertEquals(DateTime::createFromFormat('Y-m-dP', date('Y-m-dP')), $response->requested_at);
        $this->assertEquals('TRADUS B.V.', $response->company_name);
        $this->assertEquals('WIBAUTSTRAAT 00137 C', $response->address);
        $this->assertEquals('1097DN', $response->zipcode);
        $this->assertEquals('AMSTERDAM', $response->city);
    }

    /** @test */
    public function should_throw_an_invalid_vat_exception_for_being_an_invalid_vat_number()
    {
        $countryCode = 'BR';
        $vatNumber = '854502130B01';

        $this->expectException(InvalidVATException::class);

        $this->checker->check($countryCode, $vatNumber);
    }

    /** @test */
    public function should_be_a_valid_vat_number()
    {
        $countryCode = 'NL';
        $vatNumber = '854502130B01';

        $this->assertTrue($this->checker->isValid($countryCode, $vatNumber));
    }

    /** @test */
    public function should_be_an_invalid_vat_number()
    {
        $countryCode = 'BR';
        $vatNumber = '854502130B01';

        $this->assertFalse($this->checker->isValid($countryCode, $vatNumber));
    }
}
