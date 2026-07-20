<?php

namespace App\Console\Commands;

use App\Business;
use App\BusinessLocation;
use App\MovementTagConfig;
use App\VariationLocationDetails;
use Carbon\Carbon;
use DB;
use Illuminate\Console\Command;

class UpdateMovementTags extends Command
{
    protected $signature = 'pos:updateMovementTags {--business_id= : Process a specific business only}';

    protected $description = 'Auto-calculate movement tags and min/max stock based on 90 days sales data';

    public function handle()
    {
        $business_query = Business::where('is_active', 1);

        if ($this->option('business_id')) {
            $business_query->where('id', $this->option('business_id'));
        }

        $business_query->chunkById(50, function ($businesses) {
            foreach ($businesses as $business) {
                $this->processBusinessLocations($business);
            }
        });

        $this->info('Movement tags and min/max stock updated successfully.');
    }

    private function processBusinessLocations(Business $business)
    {
        $locations = BusinessLocation::where('business_id', $business->id)->get();

        foreach ($locations as $location) {
            $configs = MovementTagConfig::getConfigsForLocation($business->id, $location->id);

            if ($configs->isEmpty()) {
                continue;
            }

            $this->processLocation($business->id, $location->id, $configs);
        }
    }

    private function processLocation($business_id, $location_id, $configs)
    {
        $end_date = Carbon::now();
        $start_date = Carbon::now()->subDays(90);

        // Get total sold quantity per variation at this location over 90 days
        $sales_data = DB::table('transaction_sell_lines as tsl')
            ->join('transactions as t', 'tsl.transaction_id', '=', 't.id')
            ->where('t.business_id', $business_id)
            ->where('t.location_id', $location_id)
            ->where('t.type', 'sell')
            ->where('t.status', 'final')
            ->whereBetween('t.transaction_date', [$start_date, $end_date])
            ->select(
                'tsl.product_id',
                'tsl.variation_id',
                DB::raw('SUM(tsl.quantity - tsl.quantity_returned) as total_sold')
            )
            ->groupBy('tsl.product_id', 'tsl.variation_id')
            ->get()
            ->keyBy(function ($item) {
                return $item->product_id . '_' . $item->variation_id;
            });

        // Process all variation_location_details for this location
        VariationLocationDetails::where('location_id', $location_id)
            ->chunkById(200, function ($vld_records) use ($sales_data, $configs) {
                foreach ($vld_records as $vld) {
                    $key = $vld->product_id . '_' . $vld->variation_id;
                    $total_sold_90 = isset($sales_data[$key]) ? (float) $sales_data[$key]->total_sold : 0;

                    $daily_avg = $total_sold_90 / 90;
                    $monthly_avg = $daily_avg * 30;

                    $matched = MovementTagConfig::matchTag($configs, $monthly_avg);

                    if ($matched) {
                        $min_stock = (int) round($daily_avg * $matched->avg_days_for_min_stock);
                        $max_stock = (int) round($min_stock + ($min_stock * $matched->max_stock_buffer_percent / 100));

                        $vld->movement_tag = $matched->tag_code;
                        $vld->min_quantity = $min_stock;
                        $vld->max_quantity = $max_stock;
                        $vld->min_max_source = 'auto';
                        $vld->last_auto_update_at = Carbon::now();
                        $vld->save();
                    }
                }
            });
    }
}
