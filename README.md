# PHP VAT Autocomplete

A PHP package that helps you to check information about Union European companies through the combination of VAT number and Country code. This package is an interface to consume the SOAP API webservice provided by the European Commission. (http://ec.europa.eu/taxation_customs/vies/checkVatTestService.wsdl)

## Installation
```
composer require jairforo/php-vat-autocomplete dev-master
```

## Usage
```php
use JairForo\VATAutoComplete\VATAutoComplete;

$vatInformation = (new VATAutoComplete($countryCode, $vatNumber))->checkVAT();

print_r(json_encode($vatInformation));

{
    "country_code": "NL",
    "vat_number": "8545021xxxx",
    "valid": true,
    "company_name": "Unicorn B.V.",
    "address": "Unicorn Street 24",
    "postcode": "1097 BC",
    "city": "Amsterdam",
}

```
