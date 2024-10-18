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
          Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers');
            $table->foreignId('supplier_id')->constrained('suppliers');
            $table->foreignId('from_destination_id')->constrained('destinations', 'id');
            $table->foreignId('to_destination_id')->constrained('destinations', 'id');
            $table->string('trip_type');
            $table->string('ticket_type');
            $table->float('total_amount');
            $table->float('airline_cost');
            $table->json('other_expenses')->nullable();
            $table->foreignId('payment_method_id')->constrained('payment_methods');
            $table->string('ticket_attachment')->nullable();
            $table->string('sales_status');
            $table->float('revenue');
            $table->foreignId('registered_by')->nullable()->constrained('users');
            $table->timestamps();   
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
