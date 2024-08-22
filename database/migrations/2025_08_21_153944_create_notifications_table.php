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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->decimal("batteryPercent", 10)->nullable();
            $table->string("sensor_reference")->nullable();
            $table->string("description")->nullable();
            $table->foreignId("sensor_id")->constrained("sensors")->cascadeOnDelete();
            $table->foreignId("typeNotification_id")->constrained("type_notifications")->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
