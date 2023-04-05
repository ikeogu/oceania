<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CarparklotSettingMode extends Model
{
    protected $guarded = ['id'];
    protected $table = "carparklot_setting_mode";

    public static function getCurrentCarparkMode()
    {
        $currentMode = CarparklotSettingMode::select('mode')->orderByDesc('id')->first();
        return !empty( $currentMode->mode ) ? $currentMode->mode : '';
    }

    public static function changeCurrentCarparkMode($mode)
    {   
        $currentMode = New CarparklotSettingMode();
        $currentMode->mode = $mode;
        $currentMode->created_at = now();
        $currentMode->save();
        return !empty( $currentMode->mode ) ? $currentMode->mode : '';
    }
}
