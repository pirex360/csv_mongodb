<?php
namespace Src\Traits;

use Src\Models\Transaction;
use Src\Types\TransactionType;

trait TransactionTrait {
 

    public string $transactionModelName = "Transaction";
    protected string $noDescriptionTransaction = "No Description Available";
    protected array $transactionTargetCsvHeaders = [
        ['id', 'journal_id', 'due'],
        ['id', 'description', 'posted'],
        ['id', 'journal_id', 'account_code', 'debit', 'credit']
    ];



    public function logicForTransaction(array $files) : array
    {
        $extractedData = [];
        $results = [];

        foreach ($files as $file) {

            $fileContentsArray = $this->processCsvFile($file);

            if (empty($fileContentsArray)) { continue; }

            $firstElementKeys = array_keys($fileContentsArray[0]);
            foreach ($this->transactionTargetCsvHeaders as $key => $targetHeader) {

                if($this->areRequiredKeysPresent($targetHeader, $firstElementKeys)) 
                {
                    match($key) {
                        0   => $extractedData['invoices'] = $fileContentsArray,
                        1   => $extractedData['journals'] = $fileContentsArray,
                        2   => $extractedData['lines'] = $fileContentsArray,
                    };
                }
                
            }

            if(isset($extractedData['invoices']) && isset($extractedData['journals']) && isset($extractedData['lines'])) {
                $results = $this->transactionProcessData($extractedData);
            }

        }
      

        return $results;
        
    }

    private function transactionProcessData(array $data) : array
    {
        $results = [];
       
        foreach($data['journals'] as $key => $journal)
        {
            $line['id'] = $key+1;
            $line['original_id'] = $journal['id'];
            $line['description'] = $journal['description'];
            $line['type'] = TransactionType::JOURNAL;

            $results[] = $line;
        }

        array_walk($data['invoices'], function ($invoice) use (&$results) {
            foreach ($results as &$result) {
                if ($result['original_id'] === $invoice['journal_id']) {
                    $result['type'] = TransactionType::INVOICE;
                    break;
                }
            }
        });

        array_walk($data['lines'], function ($line) use (&$results) {
            $originalIds = array_column($results, 'original_id');
            
            if (!in_array($line['journal_id'], $originalIds)) {
                $newLine = [
                    'id'            => count($results)+1,
                    'original_id'   => $line['journal_id'],
                    'description'   => $this->noDescriptionTransaction,
                    'type'          => TransactionType::JOURNAL
                ];
                
                $results[] = $newLine;
            }
        });
        
     

        return $results;

    }



    public function createTransaction(array $csvData) : void
    {

        foreach($csvData as $item)
        {
            $data = new Transaction(
                $item['id'],
                $item['description'],
                $item['type']
            );

            $data->save();
        }

    }



}