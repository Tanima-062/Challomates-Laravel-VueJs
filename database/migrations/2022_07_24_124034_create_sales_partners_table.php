<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales_partners', function (Blueprint $table) {
            $table->id();
            $table->string('prefix_id');
            $table->string('company_name', 40)->index('company_name');
            $table->string('profile_picture')->nullable();
            $table->string('receipt_template');
            $table->string('receipt_template_name');
            $table->enum('status', ['new', 'active', 'inactive'])->default('new');
            $table->string('website')->nullable();
            $table->string('street', 30);
            $table->string('house_number', 30);
            $table->string('zip_code');
            $table->string('city', 30);
            $table->string('country');
            $table->boolean('no_information')->default(true);
            $table->string('contact_person_first_name', 30)->index('contact_person_first_name');
            $table->string('contact_person_last_name', 30)->index('contact_person_last_name');
            $table->string('contact_person_email');
            $table->string('contact_person_country_iso_code');
            $table->string('contact_person_phone_number');
            $table->string('contact_person_full_phone_number');
            $table->string('map_address');
            $table->point('coordinates');
            $table->unsignedBigInteger('branch_id');
            $table->unsignedBigInteger('branch_category_id');
            $table->unsignedBigInteger('consultant_id');
            $table->unsignedBigInteger('challo_mates_admin_id');
            $table->timestamps();

            $table->foreign('branch_id')->references('id')->on('branches');
            $table->foreign('branch_category_id')->references('id')->on('branch_categories');
            $table->foreign('consultant_id')->references('id')->on('users');
            $table->foreign('challo_mates_admin_id')->references('id')->on('users');
            $table->spatialIndex('coordinates');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sales_partners');
    }
};
