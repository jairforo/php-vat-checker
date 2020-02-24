<?php

namespace JairForo\VATChecker\Exceptions;

class VATCheckerException extends \RuntimeException
{
    public static function forMessage(string $message): self
    {
        return new self('Could not check VAT: '.$message);
    }
}
