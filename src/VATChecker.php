<?php

namespace JairForo\VATChecker;

use JairForo\VATChecker\Api\ApiGateway;
use JairForo\VATChecker\Api\ViesGateway;
use JairForo\VATChecker\Exceptions\InvalidVATException;
use JairForo\VATChecker\Exceptions\VATCheckerException;
use JairForo\VATChecker\Objects\VATResponse;

class VATChecker
{
    /** @var ApiGateway */
    private $gateway;

    public function __construct()
    {
        // Check if were running inside of a Laravel installation. If we are,
        // we can let the IoC container resolve the right gateway for us.
        if (defined('LARAVEL_START') && function_exists('app')) {
            $this->setApiGateway(app(ApiGateway::class));

            return;
        }

        $this->setApiGateway(new ViesGateway());
    }

    /**
     * Swap out the used API Gateway used to check the VAT at.
     *
     * @param  ApiGateway  $gateway
     * @return VATChecker
     */
    public function setApiGateway(ApiGateway $gateway): self
    {
        $this->gateway = $gateway;

        return $this;
    }

    /**
     * Checks the VAT information of the given VAT number.
     *
     * @param  string  $countryCode
     * @param  string  $vatNumber
     *
     * @throws InvalidVATException
     * @throws VATCheckerException
     *
     * @return VATResponse
     */
    public function check(string $countryCode, string $vatNumber): VATResponse
    {
        return $this->gateway->check($countryCode, $vatNumber);
    }

    /**
     * Only validates whether the given VAT information is valid.
     *
     * @param  string  $countryCode
     * @param  string  $vatNumber
     *
     * @throws VATCheckerException
     *
     * @return bool
     */
    public function isValid(string $countryCode, string $vatNumber): bool
    {
        try {
            $this->check($countryCode, $vatNumber);

            return true;
        } catch (InvalidVATException $exception) {
            //
        }

        return false;
    }
}
