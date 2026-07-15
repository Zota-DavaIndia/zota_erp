<?php

namespace Modules\Superadmin\Http\Controllers;

use App\Brands;
use App\Business;
use App\Category;
use App\Product;
use App\ProductVariation;
use App\Unit;
use App\Variation;
use Illuminate\Routing\Controller;

class SuperadminProductController extends Controller
{
    /**
     * Sync a newly created master product to all active businesses.
     * Called from ProductController::store() when superadmin creates a product.
     */
    public static function syncMasterProductToAllBusinesses(Product $master_product)
    {
        $controller = app(self::class);
        $businesses = Business::where('is_active', 1)->get();

        foreach ($businesses as $business) {
            $controller->syncMasterProductToBusiness($master_product, $business);
        }
    }

    /**
     * Sync all master products to a specific business.
     * Called when a new business is registered.
     */
    public static function syncAllProductsToBusiness(Business $business)
    {
        $controller = app(self::class);

        $master_products = Product::where('is_master_product', 1)
            ->with(['variations', 'product_variations'])
            ->get();

        foreach ($master_products as $master_product) {
            $controller->syncMasterProductToBusiness($master_product, $business);
        }
    }

    /**
     * Sync a single master product to a specific business.
     */
    public function syncMasterProductToBusiness(Product $master_product, Business $business)
    {
        // Don't sync to the master product's own business
        if ($business->id == $master_product->business_id) {
            return null;
        }

        // Check if already synced
        $existing = Product::where('master_product_id', $master_product->id)
            ->where('business_id', $business->id)
            ->first();

        if ($existing) {
            return $existing;
        }

        $created_by = $business->owner_id ?? $business->created_by ?? 1;

        // Resolve unit for this business
        $unit = Unit::find($master_product->unit_id);
        $unit_name = $unit ? $unit->actual_name : 'Pieces';
        $unit_short = $unit ? $unit->short_name : 'Pc(s)';
        $unit_id = $this->resolveUnitForBusiness($business->id, $unit_name, $unit_short, $created_by);

        // Resolve category
        $category_id = null;
        if (!empty($master_product->category_id)) {
            $cat = Category::find($master_product->category_id);
            if ($cat) {
                $category_id = $this->resolveCategoryForBusiness($business->id, $cat->name, $created_by);
            }
        }

        // Resolve brand
        $brand_id = null;
        if (!empty($master_product->brand_id)) {
            $brand = Brands::find($master_product->brand_id);
            if ($brand) {
                $brand_id = $this->resolveBrandForBusiness($business->id, $brand->name, $created_by);
            }
        }

        // Create the business product copy
        $product = Product::create([
            'master_product_id' => $master_product->id,
            'name' => $master_product->name,
            'business_id' => $business->id,
            'type' => $master_product->type,
            'unit_id' => $unit_id,
            'category_id' => $category_id,
            'brand_id' => $brand_id,
            'sku' => $master_product->sku . '-' . $business->id,
            'barcode_type' => $master_product->barcode_type,
            'image' => $master_product->image,
            'product_description' => $master_product->product_description,
            'weight' => $master_product->weight,
            'enable_stock' => $master_product->enable_stock,
            'alert_quantity' => $master_product->alert_quantity,
            'not_for_selling' => $master_product->not_for_selling,
            'tax_type' => $master_product->tax_type,
            'is_inactive' => $master_product->is_inactive ?? 0,
            'created_by' => $created_by,
        ]);

        // Copy variations
        $master_variations = Variation::where('product_id', $master_product->id)->get();
        foreach ($master_variations as $mv) {
            $master_pv = ProductVariation::find($mv->product_variation_id);

            $pv = ProductVariation::create([
                'name' => $master_pv ? $master_pv->name : 'DUMMY',
                'product_id' => $product->id,
                'is_dummy' => $master_pv ? $master_pv->is_dummy : 1,
            ]);

            Variation::create([
                'name' => $mv->name,
                'product_id' => $product->id,
                'product_variation_id' => $pv->id,
                'sub_sku' => $mv->sub_sku . '-' . $business->id,
                'default_purchase_price' => $mv->default_purchase_price,
                'dpp_inc_tax' => $mv->dpp_inc_tax,
                'profit_percent' => $mv->profit_percent,
                'default_sell_price' => $mv->default_sell_price,
                'sell_price_inc_tax' => $mv->sell_price_inc_tax,
                // Pointer back to the master variation row for reliable matching
                'master_variation_id' => $mv->id,
            ]);
        }

        // Assign product to all business locations
        $locations = $business->locations()->pluck('id')->toArray();
        if (!empty($locations)) {
            $product->product_locations()->sync($locations);
        }

        return $product;
    }

