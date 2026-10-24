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
        Schema::create('shipment_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trip_id')->constrained('trips')->cascadeOnDelete();
            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
            $table->string('description', 500);
            $table->string('category', 30);
            $table->decimal('weight_kg', 6, 2);
            $table->unsignedInteger('declared_value')->nullable();
            $table->string('receiver_name', 120);
            $table->string('receiver_phone', 20);
            $table->string('receiver_city', 80);
            $table->string('status', 20)->default('en_attente');
            $table->string('decision_note', 255)->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();

            $table->index(['trip_id', 'status']);
            $table->index('sender_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipment_requests');
    }
};
