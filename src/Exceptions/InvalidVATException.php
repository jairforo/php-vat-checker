<?php

namespace JairForo\VATChecker\Exceptions;

class InvalidVATException extends VATCheckerException
{
    protected $message = 'The provided VAT number is not valid for the given country.';
}
