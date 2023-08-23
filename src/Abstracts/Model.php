<?php

namespace Src\Abstracts;

use Xenus\Collection;
use Xenus\Connection;

abstract class Model extends Collection
{

    public $collection;
    public $connection;
    public string $collectionName = "";
    public array $fillable = [];



    public function __construct(){

       $this->initialize();

    }

    protected function initialize() : void
    {

        $this->connection = new Connection("mongodb://{$_ENV['DATABASE_SERVER']}:{$_ENV['DATABASE_PORT']}", $_ENV['DATABASE_NAME']);
        if ($this->collectionName) {
            $this->collection = new Collection($this->connection, ['name' => $this->collectionName ]);
        }
        
    }


    protected function fillFromArray(array $data)
    {
        foreach ($data as $key => $value) {
            if (in_array($key, $this->fillable)) {
                $this->$key = $value;
            }
        }
    }


    public function getFillableValues(): array
    {
        $fillableValues = [];

        foreach ($this->fillable as $propertyName) {
            if (property_exists($this, $propertyName)) {
                $fillableValues[$propertyName] = $this->$propertyName;
            }
        }

        return $fillableValues;
    }


    public function save(array $data = []) : \MongoDB\InsertOneResult | \MongoDB\InsertManyResult | null
    {

        if (!$data) {   
            // single record
            $fillableValues = $this->getFillableValues();
            if(! empty($fillableValues)) {
                return $this->collection->insert($fillableValues);
            }

        } else {
            // array of data
            $fillableDocuments = [];

            foreach ($data as $document) {
                $fillableDocument = array_intersect_key($document, array_flip($this->fillable));
                if (!empty($fillableDocument)) {
                    $fillableDocuments[] = $fillableDocument;
                }
            }

            if (!empty($fillableDocuments)) {
                return $this->collection->insertMany($fillableDocuments);
            }

        }


        return null;
        
    }


}