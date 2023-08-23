<?php

namespace Src\Models;


use Src\Abstracts\Model;


class Account extends Model 
{

    public string $collectionName = "accounts";
    public array $fillable = ["name", "code", "balance"];


    public function __construct(
        public string|array $name = "",
        public string $code = "",
        public float $balance = 0.0
    ) 
    {
       
        parent::__construct();

        if (is_array($name)) {
            $this->fillFromArray($name);
        } else {
            [$this->name, $this->code, $this->balance] = [$name, $code, $balance];
        }
        
    }


   
}