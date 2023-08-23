<?php

namespace Src\Services;

use Src\Models\Account;
use Src\Models\TransactionLine;

class AccountService
{
    private Account $account;
    private TransactionLine $transactionLine;


    public function __construct(){
        $this->account = new Account();
        $this->transactionLine = new TransactionLine();
    }

    public function updateBalance() : void
    {
        $aggregatePipeline = [
            [
                '$group' => [
                    '_id' => '$account_code',
                    'totalDebit' => ['$sum' => '$debit'],
                    'totalCredit' => ['$sum' => '$credit']
                ]
            ],
            [
                '$project' => [
                    'account_code' => '$_id',
                    '_id' => 0,
                    'balance' => ['$subtract' => ['$totalCredit','$totalDebit']]
                ]
            ]
        ];

        $aggregationResults = $this->transactionLine->collection->aggregate($aggregatePipeline);
        
        // Update balances in the accounts collection
        $this->updateAccountBalances($aggregationResults);

    }


    private function updateAccountBalances($aggregationResults): void
    {
        foreach ($aggregationResults as $item) {

            $this->account->collection->updateOne(
                ['code' => $item['account_code']],
                ['$set' => ['balance' => $item['balance']]]
            );

        }
    }

}