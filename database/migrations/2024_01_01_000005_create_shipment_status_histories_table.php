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
        Schema::create('shipment_status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_request_id')->constrained('shipment_requests')->cascadeOnDelete();
            $table->string('status', 20);
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('note', 255)->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['shipment_request_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipment_status_histories');
    }
};
