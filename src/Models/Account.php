<?php

namespace Src\Models;

use Xenus\Collection;
use Src\Abstracts\Model;


class Account extends Model 
{

    protected string $collectionName = "accounts";

    public function __construct(
        string $name = "", 
        string $code = "", 
        float $balance = 0.0
    ) 
    {
        parent::__construct();

        $this->name = $name;
        $this->code = $code;
        $this->balance = $balance;

    }

    public function updateBalance() : void
    {
        $transactionLinesCollectionName = "transactions_lines";
        $transactionLinesCollection = new Collection($this->connection, ["name" => $transactionLinesCollectionName]);
        $accountsCollection = new Collection($this->connection, ["name" => $this->collectionName]);

        $aggregatePipeline = [
            [
                '$group' => [
                    '_id' => '$accountCode',
                    'totalDebit' => ['$sum' => '$debit'],
                    'totalCredit' => ['$sum' => '$credit']
                ]
            ],
            [
                '$project' => [
                    'accountCode' => '$_id',
                    '_id' => 0,
                    'balance' => ['$subtract' => ['$totalCredit','$totalDebit']]
                ]
            ]
        ];

        $aggregationResults = $transactionLinesCollection->aggregate($aggregatePipeline);

        // Update balances in the accounts collection
        foreach ($aggregationResults as $account) {
            $accountsCollection->updateOne(
                ['code' => $account['accountCode']],
                ['$set' => ['balance' => $account['balance']]]
            );
        }
    }

}