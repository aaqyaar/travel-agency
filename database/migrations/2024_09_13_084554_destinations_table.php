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
        Schema::create('destinations', function(Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string(column: 'continent');
            $table->string('iso_country', 2); 
            $table->string('municipality');
            $table->string('iata_code');
            $table->foreignId('registered_by')->nullable()->constrained('users');
            $table->timestamps();

            $table->foreign('iso_country')->references('code')->on('countries')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('destinations');
    }
};
