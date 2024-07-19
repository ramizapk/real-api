<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->string('property_name'); //الاسم
            $table->text('description'); //الوصصف
            $table->decimal('price', 10, 2);   //السعر
            $table->unsignedBigInteger('type_id');      //ايدي النوع
            $table->unsignedBigInteger('city_id');       //ايدي المدينة
            $table->string('address');                   //العنوان
            $table->decimal('latitude', 10, 8)->nullable(); // خط العرض
            $table->decimal('longitude', 11, 8)->nullable(); // خط الطول
            $table->integer('bathrooms');    //عدد الحمامات
            $table->integer('bedrooms');     //عدد غرف النوم
            $table->integer('capacity');     //السعة
            $table->json('amenities')->nullable();         //المرافق
            $table->json('kitchen_amenities')->nullable();     //مرافق المطبخ
            $table->enum('property_status', ['sale', 'rent']);   //حالة العقار بيع او ايجار
            $table->enum('availability_status', ['available', 'unavailable', 'rented', 'sold'])->default('available');
            ; // الإتاحة
            $table->unsignedBigInteger('owner_id');
            $table->string('owner_type');
            $table->enum('request_status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamps();
            $table->foreign('type_id')->references('id')->on('property_types');
            $table->foreign('city_id')->references('id')->on('cities');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
