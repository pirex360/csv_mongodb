<?php
namespace Src\Traits;


trait TransactionLineTrait {
 

    protected array $transactionLineTargetCsvHeaders = [
        ['id', 'journal_id', 'account_code', 'debit', 'credit']
    ];
  


    public function logicForTransactionLine(array $files, array $aux) : array
    {
        $extractedData = [];
        $results = [];

        foreach ($files as $file) {

            $fileContentsArray = $this->processCsvFile($file);

            if (empty($fileContentsArray)) { continue; }

            $firstElementKeys = array_keys($fileContentsArray[0]);
            foreach ($this->transactionLineTargetCsvHeaders as $key => $targetHeader) {
                
                if($this->areRequiredKeysPresent($targetHeader, $firstElementKeys) && $key === 0) 
                {
                    $extractedData['transactions'] = $aux;
                    $extractedData['lines'] = $fileContentsArray;
                }
                
            }

            if(isset($extractedData['transactions']) && isset($extractedData['lines'])) {
                $results = $this->transactionLineProcessData($extractedData);
            }
           
        }
      

        return $results;
        
    }


    private function getNewIdFromOriginal(string $original_id, array $transactions) : string | null
    {
        $key = array_search($original_id, array_column($transactions, 'original_id'));

        return $key !== false ? $transactions[$key]['ref'] : null;
    }


    private function transformCurrency($string) : float
    {
        return (float)str_replace('$', '' ,$string);
    }


    private function transactionLineProcessData(array $data) : array
    {
        $results = [];

        foreach($data['lines'] as $line)
        { 
            $newId = $this->getNewIdFromOriginal($line['journal_id'], $data['transactions']);

            if ($newId) {
                $item['transaction_ref'] = $newId;
                $item['original_journal_id'] = $line['journal_id'];
                $item['account_code'] = $line['account_code'] 
                                            ? $line['account_code'] 
                                            : $this->noAccountCodeText;
                $item['debit'] = $this->transformCurrency($line['debit']);
                $item['credit'] = $this->transformCurrency($line['credit']);

                $results[] = $item;
            }

        }



        return $results;

    }





}