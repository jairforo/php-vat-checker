# PHP VAT Checker

A PHP package that helps you to check information about European companies through the combination of VAT number and Country code. This package is an interface to consume the SOAP API webservice provided by the European Commission. (http://ec.europa.eu/taxation_customs/vies/checkVatTestService.wsdl)

## Installation
```
composer require jairforo/php-vat-checker dev-master
```

## Usage
```php
use JairForo\VATChecker\VATChecker;

$vatInformation = (new VATChecker($countryCode, $vatNumber))->checkVAT();

print_r(json_encode($vatInformation));

{
    "country_code": "RO",
    "vat_number": "8545021xxxx",
    "valid": true,
    "company_name": "Unicorn B.V.",
    "address": "<street> <number> <postcode> <city>"
}

```
