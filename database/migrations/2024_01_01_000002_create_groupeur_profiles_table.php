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
        Schema::create('groupeur_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->text('bio');
            $table->string('usual_routes', 255)->nullable();
            $table->string('id_document_path', 255);
            $table->string('validation_status', 20)->default('en_attente');
            $table->string('rejection_reason', 255)->nullable();
            $table->foreignId('validated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('validated_at')->nullable();
            $table->decimal('average_rating', 3, 2)->default(0.00);
            $table->unsignedInteger('ratings_count')->default(0);
            $table->unsignedInteger('delivered_count')->default(0);
            $table->timestamps();

            $table->index('validation_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('groupeur_profiles');
    }
};
