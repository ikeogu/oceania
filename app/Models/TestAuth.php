<?php

namespace App\Models;

use App\Events\PumpAuthorized;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestAuth extends Model
{
    use HasFactory;
    protected $table = "testauth";
    protected $fillable = ["pump_no", "dose"];
    protected $dispatchesEvents = [
		'created'=> PumpAuthorized::class,
	];
}
