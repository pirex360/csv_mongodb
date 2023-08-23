<?php

namespace Src\Models;

use Src\Abstracts\Model;
use Src\Types\TransactionType;


class Transaction extends Model 
{
    public string $collectionName = "transactions";
    public array $fillable = ["ref", "description", "type"];

    
    // Constructor to initialize properties
    public function __construct(
        public int|array $ref = 0, 
        public string $description = "", 
        public string $type = TransactionType::JOURNAL
    )
    {

        parent::__construct();

        if (is_array($ref)) {
            $this->fillFromArray($ref);
        } else {
            [$this->ref, $this->description, $this->type] = [$ref, $description, $type];
        }

    }

    
}