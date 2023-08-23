<?php

namespace Src\Models;

use Src\Abstracts\Model;


class TransactionLine extends Model 
{
    public string $collectionName = "transactions_lines";
    public array $fillable = ["transaction_ref", "account_code", "debit", "credit"];


    // Constructor to initialize properties
    public function __construct(
        public int|array $transaction_ref = [], 
        public string $account_code = "", 
        public float $debit = 0.0, 
        public float $credit = 0.0
    )
    {
        parent::__construct();

        if (is_array($transaction_ref)) {
            $this->fillFromArray($transaction_ref);
        } else {
            [$this->transaction_ref, $this->account_code, $this->debit, $this->credit] = [$transaction_ref, $account_code, $debit, $credit];
        }

    }

}