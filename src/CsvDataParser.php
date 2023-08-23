<?php

namespace Src;

use ReflectionClass;
use League\Csv\Reader;
use Src\Traits\AccountTrait;
use Src\Traits\CompanyInfoTrait;
use Src\Traits\TransactionTrait;
use Src\Traits\TransactionLineTrait;

class CsvDataParser
{
    use AccountTrait, CompanyInfoTrait, TransactionTrait, TransactionLineTrait;

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
        $data = [];
        $usedModels = $this->getUsedModels();

        foreach ($usedModels as $model) {
            if ($model === "TransactionLine") {
                $data[$model] = $this->processData($model, $this->csvFiles, $data['Transaction']);
            } else {
                $data[$model] = $this->processData($model, $this->csvFiles);
            }
        }


        return $data;
    }


    private function saveToCollections(array $data) : void
    {

        $usedModels = $this->getUsedModels();

        foreach ($usedModels as $model) { 

            if (!empty($data[$model])) { 

                $fullClass = "Src\\Models\\" . $model;
                (new $fullClass)->save($data[$model]);

            }
        }

    }


    private function processData(string $model, array $files, array $aux = null) : array
    {
        $method = "logicFor" . $model;


        return $this->$method($files, $aux);
        
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

    
    public function getUsedModels() {

        $usedModels = [];

        $class = new ReflectionClass($this);
        $traits = $class->getTraits();

        foreach ($traits as $trait) {
            $traitName = $trait->getName();
            $parts = explode('\\', $traitName);
            $usedModels[] =  str_replace("Trait","",end($parts));
        }


        return $usedModels;
        
    }


}