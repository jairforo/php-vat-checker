<?php

namespace JairForo\VATChecker\Api;

use DateTime;
use JairForo\VATChecker\Exceptions\InvalidVATException;
use JairForo\VATChecker\Exceptions\VATCheckerException;
use JairForo\VATChecker\Objects\VATResponse;

class FakeGateway implements ApiGateway
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
    public function check(string $countryCode, string $vatNumber): VATResponse
    {
        if ($countryCode === 'DE' && $vatNumber === '811191002') {
            return new VATResponse(
                $countryCode,
                $vatNumber,
                DateTime::createFromFormat('Y-m-dP', date('Y-m-dP'))
            );
        }

        if ($countryCode !== 'NL' || $vatNumber !== '854502130B01') {
            throw new InvalidVATException();
        }

        return new VATResponse(
            $countryCode,
            $vatNumber,
            DateTime::createFromFormat('Y-m-dP', date('Y-m-dP')),
            'UNICORN B.V.',
            'UNICORN STREET 007',
            '1108DH',
            'AMSTERDAM'
        );
    }
}
