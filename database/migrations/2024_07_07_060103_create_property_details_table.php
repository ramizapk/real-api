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
        Schema::create('property_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('property_id'); // معرّف العقار
            $table->time('check_in_time')->nullable(); // وقت الحضور
            $table->time('check_out_time')->nullable();// وقت الانصراف
            $table->decimal('security_deposit', 10, 2)->nullable(); // مبلغ التأمين
            $table->text('additional_notes')->nullable(); // ملاحظات أخرى
            $table->timestamps();

            $table->foreign('property_id')->references('id')->on('properties')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('property_details');
    }
};
