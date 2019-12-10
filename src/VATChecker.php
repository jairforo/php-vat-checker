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
            throw new \InvalidArgumentException('The country code does not belong to European Union');
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

        $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        if ($statusCode > 200) {
            throw new \Exception($this->getMessageByStatusCode($statusCode), $statusCode);
        }

        return $this->parseSoapResponse($response, $statusCode);
    }


    private function parseSoapResponse($response, $statusCode): array
    {
        $response = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $response);
        $data = (array) (new \SimpleXMLElement($response))->soapBody->checkVatResponse;

        $address = $number = $name = null;
        if ($data["address"] instanceof SimpleXMLElement) {
            $addressData = explode(' ', trim(str_replace("\n", ' ', $data['address'])));

            list($address, $number, $postcode, $city) = $addressData;
            $name = $data['name'];
            $address = $address . ' ' . $number;
        }

        return [
            'country_code' => $data['countryCode'],
            'vat_number' => $data['vatNumber'],
            'valid' => $data['valid'] === "true" ? true : false,
            'company_name' => $name ?? null,
            'address' => $address ?? null,
            'postcode' => $postcode ?? null,
            'city' => $city ?? null,
        ];
    }

    private function getMessageByStatusCode(int $statusCode): string
    {
        $errorMessage = 'SOMETHING_WENT_WRONG';
        switch ($statusCode) {
            case 201:
                $errorMessage = 'INVALID_INPUT';
                break;
            case 202:
                $errorMessage = 'INVALID_REQUESTER_INFO';
                break;
            case 300:
                $errorMessage = 'SERVICE_UNAVAILABLE';
                break;
            case 301:
                $errorMessage = 'MS_UNAVAILABLE';
                break;
            case 302:
                $errorMessage = 'MS_UNAVAILABLE';
                break;
            case 400:
                $errorMessage = 'VAT_BLOCKED';
                break;
            case 401:
                $errorMessage = 'IP_BLOCKED';
                break;
            case 500:
                $errorMessage = 'GLOBAL_MAX_CONCURRENT_REQ';
                break;
            case 501:
                $errorMessage = 'GLOBAL_MAX_CONCURRENT_REQ_TIME';
                break;
            case 600:
                $errorMessage = 'MS_MAX_CONCURRENT_REQ';
                break;
            case 601:
                $errorMessage = 'MS_MAX_CONCURRENT_REQ_TIME';
                break;
        }

        return $errorMessage;
    }
}