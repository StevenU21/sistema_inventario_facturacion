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
        Schema::create('products', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('image')->nullable();
            $table->text('description')->nullable();
            $table->decimal('purchase_price', 10, 2);
            $table->decimal('sale_price', 10, 2);
            $table->unsignedInteger('stock');
            $table->unsignedInteger('min_stock');

            $table->integer('brand_id')->unsigned();
            $table->foreign('brand_id')->references('id')->on('brands')->onDelete('cascade')->onUpdate('cascade');

            $table->integer('category_id')->unsigned();
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade')->onUpdate('cascade');

            $table->integer('tax_id')->unsigned();
            $table->foreign('tax_id')->references('id')->on('taxes')->onDelete('cascade')->onUpdate('cascade');

            $table->integer('unit_measure_id')->unsigned();
            $table->foreign('unit_measure_id')->references('id')->on('unit_measures')->onDelete('cascade')->onUpdate('cascade');

            $table->integer('entity_id')->unsigned();
            $table->foreign('entity_id')->references('id')->on('entities')->onDelete('cascade')->onUpdate('cascade');

            $table->integer('product_status_id')->unsigned();
            $table->foreign('product_status_id')->references('id')->on('product_statuses')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
