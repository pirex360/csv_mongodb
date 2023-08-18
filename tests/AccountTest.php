<?php

namespace Tests;

use Src\CsvDataParser;
use PHPUnit\Framework\TestCase;

class AccountTest extends TestCase
{


    public function testAccountProcessDataLogic()
    {
    
        $csvDataParser = new CsvDataParser('');

        // Define sample input data
        $data = [
            'accounts' => [
                ['name' => 'Account1', 'account_code' => 'A001'],
                ['name' => 'Account2', 'account_code' => 'A002'],
            ],
            'list' => [
                ['account_code' => 'A003'],
                ['account_code' => 'A004'],
            ],
            'lines' => [
                ['account_code' => 'A005'],
                ['account_code' => 'A006'],
                ['account_code' => 'A003'],
                ['account_code' => ''],
            ],
        ];


        $accountLogicMethod = getPrivateMethod(CsvDataParser::class, 'accountProcessData');
        $accountNoName = getPrivateProperty(CsvDataParser::class, 'noAccountName');
        $accountNoCode =  getPrivateProperty(CsvDataParser::class, 'noAccountCode');

        $result = $accountLogicMethod->invoke($csvDataParser, $data);


        $this->assertCount(7, $result);

        $this->assertEquals('Account1', $result[0]['name']);
        $this->assertEquals('A001', $result[0]['account_code']);
        $this->assertEquals(0, $result[0]['balance']);
        $this->assertEquals($accountNoName->getValue($csvDataParser), $result[3]['name']);
        $this->assertEquals($accountNoCode->getValue($csvDataParser), $result[6]['account_code']);

    }


    

}
