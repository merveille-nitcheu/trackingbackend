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
        Schema::create('sensor_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId("sensor_id")->constrained("sensors")->cascadeOnDelete();
            $table->decimal("longitude", 10)->nullable();
            $table->decimal("latitude", 10)->nullable();
            $table->decimal("temperature", 10)->nullable();
            $table->decimal("battery", 10)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sensor_records');
    }
};
