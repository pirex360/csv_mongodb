<?php

require __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;
use Monolog\Logger;
use Src\CsvDataParser;
use Monolog\Handler\StreamHandler;
use Src\Migrations\CollectionsMigration;

$log = new Logger('Dataswitcher Tech Challenge');
$log->pushHandler(new StreamHandler('app.log', Logger::DEBUG));
$log->info('Running Conversion');

// CALL YOUR CODE HERE
$startTime = microtime(true);

$log->info(':: Reading .env file...');
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$log->info('::' . $_ENV['APP_NAME'] . ' v.' . $_ENV['APP_VERSION'] );

$log->info(':: Running Database Migrations...');
CollectionsMigration::run();

$log->info(':: Parse Data Folder...');
$parser = new CsvDataParser($_ENV['CSV_FOLDER']);
$files = $parser->getCsvFileNames();
$log->info(' » Detected files with data: ' . implode(', ', $files));

$log->info(':: Processing Data & Logic Started...');
$parser->parse();
$log->info(':: Processing Data & Logic Ended...');

$log->info(':: Updating Account Balance Values...');
$parser->updateAccountBalance();


$elapsedTime = microtime(true) - $startTime;
$log->info("Total execution time: {$elapsedTime} seconds");

// END YOUR CALL HERE


$log->info('Ended Conversion');
echo "\nConversion Finished!\n";
