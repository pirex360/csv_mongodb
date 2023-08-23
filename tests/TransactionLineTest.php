<?php

namespace Tests;

use Src\CsvDataParser;
use PHPUnit\Framework\TestCase;


class TransactionLineTest extends TestCase
{


    public function testTransactionProcessDataLogic()
    {
    
        $csvDataParser = new CsvDataParser('');

        // Define sample input data
        $data = [
            'transactions' => [
                ['ref' => 'T001', 'original_id' => 1, 'account_code' => 'A001'],
                ['ref' => 'T002', 'original_id' => 2, 'account_code' => 'A002'],
            ],
            'lines' => [
                ['journal_id' => 1, 'account_code' => 'A001', 'debit' => '$100', 'credit' => '$0'],
                ['journal_id' => 2, 'account_code' => 'A002', 'debit' => '$0', 'credit' => '$50'],
                ['journal_id' => 3, 'account_code' => 'A003', 'debit' => '$50', 'credit' => '$0'],
            ],
        ];


        $transactionLineLogicMethod = getPrivateMethod(CsvDataParser::class, 'transactionLineProcessData');
 
        $result = $transactionLineLogicMethod->invoke($csvDataParser, $data);


        $this->assertCount(2, $result);
        $this->assertEquals('T001', $result[0]['transaction_ref']);
        $this->assertEquals('T002', $result[1]['transaction_ref']);
        $this->assertEquals(100.0, $result[0]['debit']);
        $this->assertEquals(50.0, $result[1]['credit']);
    }


}
