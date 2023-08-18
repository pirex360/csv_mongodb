<?php

namespace Src;

use League\Csv\Reader;
use Src\Traits\AccountTrait;
use Src\Traits\CompanyInfoTrait;
use Src\Traits\TransactionLineTrait;
use Src\Traits\TransactionTrait;

class CsvDataParser
{
    use CompanyInfoTrait, AccountTrait, TransactionTrait, TransactionLineTrait;

    private string $csvFolder;
    private array $csvFiles = [];



    public function __construct(string $folder)
    {
        $this->csvFolder = dirname(__DIR__) . '/' . $folder;
    }


    public function getCsvFileNames() : array
    {
        $files = scandir($this->csvFolder);

        foreach ($files as $file) {
           
            if (strtolower(pathinfo($file, PATHINFO_EXTENSION)) === 'csv') {
                if ($this->isValidCsvFile($file)) {
                    $this->csvFiles[] = $file;
                }
            }

        }


        return $this->csvFiles;
    }


    public function isValidCsvFile(string $file) : bool
    {
        $filePath = $this->csvFolder . '/' . $file;
        $handle = fopen($filePath, 'r');

        // Read the header
        $header = fgetcsv($handle);

        // Check if the header is non-empty
        if ($header === false || empty(array_filter($header))) {
            fclose($handle);
            return false;
        }

        // Read the first data row
        $data = fgetcsv($handle);

        fclose($handle);

        // Check if the first data row is not empty
        return $data !== false && !empty(array_filter($data));

    }
    

    public function parse() : void
    {
        // get formatted array for each model from csv files and save in database collections
        $this->saveToCollections($this->parseFromCsv());      
    }


    private function parseFromCsv() : array
    {
        $data[$this->accountModelName] = $this->processData($this->accountModelName, $this->csvFiles);  
        $data[$this->companyInfoModelName] = $this->processData($this->companyInfoModelName, $this->csvFiles);  
        $data[$this->transactionModelName] = $this->processData($this->transactionModelName, $this->csvFiles);  

        $data[$this->transactionLineModelName] = $this->processData($this->transactionLineModelName, $this->csvFiles, $data[$this->transactionModelName]);  


        return $data;
    }


    private function saveToCollections(array $data) : void
    {
        $models = [
            $this->accountModelName,
            $this->companyInfoModelName,
            $this->transactionModelName,
            $this->transactionLineModelName,
        ];

        foreach ($models as $modelName) { 
            if (!empty($data[$modelName])) { 

                $createMethod = 'create' . $modelName;
                $this->$createMethod($data[$modelName]);

            }
        }

    }


    private function processData(string $model, array $files, array $aux = null) : array
    {

        $data = match($model) {
            $this->accountModelName => $this->logicForAccount($files),
            $this->companyInfoModelName => $this->logicForCompanyInfo($files),
            $this->transactionModelName => $this->logicForTransaction($files),
            $this->transactionLineModelName => $this->logicForTransactionLine($files, $aux)
        };


        return $data;
    }

    
    protected function csvReadFile(string $filePath) : array
    {
        $csv = Reader::createFromPath($filePath, 'r');
        $csv->setHeaderOffset(0);   // Assuming the first row is the header
        
        $data = $csv->getRecords();
        $dataArray = iterator_to_array($data);

        // Transform header keys to lowercase
        $dataArray = array_map('array_change_key_case', $dataArray);


        return array_values($dataArray);
    }


    protected function processCsvFile(string $file): array
    {
        $filePath = $this->csvFolder . '/' . $file;
        
        return $this->csvReadFile($filePath);
    }


    public function areRequiredKeysPresent(array $requiredKeys, array $availableKeys): bool
    {
        foreach ($requiredKeys as $key) {
            if (!in_array($key, $availableKeys)) {
                return false;
            }
        }
        return true;
    }


}