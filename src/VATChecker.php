<?php

namespace JairForo\VATChecker;

class VATChecker
{
    const URL = 'http://ec.europa.eu/taxation_customs/vies/services/checkVatService';

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

        curl_setopt_array($curl, array(
            CURLOPT_URL => self::URL,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "<soap:Envelope xmlns:soap=\"http://schemas.xmlsoap.org/soap/envelope/\"\n  xmlns:tns1=\"urn:ec.europa.eu:taxud:vies:services:checkVat:types\"\n  xmlns:impl=\"urn:ec.europa.eu:taxud:vies:services:checkVat\">\n  <soap:Header>\n  </soap:Header>\n  <soap:Body>\n    <tns1:checkVat xmlns:tns1=\"urn:ec.europa.eu:taxud:vies:services:checkVat:types\"\n     xmlns=\"urn:ec.europa.eu:taxud:vies:services:checkVat:types\">\n     <tns1:countryCode>NL</tns1:countryCode>\n     <tns1:vatNumber>854502130B01</tns1:vatNumber>\n    </tns1:checkVat>\n  </soap:Body>\n</soap:Envelope>",
            CURLOPT_HTTPHEADER => array(
                "Accept: */*",
                "Accept-Encoding: gzip, deflate",
                "Cache-Control: no-cache",
                "Connection: keep-alive",
                "Content-Length: 544",
                "Content-Type: application/xml",
                "Host: ec.europa.eu",
                "Postman-Token: 617c609d-4695-4572-9cae-845731f3bf19,a752a846-18b2-4f2e-b25d-09fd3c044016",
                "User-Agent: PostmanRuntime/7.20.1",
                "cache-control: no-cache"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            echo $response;
        }
    }
}