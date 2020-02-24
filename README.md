# PHP VAT Checker

[![Latest Version](https://img.shields.io/github/release/jairforo/php-vat-checker.svg?style=flat-square)](https://github.com/jairforo/jairforo/php-vat-checker/releases)
[![Total Downloads](https://img.shields.io/packagist/dt/jairforo/php-vat-checker.svg?style=flat-square)](https://packagist.org/packages/jairforo/php-vat-checker)


A PHP package that helps you to check information about Union European companies through the combination of VAT number and Country code. This package is an interface to consume the SOAP API webservice provided by the European Commission. (http://ec.europa.eu/taxation_customs/vies/checkVatTestService.wsdl)

## Installation

You can install the package via composer:

```bash
composer require jairforo/php-vat-checker
```

## Usage

### Checking the VAT information.
This check only validates whether the VAT information is valid.

```php
use JairForo\VATChecker\Exceptions\VATCheckerException;
use JairForo\VATChecker\VATChecker;

try {
    /** @var bool $isValid */
    $isValid = (new VATChecker())->isValid($countryCode, $vatNumber);
} catch (VATCheckerException $exception) {
    // Something went wrong during the request.
}
```

### Obtaining the VAT information.

- If the information is not valid, an InvalidVATException is thrown.
- If something goes wrong during the request, an VATCheckerException is thrown.

```php
use JairForo\VATChecker\Exceptions\InvalidVATException;
use JairForo\VATChecker\Exceptions\VATCheckerException;
use JairForo\VATChecker\Objects\VATResponse;
use JairForo\VATChecker\VATChecker;

try {
    /** @var VATResponse $vatResponse */
    $vatResponse = (new VATChecker())->check($countryCode, $vatNumber);
    
    print_r($vatResponse);
} catch (VATCheckerException $exception) {
    if ($exception instanceof InvalidVATException) {
        // The VAT information was invalid.
        return;
    }
    
    // Something else went wrong during the request.
}
```

The above, when successful, prints the following output:
```
{
  "country_code": "NL",
  "vat_number": "854502130B01",
  "requested_at": {
    "date": "2020-02-02 00:00:00.000000",
    "timezone_type": 1,
    "timezone": "+00:00"
  },
  "company_name": "TRADUS B.V.",
  "address": "WIBAUTSTRAAT 00137 C",
  "zipcode": "1097DN",
  "city": "AMSTERDAM"
}
```

### Testing

``` bash
composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## License

The BSD 2-clause "Simplified" license  (bsd-2-clause). Please see [License File](LICENSE.md) for more information.
