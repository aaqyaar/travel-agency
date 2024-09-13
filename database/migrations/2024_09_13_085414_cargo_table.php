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
        Schema::create('cargo_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers');
            $table->text('sender_details');
            $table->text('receiver_details');
            $table->foreignId('cargo_origin_id')->constrained('destinations');
            $table->foreignId('cargo_destination_id')->constrained('destinations');
            $table->float('weight');
            $table->text('item_description');
            $table->float('shipping_price');
            $table->float('dispatch_cost');
            $table->json('other_expenses')->nullable();
            $table->float('total_amount');
            $table->boolean('payment_received')->default(false);
            $table->date('dispatch_date');
            $table->string('shipping_status');
            $table->foreignId('registered_by')->nullable()->constrained('users');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cargo_orders');
    }
};
