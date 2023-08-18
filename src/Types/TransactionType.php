<?php
/**
 * Note if php8.1+ , this should be a ENUM
 */
namespace Src\Types;

class TransactionType
{
    public const JOURNAL = 'J';
    public const INVOICE = 'I';
}