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
     * The chain "template" business — the super admin's own (lowest-id)
     * business, matching the convention used elsewhere in the chain
     * (BusinessUtil, InvoiceAssignmentController). Its GLOBAL tag config
     * is the chain-wide default that every store inherits.
     */
    public static function templateBusinessId()
    {
        return Business::orderBy('id')->value('id');
    }

    /**
     * Resolve the effective tag configs for a store's location, in
     * priority order:
     *   1. Per-location override for this exact (business, location).
     *   2. This business's own global config (location_id = null).
     *   3. The TEMPLATE (super admin) business's global config.
     *
     * Tier 3 is what makes the feature work across the whole chain:
     * each store is its own business and normally has no config rows of
     * its own, so without this fallback the nightly auto-calc would skip
     * every store. The super admin configures tags once on the template
     * business and all stores inherit them, while still allowing a
     * per-store override via tier 1.
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

        $business_global = self::where('business_id', $business_id)
            ->whereNull('location_id')
            ->orderBy('sort_order')
            ->get();

        if ($business_global->isNotEmpty()) {
            return $business_global;
        }

        // Chain-wide fallback: the template business's global config.
        $template_business_id = self::templateBusinessId();
        if (! empty($template_business_id) && (int) $template_business_id !== (int) $business_id) {
            return self::where('business_id', $template_business_id)
                ->whereNull('location_id')
                ->orderBy('sort_order')
                ->get();
        }

        return collect();
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
