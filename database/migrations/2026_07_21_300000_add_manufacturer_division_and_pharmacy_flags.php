<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('manufacturers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('business_id')->unsigned();
            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('created_by')->unsigned();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('divisions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('business_id')->unsigned();
            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('created_by')->unsigned();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->integer('manufacturer_id')->unsigned()->nullable()->after('brand_id');
            $table->integer('division_id')->unsigned()->nullable()->after('manufacturer_id');
            $table->boolean('can_be_purchased')->default(1)->after('not_for_selling');
            $table->boolean('can_be_stored')->default(1)->after('can_be_purchased');
            $table->boolean('can_be_sold')->default(1)->after('can_be_stored');
            $table->text('product_tags')->nullable()->after('can_be_sold');

            $table->foreign('manufacturer_id')->references('id')->on('manufacturers')->onDelete('set null');
            $table->foreign('division_id')->references('id')->on('divisions')->onDelete('set null');
        });

        Schema::table('variations', function (Blueprint $table) {
            $table->decimal('ptr', 22, 4)->default(0)->after('mrp_inc_tax');
            $table->decimal('pts', 22, 4)->default(0)->after('ptr');
        });
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['manufacturer_id']);
            $table->dropForeign(['division_id']);
            $table->dropColumn(['manufacturer_id', 'division_id', 'can_be_purchased', 'can_be_stored', 'can_be_sold', 'product_tags']);
        });

        Schema::table('variations', function (Blueprint $table) {
            $table->dropColumn(['ptr', 'pts']);
        });

        Schema::dropIfExists('divisions');
        Schema::dropIfExists('manufacturers');
    }
};
