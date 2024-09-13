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
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('contact_number')->unique();
            $table->string('email')->nullable();
            $table->decimal('opening_balance', 10, 2)->default(0);
            $table->decimal('unpaid_balance', 10, 2)->default(0);
            $table->softDeletes();
            $table->timestamps();
            $table->foreignId('registered_by')->nullable()->constrained('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
