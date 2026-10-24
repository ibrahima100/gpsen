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
        Schema::create('trips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('groupeur_id')->constrained('users')->cascadeOnDelete();
            $table->string('origin_country', 80);
            $table->string('origin_city', 80);
            $table->string('destination_country', 80);
            $table->string('destination_city', 80);
            $table->date('departure_date');
            $table->date('arrival_date')->nullable();
            $table->decimal('capacity_kg', 6, 2);
            $table->unsignedInteger('price_per_kg');
            $table->text('accepted_items')->nullable();
            $table->text('prohibited_items')->nullable();
            $table->string('pickup_info', 255)->nullable();
            $table->string('status', 20)->default('ouvert');
            $table->string('cancelled_reason', 255)->nullable();
            $table->timestamps();

            $table->index(['status', 'departure_date']);
            $table->index(['origin_city', 'destination_city']);
            $table->index('groupeur_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trips');
    }
};
