<?php 

namespace Src\Models;

use Src\Abstracts\Model;


class CompanyInfo extends Model 
{
    protected string $collectionName = "company_info";

    
    public function __construct(
        string $name = "", 
        string $email = "", 
        string $address = "", 
        string $type = ""
    )
    {
        parent::__construct();

        $this->name = $name;
        $this->email = $email;
        $this->address = $address;
        $this->type = $type;
    }

    


}