<?php
namespace Src\Traits;


trait CompanyInfoTrait {
 

    protected array $companyInfoCsvKeys =  ['name', 'email', 'location', 'type'];
    protected array $companyInfoTargetCsvHeaders = [
        ['name', 'email', 'location'],
        ['type']
    ];



    public function logicForCompanyInfo(array $files, array $aux = null) : array
    {
        $extractedData = [];

        foreach ($files as $file) 
        {

            $fileContentsArray = $this->processCsvFile($file);
            if (empty($fileContentsArray)) { continue; }

            $extractedData = $this->processCompanyInfoData($fileContentsArray, $extractedData);
            
        }


        return $extractedData;
        
    }


    private function processCompanyInfoData(array $fileContentsArray, array $extractedData): array
    {

        $firstElementKeys = array_keys($fileContentsArray[0]);
        foreach ($this->companyInfoTargetCsvHeaders as $targetHeader) {
        
            if($this->areRequiredKeysPresent($targetHeader, $firstElementKeys))
            {
                $rows = 0;
                foreach($fileContentsArray as $item) 
                {
                    foreach ($this->companyInfoCsvKeys as $csvKeys)
                    {
                        if (isset($item[$csvKeys])) 
                        { 
                            $extractedData[$rows][$csvKeys] = $item[$csvKeys];  // Set Key
                        }

                        if (!isset($extractedData[$rows][$csvKeys]) )
                        {
                            $extractedData[$rows][$csvKeys] = isset($item[$csvKeys]) ? $item[$csvKeys] : '';    // Deal with empty value keys
                        }
                    }
                    $rows++;
                }
               
            }
           
        }

      
        return $extractedData;

    }



}