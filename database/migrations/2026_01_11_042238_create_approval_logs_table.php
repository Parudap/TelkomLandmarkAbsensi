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
        Schema::create('approval_logs', function (Blueprint $table) {
            $table->id();
            
            // Polymorphic relation - bisa untuk user registration atau izin
            $table->unsignedBigInteger('approvable_id');
            $table->string('approvable_type');
            
            // Tipe approval: registration, izin_layer1, izin_layer2
            $table->enum('tipe_approval', ['registration', 'izin_layer1', 'izin_layer2']);
            
            // Status: pending, approved, rejected, auto_approved
            $table->enum('status', ['pending', 'approved', 'rejected', 'auto_approved']);
            
            // Approver
            $table->foreignId('approver_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('approver_role')->nullable(); // hr
            
            // Keterangan
            $table->text('keterangan')->nullable();
            
            // Waktu approval
            $table->timestamp('approved_at')->nullable();
            
            $table->timestamps();
            
            // Index (manual tanpa morphs untuk menghindari duplikasi)
            $table->index(['approvable_type', 'approvable_id']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approval_logs');
    }
};
