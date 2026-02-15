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
        Schema::create('quotation_details', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(\App\Models\Quotation::class)
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignIdFor(\App\Models\Product::class)
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('product_name');
            $table->string('product_code');

            $table->integer('quantity');
            $table->decimal('price', 10, 2);
            $table->decimal('unit_price', 10, 2);
            $table->decimal('sub_total', 10, 2);
            $table->decimal('product_discount_amount', 10, 2);
            $table->string('product_discount_type')->default('fixed');
            $table->decimal('product_tax_amount', 10, 2);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotation_details');
    }
};