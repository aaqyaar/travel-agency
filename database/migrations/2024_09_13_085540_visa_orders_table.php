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
        Schema::create('visa_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers');
            $table->string('visa_type');
            $table->string('visa_country');
            $table->string('visa_source');
            $table->string('document_collection');
            $table->float('processing_cost');
            $table->float('refund_amount')->nullable();
            $table->json('other_expenses')->nullable();
            $table->float('total_amount');
            $table->boolean('payment_received')->default(false);
            $table->float('revenue_or_loss');
            $table->string('processing_status');
            $table->enum('payment_status', ['paid', 'unpaid', 'partially_paid']);
            $table->float('partially_paid_amount');
            $table->float('remaining_amount');
            $table->string('visa_outcome')->nullable();
            $table->foreignId('registered_by')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visa_orders');
    }
};
