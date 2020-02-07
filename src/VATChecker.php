<?php

namespace JairForo\VATChecker;

class VATChecker implements VATCheckerInterface
{
    const URL = 'http://ec.europa.eu/taxation_customs/vies/services/checkVatService';

    const EU_COUNTRIES = [
        'AT',
        'BE',
        'BG',
        'CY',
        'CZ',
        'DE',
        'DK',
        'EE',
        'ES',
        'FI',
        'FR',
        'GB',
        'GR',
        'HR',
        'HU',
        'IE',
        'IT',
        'LT',
        'LU',
        'LV',
        'MT',
        'NL',
        'PO',
        'PT',
        'RO',
        'SE',
        'SI',
        'SK'
    ];

    private $countryCode;

    private $vatNumber;

    public function __construct(string $countryCode, string $vatNumber)
    {
        if(!in_array($countryCode, self::EU_COUNTRIES)) {
            throw new \InvalidArgumentException("The ${$countryCode} does not belong to European Union");
        }

        $this->countryCode = $countryCode;
        $this->vatNumber = $vatNumber;
    }

    public function getCountryCode(): string
    {
        return $this->countryCode;
    }

    public function getVatNumber(): string
    {
        return $this->vatNumber;
    }

    public function checkVAT(): array
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => self::URL,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $this->getBody(),
            CURLOPT_HTTPHEADER => [
                "Accept: */*",
                "Connection: keep-alive",
                "Content-Type: application/xml",
                "Host: ec.europa.eu"
            ],
        ]);

        $response = curl_exec($curl);

        $err = curl_error($curl);

        if ($err) {
            curl_close($curl);
            return [];
        }

        return $this->parseData($response);
    }

    private function getBody(): string
    {
        return <<<SOAP
            <soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/"
                xmlns:tns1="urn:ec.europa.eu:taxud:vies:services:checkVat:types"
                xmlns:impl="urn:ec.europa.eu:taxud:vies:services:checkVat">
                <soap:Header>
                </soap:Header>
                <soap:Body>
                    <tns1:checkVat xmlns:tns1="urn:ec.europa.eu:taxud:vies:services:checkVat:types" xmlns="urn:ec.europa.eu:taxud:vies:services:checkVat:types">
                    <tns1:countryCode>{$this->getCountryCode()}</tns1:countryCode>
                    <tns1:vatNumber>{$this->getVatNumber()}</tns1:vatNumber></tns1:checkVat>
                 </soap:Body>
             </soap:Envelope>
SOAP;
    }

    private function parseData($response): array
    {
        $response = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $response);
        $data = (array) (new \SimpleXMLElement($response))->soapBody->checkVatResponse;

        if ($data['address']) {
            $addressData = explode(' ', trim(str_replace("\n", ' ', $data['address'])));
            $city = $addressData[count($addressData)-1];
            $postcode = $addressData[count($addressData)-2];

            $address = implode(' ', array_slice($addressData, 0, -2));
        }

        return [
            'country_code' => $data['countryCode'],
            'vat_number' => $data['vatNumber'],
            'valid' => $data['valid'] === "true" ? true : false,
            'company_name' => $data['name'] ?? null,
            'address' => $address ?? null,
            'postcode' => $postcode ?? null,
            'city' => $city ?? null,
        ];
    }
}