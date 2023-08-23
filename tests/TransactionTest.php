<?php

namespace Tests;

use Src\CsvDataParser;
use Src\Types\TransactionType;
use PHPUnit\Framework\TestCase;

class TransactionTest extends TestCase
{


    public function testTransactionProcessDataLogic()
    {
    
        $csvDataParser = new CsvDataParser('');

        // Define sample input data
        $data = [
            'journals' => [
                ['id' => 1, 'description' => 'Journal 1'],
                ['id' => 2, 'description' => 'Journal 2'],
            ],
            'invoices' => [
                ['journal_id' => 1],
                ['journal_id' => 2],
            ],
            'lines' => [
                ['journal_id' => 1],
                ['journal_id' => 3],
            ],
        ];


        $transactionLogicMethod = getPrivateMethod(CsvDataParser::class, 'transactionProcessData');
        $noDescriptionTransactionText = getPrivateProperty(CsvDataParser::class, 'noDescriptionTransactionText');
 
        $result = $transactionLogicMethod->invoke($csvDataParser, $data);


        $this->assertCount(3, $result);
        $this->assertEquals(TransactionType::INVOICE, $result[0]['type']);
        $this->assertEquals(TransactionType::INVOICE, $result[1]['type']);
        $this->assertNotEquals(TransactionType::INVOICE, $result[2]['type']);
        $this->assertEquals($noDescriptionTransactionText->getValue($csvDataParser), $result[2]['description']);
    }


}
