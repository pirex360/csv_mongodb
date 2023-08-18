<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Src\CsvDataParser;

class CsvParserTest extends TestCase
{


    public function testGetCsvFileNames()
    {
        $csvDataParser = new CsvDataParser('');
        
        $accountLogic = getPrivateProperty(CsvDataParser::class, 'csvFiles');
        $accountLogic->setValue($csvDataParser, ['fileAbc.csv', 'file123.csv']);

    
        // Call the method to test
        $result = $csvDataParser->getCsvFileNames();

        // Assert the result
        $this->assertEquals(['fileAbc.csv', 'file123.csv'], $result);
    }


    public function testIsValidCsvFile()
    {

       $csvDataParser = new CsvDataParser('/data');

       $result = $csvDataParser->isValidCsvFile('invoices.csv');

       $this->assertTrue($result);

    }


    public function testIsNotValidCsvFile()
    {

       $csvDataParser = new CsvDataParser('/data');

       $result = $csvDataParser->isValidCsvFile('NOINFO.csv');

       $this->assertFalse($result);

    }


    public function testAreRequiredKeysPresent()
    {
        
        $csvDataParser = new CsvDataParser('');

        // Test case with matching keys
        $requiredKeys = ['key1', 'key2'];
        $availableKeys = ['key1', 'key2', 'key3'];
        $result = $csvDataParser->areRequiredKeysPresent($requiredKeys, $availableKeys);
        $this->assertTrue($result);

        // Test case with missing keys
        $requiredKeys = ['key1', 'key2', 'key3'];
        $availableKeys = ['key1', 'key2'];
        $result = $csvDataParser->areRequiredKeysPresent($requiredKeys, $availableKeys);
        $this->assertFalse($result);

    }


}
