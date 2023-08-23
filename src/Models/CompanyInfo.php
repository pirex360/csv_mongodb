<?php 

namespace Src\Models;

use Src\Abstracts\Model;


class CompanyInfo extends Model 
{
    public string $collectionName = "company_info";
    public array $fillable = ["name", "email", "address", "type"];
    

    public function __construct(
        public string|array $name = "", 
        public string $email = "", 
        public string $address = "", 
        public string $type = ""
    )
    {
        parent::__construct();

        if (is_array($name)) {
            $this->fillFromArray($name);
        } else {
            [$this->name, $this->email, $this->address, $this->type] = [$name, $name, $email, $address, $type];
        }

    }

    


}