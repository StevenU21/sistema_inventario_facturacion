<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('inventories', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('stock');
            $table->unsignedInteger('min_stock');
            $table->decimal('purchase_price', 10, 2);
            $table->decimal('sale_price', 10, 2);

            $table->integer('product_variant_id')->unsigned();
            $table->foreign('product_variant_id')->references('id')->on('product_variants')->onDelete('cascade')->onUpdate('cascade');

            $table->integer('warehouse_id')->unsigned();
            $table->foreign('warehouse_id')->references('id')->on('warehouses')->onDelete('cascade')->onUpdate('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};
