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
        Schema::create('purchases', function (Blueprint $table) {
            $table->increments('id');
            $table->enum('status', ['pending', 'completed', 'canceled'])->default('pending');
            $table->date('purchase_date')->nullable();
            $table->date('expected_at')->nullable();
            $table->date('received_at')->nullable();
            $table->string('reference')->nullable();
            $table->string('document_number')->nullable();
            $table->decimal('subtotal', 10, 2)->nullable()->default(0);
            $table->decimal('total', 10, 2)->nullable()->default(0);

            $table->integer('entity_id')->nullable();
            $table->foreign('entity_id')->references('id')->on('entities')->onDelete('cascade')->onUpdate('cascade');

            $table->integer('warehouse_id')->unsigned();
            $table->foreign('warehouse_id')->references('id')->on('warehouses')->onDelete('cascade')->onUpdate('cascade');

            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');

            $table->integer('payment_method_id')->unsigned();
            $table->foreign('payment_method_id')->references('id')->on('payment_methods')->onDelete('cascade')->onUpdate('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};
