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
        Schema::create('pools', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('property_id'); // معرّف العقار
            $table->enum('type', ['indoor', 'outdoor', 'water_park', 'heated']); // نوع المسبح
            $table->enum('fence', ['with_fence', 'without_fence']); // سياج المسبح
            $table->boolean('is_graduated'); // إذا كان المسبح مدرج
            $table->float('depth'); // عمق المسبح
            $table->float('length'); // طول المسبح
            $table->float('width'); // عرض المسبح
            $table->timestamps();

            $table->foreign('property_id')->references('id')->on('properties')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pools');
    }
};
