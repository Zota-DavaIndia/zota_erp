<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MovementTagConfig extends Model
{
    protected $guarded = ['id'];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function location()
    {
        return $this->belongsTo(BusinessLocation::class, 'location_id');
    }

    /**
     * Get tag configs for a specific location, falling back to global (location_id=null) if none set.
     */
    public static function getConfigsForLocation($business_id, $location_id)
    {
        $location_configs = self::where('business_id', $business_id)
            ->where('location_id', $location_id)
            ->orderBy('sort_order')
            ->get();

        if ($location_configs->isNotEmpty()) {
            return $location_configs;
        }

        return self::where('business_id', $business_id)
            ->whereNull('location_id')
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * Match monthly sales to the appropriate tag config.
     */
    public static function matchTag($configs, $monthly_sales)
    {
        foreach ($configs as $config) {
            $above_min = $monthly_sales >= $config->min_monthly_sales;
            $below_max = is_null($config->max_monthly_sales) || $monthly_sales < $config->max_monthly_sales;

            if ($above_min && $below_max) {
                return $config;
            }
        }

        return null;
    }

    /**
     * Seed default tag configs for a business (global, no location).
     */
    public static function seedDefaults($business_id, $created_by)
    {
        $defaults = [
            ['tag_code' => 'SFM', 'tag_name' => 'Super Fast Moving', 'min_monthly_sales' => 100, 'max_monthly_sales' => null, 'avg_days_for_min_stock' => 45, 'max_stock_buffer_percent' => 20, 'sort_order' => 1],
            ['tag_code' => 'FM', 'tag_name' => 'Fast Moving', 'min_monthly_sales' => 30, 'max_monthly_sales' => 100, 'avg_days_for_min_stock' => 30, 'max_stock_buffer_percent' => 20, 'sort_order' => 2],
            ['tag_code' => 'NFM', 'tag_name' => 'Non Fast Moving', 'min_monthly_sales' => 10, 'max_monthly_sales' => 30, 'avg_days_for_min_stock' => 10, 'max_stock_buffer_percent' => 20, 'sort_order' => 3],
            ['tag_code' => 'SM', 'tag_name' => 'Slow Moving', 'min_monthly_sales' => 0, 'max_monthly_sales' => 10, 'avg_days_for_min_stock' => 3, 'max_stock_buffer_percent' => 20, 'sort_order' => 4],
        ];

        foreach ($defaults as $d) {
            self::create(array_merge($d, [
                'business_id' => $business_id,
                'location_id' => null,
                'created_by' => $created_by,
            ]));
        }
    }
}
