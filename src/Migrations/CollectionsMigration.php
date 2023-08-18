<?php

namespace Src\Migrations;

use Xenus\Connection;
use Xenus\Collection;



class CollectionsMigration
{
    private $connection;

    private string $schemaFile = "collections_schema.json";

    public function __construct() {

        $this->connection = new Connection("mongodb://{$_ENV['DATABASE_SERVER']}:{$_ENV['DATABASE_PORT']}", $_ENV['DATABASE_NAME']);
        
    }


    public static function run() : void
    {
        $migration = new self();
        
        $migration->dropAllCollections();
        $migration->createCollections($migration->getCollectionsArray());
    }


    private function getCollectionsArray() : array
    {
        $path = __DIR__ . '/' . $this->schemaFile;
        
        return json_decode(file_get_contents($path), true);
    }


    private function createCollections(array $collections) : void 
    {

        foreach ($collections as $collectionName => $indexes) {

            $collection = new Collection($this->connection, ['name' => $collectionName]);

            foreach ($indexes as $index) {
                $keys = $index['keys'];
                $options = $index['options'] ?? [];

                $collection->createIndex($keys, $options);
            }
        }

    }


    private function dropAllCollections() : void 
    {
        
        $database = $this->connection->getDatabase();
        $collections = $database->listCollections();
        
        foreach ($collections as $collectionInfo) {
            $collectionName = $collectionInfo['name'];
            $collection = $database->{$collectionName};
            $collection->drop();
        }
    }
    
}