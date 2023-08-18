<?php

namespace Src\Models;

use Src\Abstracts\Model;


class TransactionLine extends Model 
{
    protected string $collectionName = "transactions_lines";

    // Constructor to initialize properties
    public function __construct(
        int $transactionRef, 
        string $accountCode, 
        float $debit, 
        float $credit
    )
    {
        parent::__construct();

        $this->transactionRef = $transactionRef;
        $this->accountCode = $accountCode;
        $this->debit = $debit;
        $this->credit = $credit;
    }

}