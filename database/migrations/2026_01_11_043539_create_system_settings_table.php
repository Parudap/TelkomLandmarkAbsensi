<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('system_settings', function (Blueprint $table) {
            $table->string('setting_key', 50)->primary();
            $table->text('setting_value');
            $table->string('description')->nullable();
            $table->string('category', 50)->default('general'); // general, absensi, approval, notification
            $table->enum('type', ['text', 'number', 'boolean', 'json'])->default('text');
            $table->boolean('is_editable')->default(true); // Beberapa setting tidak boleh diubah user
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_settings');
    }
};