    /**
     * Sync updates to a master product to all business copies.
     * Called from ProductController::update() when superadmin edits a master product.
     */
    public static function syncMasterProductUpdateToBusinesses(Product $master_product)
    {
        $controller = app(self::class);
        $business_products = Product::where('master_product_id', $master_product->id)->get();

        // Resolve master unit/category/brand names
        $unit = Unit::find($master_product->unit_id);
        $unit_name = $unit ? $unit->actual_name : 'Pieces';
        $unit_short = $unit ? $unit->short_name : 'Pc(s)';

        $cat = !empty($master_product->category_id) ? Category::find($master_product->category_id) : null;
        $brand = !empty($master_product->brand_id) ? Brands::find($master_product->brand_id) : null;

        foreach ($business_products as $bp) {
            $created_by = $bp->created_by;

            $unit_id = $controller->resolveUnitForBusiness($bp->business_id, $unit_name, $unit_short, $created_by);

            $category_id = null;
            if ($cat) {
                $category_id = $controller->resolveCategoryForBusiness($bp->business_id, $cat->name, $created_by);
            }

            $brand_id = null;
            if ($brand) {
                $brand_id = $controller->resolveBrandForBusiness($bp->business_id, $brand->name, $created_by);
            }

            $bp->update([
                'name' => $master_product->name,
                'barcode_type' => $master_product->barcode_type,
                'image' => $master_product->image,
                'product_description' => $master_product->product_description,
                'weight' => $master_product->weight,
                'enable_stock' => $master_product->enable_stock,
                'alert_quantity' => $master_product->alert_quantity,
                'not_for_selling' => $master_product->not_for_selling,
                'is_inactive' => $master_product->is_inactive ?? 0,
                'unit_id' => $unit_id,
                'category_id' => $category_id,
                'brand_id' => $brand_id,
            ]);

            // Sync single product variation pricing
            if ($master_product->type == 'single') {
                $master_var = Variation::where('product_id', $master_product->id)->first();
                if ($master_var) {
                    $biz_var = Variation::where('product_id', $bp->id)->first();
                    if ($biz_var) {
                        $biz_var->update([
                            'default_purchase_price' => $master_var->default_purchase_price,
                            'dpp_inc_tax' => $master_var->dpp_inc_tax,
                            'profit_percent' => $master_var->profit_percent,
                            'default_sell_price' => $master_var->default_sell_price,
                            'sell_price_inc_tax' => $master_var->sell_price_inc_tax,
                        ]);
                    }
                }
            } else {
                // Variable: sync variation names, prices; add new, remove deleted.
                // Match business variations to master variations by master_variation_id
                // (reliable), with name as a fallback for legacy rows synced before
                // the master_variation_id column existed.
                $master_vars = Variation::where('product_id', $master_product->id)->get();
                $biz_vars = Variation::where('product_id', $bp->id)->get();

                foreach ($master_vars as $mv) {
                    // Prefer matching by master_variation_id
                    $biz_var = $biz_vars->firstWhere('master_variation_id', $mv->id);

                    // Fallback: name match (for legacy rows where master_variation_id is null)
                    if (!$biz_var) {
                        $biz_var = $biz_vars->firstWhere('name', $mv->name);
                    }

                    if ($biz_var) {
                        $biz_var->update([
                            'default_purchase_price' => $mv->default_purchase_price,
                            'dpp_inc_tax' => $mv->dpp_inc_tax,
                            'profit_percent' => $mv->profit_percent,
                            'default_sell_price' => $mv->default_sell_price,
                            'sell_price_inc_tax' => $mv->sell_price_inc_tax,
                        ]);
                    } else {
                        $pv = ProductVariation::create([
                            'name' => $mv->name,
                            'product_id' => $bp->id,
                            'is_dummy' => 0,
                        ]);
                        Variation::create([
                            'name' => $mv->name,
                            'product_id' => $bp->id,
                            'product_variation_id' => $pv->id,
                            'sub_sku' => $mv->sub_sku . '-' . $bp->business_id,
                            'default_purchase_price' => $mv->default_purchase_price,
                            'dpp_inc_tax' => $mv->dpp_inc_tax,
                            'profit_percent' => $mv->profit_percent,
                            'default_sell_price' => $mv->default_sell_price,
                            'sell_price_inc_tax' => $mv->sell_price_inc_tax,
                            'master_variation_id' => $mv->id,
                        ]);
                    }
                }

                // Remove variations deleted from master.
                // Only remove synced copies (have master_variation_id) to avoid
                // touching business-owned variations. For legacy rows (no
                // master_variation_id), fall back to name-based pruning.
                $master_var_ids = $master_vars->pluck('id')->toArray();
                $master_var_names = $master_vars->pluck('name')->toArray();

                Variation::where('product_id', $bp->id)
                    ->where(function ($q) use ($master_var_ids, $master_var_names) {
                        $q->whereIn('master_variation_id', $master_var_ids)
                          ->orWhere(function ($q2) use ($master_var_ids, $master_var_names) {
                              $q2->whereNull('master_variation_id')
                                 ->whereNotIn('name', $master_var_names);
                          });
                    })
                    ->delete();
            }
        }
    }

