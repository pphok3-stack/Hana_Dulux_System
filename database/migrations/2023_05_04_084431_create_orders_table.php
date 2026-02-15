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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->foreignId("user_id")->constrained()->onDelete('cascade');
            $table->foreignIdFor(\App\Models\Customer::class)
                ->constrained();
            $table->string('order_date');
            $table->tinyInteger('order_status')
                ->comment('0 - Pending / 1 - Complete');
            $table->integer('total_products');
            $table->decimal('sub_total', 10, 2);
            $table->decimal('vat', 10, 2);
            $table->decimal('total', 10, 2);
            $table->string('invoice_no');
            $table->string('payment_type');
            $table->decimal('pay', 10, 2);
            $table->decimal('due', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};