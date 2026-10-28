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
        Schema::table('trips', function (Blueprint $table) {
            if (! Schema::hasColumn('trips', 'origin_city_id')) {
                $table->foreignId('origin_city_id')->nullable()->after('origin_city')->constrained('cities')->nullOnDelete();
            }
            if (! Schema::hasColumn('trips', 'destination_city_id')) {
                $table->foreignId('destination_city_id')->nullable()->after('destination_city')->constrained('cities')->nullOnDelete();
            }
        });

        Schema::table('shipment_requests', function (Blueprint $table) {
            if (! Schema::hasColumn('shipment_requests', 'receiver_city_id')) {
                $table->foreignId('receiver_city_id')->nullable()->after('receiver_city')->constrained('cities')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trips', function (Blueprint $table) {
            $table->dropForeign(['origin_city_id']);
            $table->dropForeign(['destination_city_id']);
            $table->dropColumn(['origin_city_id', 'destination_city_id']);
        });

        Schema::table('shipment_requests', function (Blueprint $table) {
            $table->dropForeign(['receiver_city_id']);
            $table->dropColumn(['receiver_city_id']);
        });
    }
};
