<?php

namespace Modules\Superadmin\Http\Controllers;

use App\Business;
use App\BusinessLocation;
use App\MovementTagConfig;
use App\VariationLocationDetails;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class MovementTagController extends Controller
{
    /**
     * Show tag configs page with global and per-location configs.
     */
    public function index()
    {
        if (! auth()->user()->can('superadmin')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = session('user.business_id');

        $global_configs = MovementTagConfig::where('business_id', $business_id)
            ->whereNull('location_id')
            ->orderBy('sort_order')
            ->get();

        // Seed defaults if none exist
        if ($global_configs->isEmpty()) {
            MovementTagConfig::seedDefaults($business_id, auth()->user()->id);
            $global_configs = MovementTagConfig::where('business_id', $business_id)
                ->whereNull('location_id')
                ->orderBy('sort_order')
                ->get();
        }

        $businesses = Business::where('is_active', 1)->pluck('name', 'id');

        $locations = BusinessLocation::join('business', 'business_locations.business_id', '=', 'business.id')
            ->where('business.is_active', 1)
            ->select(
                'business_locations.id',
                'business_locations.name as location_name',
                'business.name as business_name',
                'business_locations.business_id'
            )
            ->get();

        // Per-location overrides are stored under each store's OWN
        // business_id (a location is globally unique), so load them
        // across the whole chain rather than only the super admin's
        // business — otherwise the saved overrides wouldn't display.
        $location_configs = MovementTagConfig::whereNotNull('location_id')
            ->orderBy('location_id')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('location_id');

        return view('superadmin::movement_tags.index', compact(
            'global_configs',
            'businesses',
            'locations',
            'location_configs'
        ));
    }

    /**
     * Save global tag configs (apply to all stores).
     */
    public function saveGlobal(Request $request)
    {
        if (! auth()->user()->can('superadmin')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = session('user.business_id');
            $tags = $request->input('tags', []);

            DB::beginTransaction();

            // Delete existing global configs
            MovementTagConfig::where('business_id', $business_id)
                ->whereNull('location_id')
                ->delete();

            foreach ($tags as $index => $tag) {
                if (empty($tag['tag_code']) || empty($tag['tag_name'])) {
                    continue;
                }

                MovementTagConfig::create([
                    'business_id' => $business_id,
                    'location_id' => null,
                    'tag_code' => $tag['tag_code'],
                    'tag_name' => $tag['tag_name'],
                    'min_monthly_sales' => $tag['min_monthly_sales'] ?? 0,
                    'max_monthly_sales' => ! empty($tag['max_monthly_sales']) ? $tag['max_monthly_sales'] : null,
                    'avg_days_for_min_stock' => $tag['avg_days_for_min_stock'] ?? 0,
                    'max_stock_buffer_percent' => $tag['max_stock_buffer_percent'] ?? 20,
                    'sort_order' => $index + 1,
                    'created_by' => auth()->user()->id,
                ]);
            }

            DB::commit();

            return redirect()->back()->with('status', ['success' => 1, 'msg' => __('superadmin::lang.movement_tags_saved')]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

            return redirect()->back()->with('error', __('messages.something_went_wrong'));
        }
    }

    /**
     * Save location-specific tag configs.
     */
    public function saveLocation(Request $request)
    {
        if (! auth()->user()->can('superadmin')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $location_id = $request->input('location_id');
            $tags = $request->input('tags', []);

            if (empty($location_id)) {
                return redirect()->back()->with('error', __('messages.something_went_wrong'));
            }

            // A per-location override must be stored under the business
            // that actually OWNS the location — each store is its own
            // business. Storing it under the super admin's session
            // business (the old behaviour) orphaned the override, since
            // the nightly auto-calc resolves config by the store's own
            // business_id.
            $business_id = \App\BusinessLocation::where('id', $location_id)->value('business_id');
            if (empty($business_id)) {
                return redirect()->back()->with('error', __('messages.something_went_wrong'));
            }

            DB::beginTransaction();

            // Delete existing configs for this location
            MovementTagConfig::where('business_id', $business_id)
                ->where('location_id', $location_id)
                ->delete();

            foreach ($tags as $index => $tag) {
                if (empty($tag['tag_code']) || empty($tag['tag_name'])) {
                    continue;
                }

                MovementTagConfig::create([
                    'business_id' => $business_id,
                    'location_id' => $location_id,
                    'tag_code' => $tag['tag_code'],
                    'tag_name' => $tag['tag_name'],
                    'min_monthly_sales' => $tag['min_monthly_sales'] ?? 0,
                    'max_monthly_sales' => ! empty($tag['max_monthly_sales']) ? $tag['max_monthly_sales'] : null,
                    'avg_days_for_min_stock' => $tag['avg_days_for_min_stock'] ?? 0,
                    'max_stock_buffer_percent' => $tag['max_stock_buffer_percent'] ?? 20,
                    'sort_order' => $index + 1,
                    'created_by' => auth()->user()->id,
                ]);
            }

            DB::commit();

            return redirect()->back()->with('status', ['success' => 1, 'msg' => __('superadmin::lang.movement_tags_saved')]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

            return redirect()->back()->with('error', __('messages.something_went_wrong'));
        }
    }

    /**
     * Remove location-specific override (falls back to global).
     */
    public function removeLocationOverride(Request $request)
    {
        if (! auth()->user()->can('superadmin')) {
            abort(403, 'Unauthorized action.');
        }

        $location_id = $request->input('location_id');

        // Match the ownership rule used when saving the override.
        $business_id = \App\BusinessLocation::where('id', $location_id)->value('business_id');

        MovementTagConfig::where('business_id', $business_id)
            ->where('location_id', $location_id)
            ->delete();

        return redirect()->back()->with('status', ['success' => 1, 'msg' => __('superadmin::lang.location_override_removed')]);
    }

    /**
     * Show min/max stock settings page for all products across stores.
     */
    public function stockSettings(Request $request)
    {
        if (! auth()->user()->can('superadmin')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = session('user.business_id');

        $locations = BusinessLocation::join('business', 'business_locations.business_id', '=', 'business.id')
            ->where('business.is_active', 1)
            ->select(
                'business_locations.id',
                'business_locations.name as location_name',
                'business.name as business_name',
                'business_locations.business_id'
            )
            ->get();

        $selected_location = $request->input('location_id', $locations->first()->id ?? null);

        $stock_data = [];
        if ($selected_location) {
            $stock_data = VariationLocationDetails::join('products', 'variation_location_details.product_id', '=', 'products.id')
                ->join('variations', 'variation_location_details.variation_id', '=', 'variations.id')
                ->where('variation_location_details.location_id', $selected_location)
                ->select(
                    'variation_location_details.*',
                    'products.name as product_name',
                    'products.sku as product_sku',
                    'variations.name as variation_name',
                    'variations.sub_sku'
                )
                ->orderBy('products.name')
                ->get();
        }

        return view('superadmin::movement_tags.stock_settings', compact(
            'locations',
            'selected_location',
            'stock_data'
        ));
    }

    /**
     * Save manual min/max for individual product-location records.
     */
    public function saveStockSettings(Request $request)
    {
        if (! auth()->user()->can('superadmin')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $stocks = $request->input('stocks', []);

            DB::beginTransaction();

            $allowed_tags = ['SFM', 'FM', 'NFM', 'SM'];

            foreach ($stocks as $vld_id => $values) {
                $vld = VariationLocationDetails::find($vld_id);
                if ($vld) {
                    $vld->min_quantity = $values['min_quantity'] ?? 0;
                    $vld->max_quantity = $values['max_quantity'] ?? 0;

                    // Initial (manual) movement tag set by the super admin.
                    // Empty selection clears it; invalid values are ignored.
                    $tag = $values['movement_tag'] ?? null;
                    $vld->movement_tag = in_array($tag, $allowed_tags, true) ? $tag : null;

                    $vld->min_max_source = 'manual';
                    $vld->save();
                }
            }

            DB::commit();

            return redirect()->back()->with('status', ['success' => 1, 'msg' => __('superadmin::lang.stock_settings_saved')]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

            return redirect()->back()->with('error', __('messages.something_went_wrong'));
        }
    }

    /**
     * Manually trigger the auto-calculation command.
     */
    public function runAutoCalculation()
    {
        if (! auth()->user()->can('superadmin')) {
            abort(403, 'Unauthorized action.');
        }

        Artisan::call('pos:updateMovementTags');

        return redirect()->back()->with('status', ['success' => 1, 'msg' => __('superadmin::lang.auto_calculation_triggered')]);
    }
}
