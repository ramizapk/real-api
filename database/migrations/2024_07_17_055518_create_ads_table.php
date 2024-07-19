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
        Schema::create('ads', function (Blueprint $table) {
            $table->id();
            $table->text('main_text')->nullable(); // نص الإعلان إن وجد
            $table->text('sub_text')->nullable(); // النص الفرعي للإعلان إن وجد
            $table->string('button_text')->nullable(); // عنوان زر الإعلان إن وجد
            $table->string('image');// صورة الإعلان 
            $table->enum('ad_type', ['internal', 'external']); // نوع الإعلان سواء داخلي عبر الموقع أو التطبيق أو خارجي
            $table->string('ad_url')->nullable(); // رابط الإعلان
            $table->date('expiration_date'); // تاريخ انتهاء الإعلان
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ads');
    }
};
