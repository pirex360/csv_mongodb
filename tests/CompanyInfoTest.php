<?php

namespace Tests;

use Src\CsvDataParser;
use PHPUnit\Framework\TestCase;

class CompanyInfoTest extends TestCase
{


    public function testCompanyInfoLogic()
    {
    
        $csvDataParser = new CsvDataParser('');

        // Define sample input data
        $dataset_1 = [
            ['name' => 'Company A', 'email' => 'john@mail.com', 'location' => 'Portugal'],
            ['name' => 'Company B', 'email' => 'doe@mail.com', 'location' => 'Spain'],
            ['name' => 'Company XPTO', 'email' => 'xpto@mail.com'],
            ['name' => 'Company Extra'],
        ];

        $dataset_2 = [
                ['type' => 'cf'],
                ['type' => 'accrual'],
                ['type' => 'lifo'],
                ['type' => ''],
        ];


        $accountLogicMethod = getPrivateMethod(CsvDataParser::class, 'processCompanyInfoData');
        
        $result = $accountLogicMethod->invoke($csvDataParser, $dataset_1, []);  
        $result = $accountLogicMethod->invoke($csvDataParser, $dataset_2, $result);  
   

        $this->assertCount(4, $result);

        $this->assertEquals('Portugal', $result[0]['location']);
        $this->assertEquals('lifo', $result[2]['type']);
        $this->assertEquals('', $result[3]['type']);
        

    }


}
