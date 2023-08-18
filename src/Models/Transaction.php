<?php

namespace Src\Models;

use Src\Abstracts\Model;


class Transaction extends Model 
{
    protected string $collectionName = "transactions";


    // Constructor to initialize properties
    public function __construct(
        int $ref, 
        string $description, 
        string $type
    )
    {
        parent::__construct();
        
        $this->ref = $ref;
        $this->description = $description;
        $this->type = $type;
    }

    
}