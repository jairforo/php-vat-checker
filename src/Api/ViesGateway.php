<?php

namespace JairForo\VATChecker\Api;

use DateTime;
use DateTimeZone;
use JairForo\VATChecker\Exceptions\InvalidVATException;
use JairForo\VATChecker\Exceptions\VATCheckerException;
use JairForo\VATChecker\Objects\VATResponse;
use SoapClient;
use SoapFault;
use StdClass;

class ViesGateway implements ApiGateway
{
    const URL = 'http://ec.europa.eu/taxation_customs/vies/checkVatService.wsdl';

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
        try {
            $response = (new SoapClient(self::URL))->checkVat([
                'countryCode'=> $countryCode,
                'vatNumber'=> $vatNumber,
            ]);
        } catch (SoapFault $exception) {
            if ($exception->getMessage() === 'INVALID_INPUT') {
                throw new InvalidVATException();
            }

            throw new VATCheckerException('Could not check VAT: '.$exception->getMessage());
        }

        if (! $response->valid) {
            throw new InvalidVATException();
        }

        return $this->buildResponse($response);
    }

    /**
     * Transform the VIES-response into a valid VAT Response object.
     *
     * @param  StdClass  $response
     * @return VATResponse
     */
    protected function buildResponse(StdClass $response): VATResponse
    {
        $requestedAt = DateTime::createFromFormat(
            'Y-m-dP',
            $response->requestDate,
            new DateTimeZone('Europe/Brussels')
        );

        $companyName = $response->name !== '---' ? $response->name : null;

        $address = null;
        $zipcode = null;
        $city = null;
        if ($response->address !== '---') {
            $addressData = explode(' ', trim(str_replace("\n", ' ', $response->address)));
            if (count($addressData) >= 2) {
                $city = array_pop($addressData);
                $zipcode = array_pop($addressData);
            }

            $address = implode(' ', $addressData);
        }

        return new VATResponse(
            $response->countryCode,
            $response->vatNumber,
            $requestedAt,
            $companyName,
            $address,
            $zipcode,
            $city
        );
    }
}
