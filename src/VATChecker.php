<?php

namespace JairForo\VATChecker;

class VATChecker
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

    /** @var $countryCode string */
    private $countryCode;

    /** @var $vatNumber string */
    private $vatNumber;

    /**
     * VATChecker constructor.
     * @param string $countryCode
     * @param string $vatNumber
     */
    public function __construct(string $countryCode, string $vatNumber)
    {
        if(!in_array($countryCode, self::EU_COUNTRIES)) {
            throw new \InvalidArgumentException('The country code does not belong to EU');
        }

        $this->countryCode = $countryCode;
        $this->vatNumber = $vatNumber;
    }

    /**
     * @return string
     */
    public function getCountryCode(): string
    {
        return $this->countryCode;
    }

    /**
     * @param string $countryCode
     */
    public function setCountryCode(string $countryCode)
    {
        $this->countryCode = $countryCode;
    }

    /**
     * @return string
     */
    public function getVatNumber(): string
    {
        return $this->vatNumber;
    }

    /**
     * @param string $vatNumber
     */
    public function setVatNumber(string $vatNumber)
    {
        $this->vatNumber = $vatNumber;
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
            CURLOPT_POSTFIELDS => "<soap:Envelope xmlns:soap=\"http://schemas.xmlsoap.org/soap/envelope/\"\n  xmlns:tns1=\"urn:ec.europa.eu:taxud:vies:services:checkVat:types\"\n  xmlns:impl=\"urn:ec.europa.eu:taxud:vies:services:checkVat\">\n  <soap:Header>\n  </soap:Header>\n  <soap:Body>\n    <tns1:checkVat xmlns:tns1=\"urn:ec.europa.eu:taxud:vies:services:checkVat:types\"\n     xmlns=\"urn:ec.europa.eu:taxud:vies:services:checkVat:types\">\n     <tns1:countryCode>{$this->getCountryCode()}</tns1:countryCode>\n     <tns1:vatNumber>{$this->getVatNumber()}</tns1:vatNumber>\n    </tns1:checkVat>\n  </soap:Body>\n</soap:Envelope>",
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

        return $this->parseSoapResponse($response);
    }

    private function parseSoapResponse($response)
    {
        $response = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $response);
        $data = (array) (new \SimpleXMLElement($response))->soapBody->checkVatResponse;
        $data['address'] = trim(str_replace("\n", ' ', $data['address']));
        return [
            'country_code' => $data['countryCode'],
            'vat_number' => $data['vatNumber'],
            'valid' => (bool) $data['valid'],
            'company_name' => $data['name'],
            'address' => $data['address'] ?? null,
        ];
    }
}