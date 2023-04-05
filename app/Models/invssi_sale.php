<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class invssi_sale extends Model
{
    use HasFactory;
	protected $guarded=['id'];
    protected $table = "invssi_sale";


    /**
     * Get the saleitems for the sale
     */
    public function saleitems()
    {
        return $this->hasMany(invssi_saleitems::class);
    }

}
