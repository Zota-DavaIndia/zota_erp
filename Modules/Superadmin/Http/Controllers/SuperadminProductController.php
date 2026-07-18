<?php

namespace Modules\Superadmin\Http\Controllers;

use App\Brands;
use App\Business;
use App\Category;
use App\Composition;
use App\Product;
use App\ProductVariation;
use App\TaxRate;
use App\Unit;
use App\Variation;
use App\VariationLocationDetails;
use App\Warranty;
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

        // Resolve unit for this business (full definition, hierarchy included)
        $unit = Unit::find($master_product->unit_id);
        $unit_id = $this->resolveUnitForBusiness($business->id, $unit, $created_by, 'Pieces', 'Pc(s)');

        // Secondary unit (independent of the sub-unit hierarchy)
        $secondary_unit_id = null;
        if (!empty($master_product->secondary_unit_id)) {
            $su = Unit::find($master_product->secondary_unit_id);
            $secondary_unit_id = $this->resolveUnitForBusiness($business->id, $su, $created_by);
        }

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

        // Resolve sub-units (sub_unit_ids, sell/purchase whitelists and
        // default sell/purchase units) by remapping the master unit
        // ids to the corresponding unit ids in this business. Without
        // this remap the cloned product would point at units that
        // don't exist in the store and the POS row would never show
        // the sub-unit dropdown.
        $sub_unit_ids = $this->remapUnitIdList($business->id, $master_product->sub_unit_ids, $created_by);
        $sell_sub_unit_ids = $this->remapUnitIdList($business->id, $master_product->sell_sub_unit_ids, $created_by);
        $purchase_sub_unit_ids = $this->remapUnitIdList($business->id, $master_product->purchase_sub_unit_ids, $created_by);

        $default_sell_sub_unit_id = null;
        if (!empty($master_product->default_sell_sub_unit_id)) {
            $ms = Unit::find($master_product->default_sell_sub_unit_id);
            $default_sell_sub_unit_id = $this->resolveUnitForBusiness($business->id, $ms, $created_by);
        }
        $default_purchase_sub_unit_id = null;
        if (!empty($master_product->default_purchase_sub_unit_id)) {
            $mp = Unit::find($master_product->default_purchase_sub_unit_id);
            $default_purchase_sub_unit_id = $this->resolveUnitForBusiness($business->id, $mp, $created_by);
        }

        // Create the business product copy
        $product = Product::create([
            'master_product_id' => $master_product->id,
            'name' => $master_product->name,
            'business_id' => $business->id,
            'type' => $master_product->type,
            'unit_id' => $unit_id,
            'secondary_unit_id' => $secondary_unit_id,
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
            'sub_unit_ids' => $sub_unit_ids,
            'sell_sub_unit_ids' => $sell_sub_unit_ids ?: null,
            'purchase_sub_unit_ids' => $purchase_sub_unit_ids ?: null,
            'default_sell_sub_unit_id' => $default_sell_sub_unit_id,
            'default_purchase_sub_unit_id' => $default_purchase_sub_unit_id,
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

            // Initialise variation_location_details for every
            // (variation, location) pair with quantity 0. Without
            // these rows the product appears in the catalogue and
            // POS but its Current Stock is NULL and the
            // leftJoin/whereHas filters on /products may exclude
            // it depending on the user's permitted_locations
            // scope. Keeping qty at 0 reflects "not yet stocked"
            // which is the correct starting state for a freshly
            // cloned master product.
            foreach ($product->variations()->get() as $cv) {
                foreach ($locations as $loc_id) {
                    VariationLocationDetails::firstOrCreate(
                        [
                            'variation_id' => $cv->id,
                            'location_id'  => $loc_id,
                        ],
                        [
                            'product_id'          => $product->id,
                            'product_variation_id' => $cv->product_variation_id,
                            'qty_available'       => 0,
                        ]
                    );
                }
            }
        }

        return $product;
    }

    /**
     * Sync updates to a master product to all business copies.
     * Called from ProductController::update() when superadmin edits a master product.
     */
    /**
     * Sync a product edit (by superadmin) to all business copies.
     *
     * The passed product may be the master itself OR a clone. When it
     * is a clone, we first mirror the clone's data back to the master
     * so the master stays authoritative, then push the master to
     * every other clone. This way, the superadmin only needs to edit
     * the product from whichever business context is convenient and
     * the change still propagates everywhere.
     */
    public static function syncMasterProductUpdateToBusinesses(Product $edited_product)
    {
        $controller = app(self::class);

        // 1) Resolve the master product
        if (!empty($edited_product->master_product_id)) {
            // Edited product is a clone — pull the master forward.
            $master = Product::find($edited_product->master_product_id);
            if (!$master) {
                return;
            }
            // Mirror the clone's full state into the master so the
            // master is the latest source of truth. We do not
            // overwrite business_id, sku (master has its own), or
            // is_master_product/master_product_id markers.
            $controller->mirrorProductFields($master, $edited_product);
            $master->save();
        } else {
            $master = $edited_product;
        }

        $business_products = Product::where('master_product_id', $master->id)->get();

        // 2) Resolve master unit/category/brand/tax/composition/warranty
        $unit = Unit::find($master->unit_id);

        $cat = !empty($master->category_id) ? Category::find($master->category_id) : null;
        $brand = !empty($master->brand_id) ? Brands::find($master->brand_id) : null;
        $tax = !empty($master->tax) ? TaxRate::find($master->tax) : null;
        $composition = !empty($master->composition_id) ? Composition::find($master->composition_id) : null;
        $warranty = !empty($master->warranty_id) ? Warranty::find($master->warranty_id) : null;
        $secondary_unit = !empty($master->secondary_unit_id) ? Unit::find($master->secondary_unit_id) : null;

        foreach ($business_products as $bp) {
            $created_by = $bp->created_by;

            $unit_id = $controller->resolveUnitForBusiness($bp->business_id, $unit, $created_by, 'Pieces', 'Pc(s)');

            $category_id = null;
            if ($cat) {
                $category_id = $controller->resolveCategoryForBusiness($bp->business_id, $cat->name, $created_by);
            }

            $brand_id = null;
            if ($brand) {
                $brand_id = $controller->resolveBrandForBusiness($bp->business_id, $brand->name, $created_by);
            }

            $tax_id = null;
            if ($tax) {
                $tax_id = $controller->resolveTaxForBusiness($bp->business_id, $tax->name, $tax->amount);
            }

            $composition_id = null;
            if ($composition) {
                $composition_id = $controller->resolveCompositionForBusiness($bp->business_id, $composition->name);
            }

            $warranty_id = null;
            if ($warranty) {
                $warranty_id = $controller->resolveWarrantyForBusiness(
                    $bp->business_id,
                    $warranty->name,
                    $warranty->duration,
                    $warranty->duration_type
                );
            }

            $secondary_unit_id = null;
            if ($secondary_unit) {
                $secondary_unit_id = $controller->resolveUnitForBusiness($bp->business_id, $secondary_unit, $created_by);
            }

            // Resolve sub-units for this business by remapping the
            // master unit ids to the corresponding unit ids in this
            // business (same logic as on create).
            $sub_unit_ids = $controller->remapUnitIdList($bp->business_id, $master->sub_unit_ids, $created_by);
            $sell_sub_unit_ids = $controller->remapUnitIdList($bp->business_id, $master->sell_sub_unit_ids, $created_by);
            $purchase_sub_unit_ids = $controller->remapUnitIdList($bp->business_id, $master->purchase_sub_unit_ids, $created_by);

            $default_sell_sub_unit_id = null;
            if (!empty($master->default_sell_sub_unit_id)) {
                $ms = Unit::find($master->default_sell_sub_unit_id);
                $default_sell_sub_unit_id = $controller->resolveUnitForBusiness($bp->business_id, $ms, $created_by);
            }
            $default_purchase_sub_unit_id = null;
            if (!empty($master->default_purchase_sub_unit_id)) {
                $mp = Unit::find($master->default_purchase_sub_unit_id);
                $default_purchase_sub_unit_id = $controller->resolveUnitForBusiness($bp->business_id, $mp, $created_by);
            }

            $bp->update([
                'name' => $master->name,
                'barcode_type' => $master->barcode_type,
                'image' => $master->image,
                'product_description' => $master->product_description,
                'weight' => $master->weight,
                'enable_stock' => $master->enable_stock,
                'alert_quantity' => $master->alert_quantity,
                'not_for_selling' => $master->not_for_selling,
                'is_inactive' => $master->is_inactive ?? 0,
                'unit_id' => $unit_id,
                'category_id' => $category_id,
                'sub_category_id' => null, // sub_category is business-specific
                'brand_id' => $brand_id,
                'tax' => $tax_id,
                'tax_type' => $master->tax_type,
                'composition_id' => $composition_id,
                'secondary_unit_id' => $secondary_unit_id,
                'warranty_id' => $warranty_id,
                'expiry_period_type' => $master->expiry_period_type,
                'expiry_period' => $master->expiry_period,
                'enable_sr_no' => $master->enable_sr_no,
                'preparation_time_in_minutes' => $master->preparation_time_in_minutes,
                'product_custom_field1' => $master->product_custom_field1,
                'product_custom_field2' => $master->product_custom_field2,
                'product_custom_field3' => $master->product_custom_field3,
                'product_custom_field4' => $master->product_custom_field4,
                'product_custom_field5' => $master->product_custom_field5,
                'product_custom_field6' => $master->product_custom_field6,
                'product_custom_field7' => $master->product_custom_field7,
                'product_custom_field8' => $master->product_custom_field8,
                'product_custom_field9' => $master->product_custom_field9,
                'product_custom_field10' => $master->product_custom_field10,
                'product_custom_field11' => $master->product_custom_field11,
                'product_custom_field12' => $master->product_custom_field12,
                'product_custom_field13' => $master->product_custom_field13,
                'product_custom_field14' => $master->product_custom_field14,
                'product_custom_field15' => $master->product_custom_field15,
                'product_custom_field16' => $master->product_custom_field16,
                'product_custom_field17' => $master->product_custom_field17,
                'product_custom_field18' => $master->product_custom_field18,
                'product_custom_field19' => $master->product_custom_field19,
                'product_custom_field20' => $master->product_custom_field20,
                'sub_unit_ids' => $sub_unit_ids,
                'sell_sub_unit_ids' => $sell_sub_unit_ids ?: null,
                'purchase_sub_unit_ids' => $purchase_sub_unit_ids ?: null,
                'default_sell_sub_unit_id' => $default_sell_sub_unit_id,
                'default_purchase_sub_unit_id' => $default_purchase_sub_unit_id,
            ]);

            // Sync variations (single + variable)
            $controller->syncVariationsFromMaster($master, $bp);
        }
    }

    /**
     * Mirror the editable fields from $source into $target. Used to
     * write a clone's latest state back to the master so the master
     * stays the canonical record. SKU and business_id are left
     * alone because they are not business-shared.
     *
     * Foreign keys (unit_id, brand_id, etc.) on $source point at
     * rows in the source's own business. We resolve the equivalent
     * row in the target's business by name (and amount/duration for
     * tax/warranty) before assigning, so the master always points
     * at rows that exist in its own business.
     */
    private function mirrorProductFields(Product $target, Product $source)
    {
        $target_business_id = $target->business_id;
        $created_by = $target->created_by ?? ($target->business ? $target->business->owner_id : 1);

        // unit_id
        if (!empty($source->unit_id)) {
            $u = Unit::find($source->unit_id);
            if ($u) {
                $target->unit_id = $this->resolveUnitForBusiness($target_business_id, $u, $created_by);
            }
        } else {
            $target->unit_id = null;
        }

        // secondary_unit_id
        if (!empty($source->secondary_unit_id)) {
            $u = Unit::find($source->secondary_unit_id);
            $target->secondary_unit_id = $u
                ? $this->resolveUnitForBusiness($target_business_id, $u, $created_by)
                : null;
        } else {
            $target->secondary_unit_id = null;
        }

        // category_id
        if (!empty($source->category_id)) {
            $c = Category::find($source->category_id);
            $target->category_id = $c
                ? $this->resolveCategoryForBusiness($target_business_id, $c->name, $created_by)
                : null;
        } else {
            $target->category_id = null;
        }

        // brand_id
        if (!empty($source->brand_id)) {
            $b = Brands::find($source->brand_id);
            $target->brand_id = $b
                ? $this->resolveBrandForBusiness($target_business_id, $b->name, $created_by)
                : null;
        } else {
            $target->brand_id = null;
        }

        // tax
        if (!empty($source->tax)) {
            $t = TaxRate::find($source->tax);
            $target->tax = $t
                ? $this->resolveTaxForBusiness($target_business_id, $t->name, $t->amount)
                : null;
        } else {
            $target->tax = null;
        }

        // composition_id
        if (!empty($source->composition_id)) {
            $c = Composition::find($source->composition_id);
            $target->composition_id = $c
                ? $this->resolveCompositionForBusiness($target_business_id, $c->name)
                : null;
        } else {
            $target->composition_id = null;
        }

        // warranty_id
        if (!empty($source->warranty_id)) {
            $w = Warranty::find($source->warranty_id);
            $target->warranty_id = $w
                ? $this->resolveWarrantyForBusiness($target_business_id, $w->name, $w->duration, $w->duration_type)
                : null;
        } else {
            $target->warranty_id = null;
        }

        // sub_unit_ids, sell/purchase whitelists + default sell/purchase sub-units
        $target->sub_unit_ids = $this->remapUnitIdList($target_business_id, $source->sub_unit_ids, $created_by) ?: null;
        $target->sell_sub_unit_ids = $this->remapUnitIdList($target_business_id, $source->sell_sub_unit_ids, $created_by) ?: null;
        $target->purchase_sub_unit_ids = $this->remapUnitIdList($target_business_id, $source->purchase_sub_unit_ids, $created_by) ?: null;

        if (!empty($source->default_sell_sub_unit_id)) {
            $su = Unit::find($source->default_sell_sub_unit_id);
            $target->default_sell_sub_unit_id = $su
                ? $this->resolveUnitForBusiness($target_business_id, $su, $created_by)
                : null;
        } else {
            $target->default_sell_sub_unit_id = null;
        }
        if (!empty($source->default_purchase_sub_unit_id)) {
            $pu = Unit::find($source->default_purchase_sub_unit_id);
            $target->default_purchase_sub_unit_id = $pu
                ? $this->resolveUnitForBusiness($target_business_id, $pu, $created_by)
                : null;
        } else {
            $target->default_purchase_sub_unit_id = null;
        }

        // Scalar / non-FK fields - safe to copy verbatim
        $scalar_keys = [
            'name', 'type', 'sub_category_id', 'tax_type', 'barcode_type',
            'image', 'product_description', 'weight', 'enable_stock',
            'alert_quantity', 'not_for_selling', 'is_inactive',
            'expiry_period_type', 'expiry_period', 'enable_sr_no',
            'preparation_time_in_minutes',
            'product_custom_field1', 'product_custom_field2',
            'product_custom_field3', 'product_custom_field4',
            'product_custom_field5', 'product_custom_field6',
            'product_custom_field7', 'product_custom_field8',
            'product_custom_field9', 'product_custom_field10',
            'product_custom_field11', 'product_custom_field12',
            'product_custom_field13', 'product_custom_field14',
            'product_custom_field15', 'product_custom_field16',
            'product_custom_field17', 'product_custom_field18',
            'product_custom_field19', 'product_custom_field20',
        ];
        foreach ($scalar_keys as $k) {
            $target->$k = $source->$k;
        }
    }

    /**
     * Sync all variations of $master onto $business_product, matching
     * by master_variation_id and falling back to name. Adds new
     * variations present on the master and removes stale ones.
     */
    private function syncVariationsFromMaster(Product $master, Product $business_product)
    {
        if ($master->type == 'single') {
            $master_var = Variation::where('product_id', $master->id)->first();
            if ($master_var) {
                $biz_var = Variation::where('product_id', $business_product->id)->first();
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
            return;
        }

        $master_vars = Variation::where('product_id', $master->id)->get();
        $biz_vars = Variation::where('product_id', $business_product->id)->get();

        foreach ($master_vars as $mv) {
            $biz_var = $biz_vars->firstWhere('master_variation_id', $mv->id);
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
                    'product_id' => $business_product->id,
                    'is_dummy' => 0,
                ]);
                Variation::create([
                    'name' => $mv->name,
                    'product_id' => $business_product->id,
                    'product_variation_id' => $pv->id,
                    'sub_sku' => $mv->sub_sku . '-' . $business_product->business_id,
                    'default_purchase_price' => $mv->default_purchase_price,
                    'dpp_inc_tax' => $mv->dpp_inc_tax,
                    'profit_percent' => $mv->profit_percent,
                    'default_sell_price' => $mv->default_sell_price,
                    'sell_price_inc_tax' => $mv->sell_price_inc_tax,
                    'master_variation_id' => $mv->id,
                ]);
            }
        }

        $master_var_ids = $master_vars->pluck('id')->toArray();
        $master_var_names = $master_vars->pluck('name')->toArray();

        Variation::where('product_id', $business_product->id)
            ->where(function ($q) use ($master_var_ids, $master_var_names) {
                $q->whereIn('master_variation_id', $master_var_ids)
                  ->orWhere(function ($q2) use ($master_var_ids, $master_var_names) {
                      $q2->whereNull('master_variation_id')
                         ->whereNotIn('name', $master_var_names);
                  });
            })
            ->delete();
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
     * Find or create a unit in a business, replicating the FULL unit
     * definition of the source (master) unit — hierarchy included.
     *
     * Units are matched per business by actual_name. When the unit is
     * created, and also when a same-named unit already exists, its
     * definition is aligned with the source unit: short_name,
     * allow_decimal, base_unit_id (re-pointed to this business's own
     * copy of the source's base unit, resolved recursively),
     * base_unit_multiplier and intermediate_unit_id. Without this the
     * store copy is a "flat" unit (no base link, no multiplier) and
     * the POS/purchase sub-unit dropdowns can never appear, while
     * conversions silently fall back to x1.
     *
     * Healing on every call also means: re-syncing a master product
     * repairs any store whose units were created flat by older code,
     * and later unit-definition changes in the master business
     * propagate to stores the next time any sync touches them.
     *
     * Public so maintenance commands (units:repair-store-hierarchy)
     * can reuse the exact same resolution logic.
     *
     * @param  int  $business_id  target business
     * @param  \App\Unit|null  $source_unit  unit row to replicate
     * @param  int  $created_by
     * @param  string|null  $fallback_name  used only when $source_unit is
     *                      null (legacy data) — creates/finds a flat
     *                      unit by name as before.
     * @param  string|null  $fallback_short_name
     * @param  int  $depth  internal recursion guard
     * @return int|null unit id in the target business
     */
    public function resolveUnitForBusiness($business_id, $source_unit, $created_by, $fallback_name = null, $fallback_short_name = null, $depth = 0)
    {
        // Legacy fallback: no source unit definition available.
        if (empty($source_unit)) {
            if (empty($fallback_name)) {
                return null;
            }
            $unit = Unit::where('business_id', $business_id)
                ->where('actual_name', $fallback_name)
                ->first();
            if (!$unit) {
                $unit = Unit::create([
                    'business_id' => $business_id,
                    'actual_name' => $fallback_name,
                    'short_name' => $fallback_short_name ?: $fallback_name,
                    'allow_decimal' => 0,
                    'created_by' => $created_by,
                ]);
            }

            return $unit->id;
        }

        // Guard against pathological cycles in unit data. A valid
        // hierarchy is at most base <- sub-unit (<- intermediate ref),
        // so a depth beyond 3 means the source data is circular.
        if ($depth > 3) {
            \Log::warning('resolveUnitForBusiness: unit hierarchy too deep/circular for unit ' . $source_unit->id);

            return null;
        }

        // Resolve the hierarchy references FIRST so the target ids
        // exist before we create/heal this unit.
        $target_base_id = null;
        if (!empty($source_unit->base_unit_id)) {
            $source_base = Unit::find($source_unit->base_unit_id);
            $target_base_id = $this->resolveUnitForBusiness($business_id, $source_base, $created_by, null, null, $depth + 1);
        }

        $target_intermediate_id = null;
        if (!empty($source_unit->intermediate_unit_id)) {
            $source_intermediate = Unit::find($source_unit->intermediate_unit_id);
            $target_intermediate_id = $this->resolveUnitForBusiness($business_id, $source_intermediate, $created_by, null, null, $depth + 1);
        }

        $unit = Unit::where('business_id', $business_id)
            ->where('actual_name', $source_unit->actual_name)
            ->first();

        // A unit must never reference itself.
        if (!empty($unit)) {
            if ($target_base_id == $unit->id) {
                $target_base_id = null;
            }
            if ($target_intermediate_id == $unit->id) {
                $target_intermediate_id = null;
            }
        }

        if (!$unit) {
            $unit = Unit::create([
                'business_id' => $business_id,
                'actual_name' => $source_unit->actual_name,
                'short_name' => $source_unit->short_name,
                'allow_decimal' => $source_unit->allow_decimal ?? 0,
                'base_unit_id' => $target_base_id,
                'base_unit_multiplier' => !empty($target_base_id) ? $source_unit->base_unit_multiplier : null,
                'intermediate_unit_id' => $target_intermediate_id,
                'created_by' => $created_by,
            ]);

            return $unit->id;
        }

        // Heal an existing unit whose definition drifted from the
        // source (flat legacy clones, changed multipliers, wrong
        // decimal flag, missing intermediate link).
        $dirty = false;

        if ((int) $unit->allow_decimal !== (int) ($source_unit->allow_decimal ?? 0)) {
            $unit->allow_decimal = $source_unit->allow_decimal ?? 0;
            $dirty = true;
        }
        if ($unit->base_unit_id != $target_base_id) {
            $unit->base_unit_id = $target_base_id;
            $dirty = true;
        }
        $target_multiplier = !empty($target_base_id) ? $source_unit->base_unit_multiplier : null;
        if (abs((float) $unit->base_unit_multiplier - (float) $target_multiplier) > 0.0001
            || (is_null($unit->base_unit_multiplier) !== is_null($target_multiplier))) {
            $unit->base_unit_multiplier = $target_multiplier;
            $dirty = true;
        }
        if ($unit->intermediate_unit_id != $target_intermediate_id) {
            $unit->intermediate_unit_id = $target_intermediate_id;
            $dirty = true;
        }

        if ($dirty) {
            $unit->save();
        }

        return $unit->id;
    }

    /**
     * Remap a list of unit ids from one business into another,
     * resolving (and healing) each unit by name. Used for
     * sub_unit_ids / sell_sub_unit_ids / purchase_sub_unit_ids.
     *
     * @param  int  $business_id
     * @param  array|null  $unit_ids  source-business unit ids
     * @param  int  $created_by
     * @return array target-business unit ids (deduplicated)
     */
    public function remapUnitIdList($business_id, $unit_ids, $created_by)
    {
        $mapped_ids = [];
        if (!empty($unit_ids) && is_array($unit_ids)) {
            $source_units = Unit::whereIn('id', $unit_ids)->get();
            foreach ($source_units as $source_unit) {
                $mapped = $this->resolveUnitForBusiness($business_id, $source_unit, $created_by);
                if ($mapped && !in_array($mapped, $mapped_ids)) {
                    $mapped_ids[] = $mapped;
                }
            }
        }

        return $mapped_ids;
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
     * Find a tax rate in the target business by name+amount. We do
     * NOT create a new tax row because the amount is a percentage
     * that varies by jurisdiction; if no match is found the clone
     * gets a null tax (preserves the prior "preserve the master
     * state" intent — the store can then assign its own tax).
     */
    private function resolveTaxForBusiness($business_id, $tax_name, $tax_amount = null)
    {
        if (empty($tax_name)) {
            return null;
        }
        $q = TaxRate::where('business_id', $business_id)->where('name', $tax_name);
        if ($tax_amount !== null) {
            $q->where('amount', $tax_amount);
        }
        $tax = $q->first();
        return $tax ? $tax->id : null;
    }

    /**
     * Find a composition in the target business by name. Compositions
     * are free-form labels so this is a safe remap. Returns null if
     * the composition hasn't been replicated yet (the store can add
     * it later via Compositions UI).
     */
    private function resolveCompositionForBusiness($business_id, $composition_name)
    {
        if (empty($composition_name)) {
            return null;
        }
        $comp = Composition::where('business_id', $business_id)
            ->where('name', $composition_name)
            ->first();
        return $comp ? $comp->id : null;
    }

    /**
     * Find a warranty in the target business by name + duration.
     * Returns null if not found; the store can configure its own.
     */
    private function resolveWarrantyForBusiness($business_id, $warranty_name, $duration = null, $duration_type = null)
    {
        if (empty($warranty_name)) {
            return null;
        }
        $q = Warranty::where('business_id', $business_id)->where('name', $warranty_name);
        if ($duration !== null) {
            $q->where('duration', $duration);
        }
        if ($duration_type !== null) {
            $q->where('duration_type', $duration_type);
        }
        $w = $q->first();
        return $w ? $w->id : null;
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