    /**
     * Delete all synced business copies of a master product.
     * Called from ProductController::destroy() when superadmin deletes a master product.
     */
    public static function deleteSyncedBusinessProducts($master_product_id)
    {
        $business_products = Product::where('master_product_id', $master_product_id)->get();

        foreach ($business_products as $bp) {
            \App\VariationLocationDetails::where('product_id', $bp->id)->delete();
            $bp->product_locations()->detach();
            Variation::where('product_id', $bp->id)->delete();
            ProductVariation::where('product_id', $bp->id)->delete();
            $bp->delete();
        }
    }

    /**
     * Find or create a unit in a business by name.
     */
    private function resolveUnitForBusiness($business_id, $unit_name, $short_name, $created_by)
    {
        $unit = Unit::where('business_id', $business_id)
            ->where('actual_name', $unit_name)
            ->first();

        if (!$unit) {
            $unit = Unit::create([
                'business_id' => $business_id,
                'actual_name' => $unit_name,
                'short_name' => $short_name,
                'allow_decimal' => 0,
                'created_by' => $created_by,
            ]);
        }

        return $unit->id;
    }

    /**
     * Find or create a category in a business by name.
     */
    private function resolveCategoryForBusiness($business_id, $category_name, $created_by)
    {
        $category = Category::where('business_id', $business_id)
            ->where('name', $category_name)
            ->where('category_type', 'product')
            ->where(function ($q) {
                $q->whereNull('parent_id')->orWhere('parent_id', 0);
            })
            ->first();

        if (!$category) {
            $category = Category::create([
                'business_id' => $business_id,
                'name' => $category_name,
                'short_code' => strtoupper(substr(preg_replace('/[^a-zA-Z0-9]/', '', $category_name), 0, 4)),
                'parent_id' => 0,
                'created_by' => $created_by,
                'category_type' => 'product',
            ]);
        }

        return $category->id;
    }

    /**
     * Find or create a brand in a business by name.
     */
    private function resolveBrandForBusiness($business_id, $brand_name, $created_by)
    {
        $brand = Brands::where('business_id', $business_id)
            ->where('name', $brand_name)
            ->first();

        if (!$brand) {
            $brand = Brands::create([
                'business_id' => $business_id,
                'name' => $brand_name,
                'created_by' => $created_by,
            ]);
        }

        return $brand->id;
    }

    /**
     * Display a listing of all master products with their synced-copy counts.
     * UI for the Superadmin Master Products nav entry.
     */
    public function index()
    {
        $master_products = Product::withCount('masterProductCopies')
            ->where('is_master_product', 1)
            ->orderBy('id', 'desc')
            ->paginate(20);

        return view('superadmin::master_products.index', compact('master_products'));
    }

    /**
     * Show one master product and the list of businesses that have a synced copy.
     */
    public function show($id)
    {
        $master_product = Product::with(['variations', 'category', 'brand', 'unit'])
            ->where('is_master_product', 1)
            ->where('id', $id)
            ->firstOrFail();

        // Pull all synced copies with their business info
        $synced_copies = Product::with('business')
            ->where('master_product_id', $master_product->id)
            ->get();

        return view('superadmin::master_products.show', compact('master_product', 'synced_copies'));
    }
}
