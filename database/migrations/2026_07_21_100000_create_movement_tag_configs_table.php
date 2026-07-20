<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('movement_tag_configs', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('business_id');
            $table->unsignedInteger('location_id')->nullable();
            $table->string('tag_code', 10);
            $table->string('tag_name', 50);
            $table->decimal('min_monthly_sales', 10, 2)->default(0);
            $table->decimal('max_monthly_sales', 10, 2)->nullable();
            $table->unsignedInteger('avg_days_for_min_stock')->default(0);
            $table->decimal('max_stock_buffer_percent', 5, 2)->default(20);
            $table->unsignedTinyInteger('sort_order')->default(0);
            $table->unsignedInteger('created_by');
            $table->timestamps();

            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
            $table->foreign('location_id')->references('id')->on('business_locations')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');

            $table->index(['business_id', 'location_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('movement_tag_configs');
    }
};
