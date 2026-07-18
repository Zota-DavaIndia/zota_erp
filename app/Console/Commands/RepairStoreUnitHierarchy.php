<?php

namespace App\Console\Commands;

use App\Business;
use App\Product;
use App\Unit;
use Illuminate\Console\Command;
use Modules\Superadmin\Http\Controllers\SuperadminProductController;

class RepairStoreUnitHierarchy extends Command
{
    /**
     * @var string
     */
    protected $signature = 'units:repair-store-hierarchy {--dry-run : Report what would change without writing}';

    /**
     * @var string
     */
    protected $description = 'Replicate the template (first) business\'s unit hierarchy into every store business — healing flat/mismatched units created by older sync code — and clean dangling unit references on products.';

    public function handle()
    {
        $dry_run = $this->option('dry-run');

        // The chain convention throughout the central-master layer:
        // the first (lowest id) business — the super admin's own —
        // is the template every store derives from.
        $template = Business::orderBy('id')->first();
        if (empty($template)) {
            $this->error('No business found.');

            return 1;
        }
        $this->info("Template business: #{$template->id} {$template->name}");

        // Bases first so sub-units resolve against an existing base.
        $template_units = Unit::where('business_id', $template->id)
            ->orderByRaw('base_unit_id IS NOT NULL')
            ->orderBy('id')
            ->get();

        $resolver = app(SuperadminProductController::class);

        $other_businesses = Business::where('id', '!=', $template->id)->orderBy('id')->get();

        foreach ($other_businesses as $business) {
            $this->line("Business #{$business->id} {$business->name}:");
            $created_by = $business->owner_id ?? $business->created_by ?? 1;

            foreach ($template_units as $template_unit) {
                $existing = Unit::where('business_id', $business->id)
                    ->where('actual_name', $template_unit->actual_name)
                    ->first();

                $state = 'ok';
                if (empty($existing)) {
                    $state = 'missing';
                } elseif (! empty($template_unit->base_unit_id) && empty($existing->base_unit_id)) {
                    $state = 'flat';
                } elseif (! empty($template_unit->base_unit_id)
                    && abs((float) $existing->base_unit_multiplier - (float) $template_unit->base_unit_multiplier) > 0.0001) {
                    $state = 'multiplier-drift';
                } elseif ((int) $existing->allow_decimal !== (int) $template_unit->allow_decimal) {
                    $state = 'decimal-drift';
                }

                if ($state !== 'ok') {
                    $this->line("  - '{$template_unit->actual_name}': {$state}" . ($dry_run ? ' (dry run, not fixed)' : ' -> fixed'));
                }

                if (! $dry_run) {
                    $resolver->resolveUnitForBusiness($business->id, $template_unit, $created_by);
                }
            }
        }

        // Clean dangling unit references on products (units that were
        // soft-deleted or never existed in the product's business).
        $this->line('');
        $this->info('Checking products for dangling unit references...');
        $fixed_products = 0;

        Product::with([])->orderBy('id')->chunk(100, function ($products) use (&$fixed_products, $dry_run) {
            foreach ($products as $product) {
                $valid_unit_ids = Unit::where('business_id', $product->business_id)
                    ->pluck('id')
                    ->map(fn ($id) => (int) $id)
                    ->all();

                $changed = false;

                foreach (['sub_unit_ids', 'sell_sub_unit_ids', 'purchase_sub_unit_ids'] as $list_field) {
                    $list = $product->$list_field;
                    if (! empty($list) && is_array($list)) {
                        $clean = array_values(array_filter(array_map('intval', $list), fn ($id) => in_array($id, $valid_unit_ids, true)));
                        if ($clean !== array_map('intval', array_values($list))) {
                            $product->$list_field = $clean ?: null;
                            $changed = true;
                        }
                    }
                }

                foreach (['default_sell_sub_unit_id', 'default_purchase_sub_unit_id', 'secondary_unit_id'] as $id_field) {
                    if (! empty($product->$id_field) && ! in_array((int) $product->$id_field, $valid_unit_ids, true)) {
                        $product->$id_field = null;
                        $changed = true;
                    }
                }

                if ($changed) {
                    $fixed_products++;
                    $this->line("  - product #{$product->id} '{$product->name}' (business {$product->business_id}): dangling unit reference" . ($dry_run ? ' (dry run, not fixed)' : ' -> cleaned'));
                    if (! $dry_run) {
                        $product->save();
                    }
                }
            }
        });

        $this->info($fixed_products . ' product(s) had dangling unit references.');
        $this->info($dry_run ? 'Dry run complete — nothing written.' : 'Repair complete.');

        return 0;
    }
}
