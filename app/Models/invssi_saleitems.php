<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class invssi_saleitems extends Model
{
    use HasFactory;
	protected $guarded=['id'];
    protected $table = "invssi_saleitems";


   /**
     * Get the sale that owns the saleitems.
     */
    public function sale()
    {
        return $this->belongsTo(invssi_sale::class);
    }
}
