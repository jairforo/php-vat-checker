<?php

namespace JairForo\VATChecker\Objects;

use DateTime;

class VATResponse extends ImmutableObject
{
    public function __construct(
        string $countryCode,
        string $vatNumber,
        DateTime $requestedAt,
        string $companyName = null,
        string $address = null,
        string $zipcode = null,
        string $city = null,
        string $originalAddress = null
    ) {
        parent::__construct([
            'country_code' => $countryCode,
            'vat_number' => $vatNumber,
            'requested_at' => $requestedAt,
            'company_name' => $companyName,
            'address' => $address,
            'zipcode' => $zipcode,
            'city' => $city,
            'original_address' => $originalAddress,
        ]);
    }
}
