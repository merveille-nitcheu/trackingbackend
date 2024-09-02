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
        Schema::create('sites', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->longText("description")->nullable();
            $table->string("address")->nullable();
            $table->decimal("longitude", 10)->nullable();
            $table->decimal("latitude", 10)->nullable();
            $table->double("gmt");
            $table->integer("nbsubsite")->default(0);
            $table->foreignId("compagny_id")->constrained("compagnies")->cascadeOnDelete();
            $table->foreignId('site_id')->nullable()->constrained()->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();



        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sites');
    }
};
