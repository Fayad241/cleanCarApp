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
        Schema::create('service_vehicule_durations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('service_id')->constrained()->onDelete('cascade');
            $table->enum('vehicule_size', ['small', 'medium', 'large', 'xlarge', 'motorcycle']);
            $table->integer('duration_minutes');
            $table->integer('slot_size')->default(1)->comment('Nb de créneaux 30min necessaires');
            $table->decimal('price', 10, 2);
            $table->timestamps();

            // Unicité : un service ne peut avoir qu'une config par taille
            $table->unique(['service_id', 'vehicule_size']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_vehicule_durations');
    }
};
