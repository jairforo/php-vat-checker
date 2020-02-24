<?php

namespace JairForo\VATChecker\Api;

use JairForo\VATChecker\Exceptions\InvalidVATException;
use JairForo\VATChecker\Exceptions\VATCheckerException;
use JairForo\VATChecker\Objects\VATResponse;

interface ApiGateway
{
    /**
     * @param  string  $countryCode
     * @param  string  $vatNumber
     *
     * @throws InvalidVATException
     * @throws VATCheckerException
     *
     * @return VATResponse
     */
    public function check(string $countryCode, string $vatNumber): VATResponse;
}
