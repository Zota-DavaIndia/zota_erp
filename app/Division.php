<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Division extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    public static function forDropdown($business_id, $show_none = false)
    {
        $divisions = Division::where('business_id', $business_id)
                    ->orderBy('name', 'asc')
                    ->pluck('name', 'id');

        if ($show_none) {
            $divisions->prepend(__('lang_v1.none'), '');
        }

        return $divisions;
    }
}
