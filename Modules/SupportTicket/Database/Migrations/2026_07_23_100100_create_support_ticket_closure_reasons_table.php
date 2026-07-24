<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('support_ticket_closure_reasons', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('business_id')->unsigned()->nullable()->comment('null = available to every business');
            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
            $table->string('label');
            $table->boolean('requires_resend')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('business_id');
        });

        DB::table('support_ticket_closure_reasons')->insert([
            [
                'business_id' => null,
                'label' => 'Loss accepted - will not resend',
                'requires_resend' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'business_id' => null,
                'label' => 'Approved for replacement - warehouse will resend',
                'requires_resend' => true,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('support_ticket_closure_reasons');
    }
};
