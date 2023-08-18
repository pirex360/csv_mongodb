<?php
namespace Src\Traits;

use Src\Models\Account;

trait AccountTrait {
 

    public string $accountModelName = 'Account';

    protected string $noAccountCode = "UNKNOWN|ACCOUNT";
    protected string $noAccountName = "Unrecognized Account Name";
    protected array $accountTargetCsvHeaders = [
        ['id', 'name', 'account_code'],
        ['account_code', 'set'],
        ['id', 'journal_id', 'account_code', 'debit', 'credit']
    ];

    
    public function logicForAccount(array $files) : array
    {
        $results = [];
        $extractedData = [];

        foreach ($files as $file) {

            $fileContentsArray = $this->processCsvFile($file);

            if (empty($fileContentsArray)) { continue; }

            $firstElementKeys = array_keys($fileContentsArray[0]);
            foreach ($this->accountTargetCsvHeaders as $key => $targetHeader) {

                if($this->areRequiredKeysPresent($targetHeader, $firstElementKeys)) 
                {
                    match($key) {
                        0   => $extractedData['accounts'] = $fileContentsArray,
                        1   => $extractedData['list'] = $fileContentsArray,
                        2   => $extractedData['lines'] = $fileContentsArray,
                    };
                }
            }

            if(isset($extractedData['accounts']) && isset($extractedData['list']) && isset($extractedData['lines']))
            {
                $results = $this->accountProcessData($extractedData);
            }
        }


        return $results;

    }


    private function accountProcessData(array $data) : array
    {
        $results = [];

        foreach($data['accounts'] as $account)
        {
            $line['name'] = $account['name'];
            $line['account_code'] = $account['account_code'];
            $line['balance'] = 0;

            $results[] = $line;
        }

        $existingAccountCodes = array_column($results, 'account_code');
        foreach ($data['list'] as $account) {

            if (!in_array($account['account_code'], $existingAccountCodes) && $account['account_code'] != "") {

                $results[] = [
                    "name"          => $this->noAccountName,
                    "account_code"  => $account['account_code'],
                    "balance"       => 0
                ];

            }
        }

        $existingAccountCodes = array_column($results, 'account_code');
        foreach ($data['lines'] as $account) {

            if (!in_array($account['account_code'], $existingAccountCodes)) {

                $results[] = [
                    "name"          => $account['account_code']
                                            ? $this->noAccountName . ' [' . ucfirst(strtolower($account['account_code'])) . ']'
                                            : $this->noAccountName,
                    "account_code"  => $account['account_code']
                                            ? $account['account_code']
                                            : $this->noAccountCode,
                    "balance"       => 0
                ];

            }
        }


        return $results;

    }


    public function createAccount(array $csvData) : void
    {

        foreach($csvData as $item)
        {
            $data = new Account(
                $item['name'],
                $item['account_code'],
                $item['balance'],
            );

            $data->save();
        }

    }


    public function updateAccountBalance() : void
    {
        (new Account())->updateBalance();
    }

}