<?php

namespace Src\Abstracts;

use Xenus\Collection;
use Xenus\Connection;

abstract class Model extends Collection
{

    protected $collection;
    protected $connection;
    protected string $collectionName = "";
    protected array $attributes = [];

    

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


    public function __set($attribute, $value)
    {
        $this->attributes[$attribute] = $value;
    }


    public function __get($attribute)
    {
        return $this->data[$attribute] ?? null;
    }


    public function save(array $options = []) : \MongoDB\InsertOneResult | null
    {
        if($this->attributes) {
            return $this->collection->insert($this->attributes);
        }

        return null;
    }




}