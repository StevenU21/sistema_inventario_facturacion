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
        // products
        Schema::table('products', function (Blueprint $table) {
            // Lookup and filters
            if (!Schema::hasColumn('products', 'code')) {
                return;
            }
            $table->index('code', 'products_code_index');
            if (Schema::hasColumn('products', 'sku')) {
                $table->index('sku', 'products_sku_index');
            }
            if (Schema::hasColumn('products', 'status')) {
                $table->index('status', 'products_status_index');
            }
            // Foreign key filters
            $table->index('brand_id', 'products_brand_id_index');
            $table->index('category_id', 'products_category_id_index');
            $table->index('tax_id', 'products_tax_id_index');
            $table->index('unit_measure_id', 'products_unit_measure_id_index');
            $table->index('entity_id', 'products_entity_id_index');
        });

        // product_variants
        Schema::table('product_variants', function (Blueprint $table) {
            // Composite for common filtering by product/color/size
            $table->index(['product_id', 'color_id', 'size_id'], 'product_variants_product_color_size_index');
        });

        // inventories
        Schema::table('inventories', function (Blueprint $table) {
            // Composite to quickly find stock per variant per warehouse
            $table->index(['product_variant_id', 'warehouse_id'], 'inventories_variant_warehouse_index');
            // Additionally support queries scoped only by warehouse
            $table->index('warehouse_id', 'inventories_warehouse_id_index');
        });

        // inventory_movements
        Schema::table('inventory_movements', function (Blueprint $table) {
            // Time-series by inventory
            $table->index(['inventory_id', 'created_at'], 'inventory_movements_inventory_created_at_index');
            // Other common filters
            $table->index('user_id', 'inventory_movements_user_id_index');
            $table->index('type', 'inventory_movements_type_index');
        });

        // purchases
        Schema::table('purchases', function (Blueprint $table) {
            $table->index('entity_id', 'purchases_entity_id_index');
            $table->index('warehouse_id', 'purchases_warehouse_id_index');
            $table->index('user_id', 'purchases_user_id_index');
            $table->index('payment_method_id', 'purchases_payment_method_id_index');
            $table->index('reference', 'purchases_reference_index');
            $table->index(['entity_id', 'created_at'], 'purchases_entity_created_at_index');
        });

        // purchase_details
        Schema::table('purchase_details', function (Blueprint $table) {
            $table->index('purchase_id', 'purchase_details_purchase_id_index');
            $table->index('product_variant_id', 'purchase_details_product_variant_id_index');
        });

        // entities
        Schema::table('entities', function (Blueprint $table) {
            // Frequent lookups
            $table->index('identity_card', 'entities_identity_card_index');
            $table->index('ruc', 'entities_ruc_index');
            $table->index('phone', 'entities_phone_index');
            // Relational filter
            $table->index('municipality_id', 'entities_municipality_id_index');
            // Optional: quick filters for client/supplier lists
            $table->index('is_client', 'entities_is_client_index');
            $table->index('is_supplier', 'entities_is_supplier_index');
        });

        // users
        Schema::table('users', function (Blueprint $table) {
            $table->index('is_active', 'users_is_active_index');
        });

        // profiles
        Schema::table('profiles', function (Blueprint $table) {
            $table->index('user_id', 'profiles_user_id_index');
            if (Schema::hasColumn('profiles', 'identity_card')) {
                $table->index('identity_card', 'profiles_identity_card_index');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // products
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('products_code_index');
            if (Schema::hasColumn('products', 'sku')) {
                $table->dropIndex('products_sku_index');
            }
            if (Schema::hasColumn('products', 'status')) {
                $table->dropIndex('products_status_index');
            }
            $table->dropIndex('products_brand_id_index');
            $table->dropIndex('products_category_id_index');
            $table->dropIndex('products_tax_id_index');
            $table->dropIndex('products_unit_measure_id_index');
            $table->dropIndex('products_entity_id_index');
        });

        // product_variants
        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropIndex('product_variants_product_color_size_index');
        });

        // inventories
        Schema::table('inventories', function (Blueprint $table) {
            $table->dropIndex('inventories_variant_warehouse_index');
            $table->dropIndex('inventories_warehouse_id_index');
        });

        // inventory_movements
        Schema::table('inventory_movements', function (Blueprint $table) {
            $table->dropIndex('inventory_movements_inventory_created_at_index');
            $table->dropIndex('inventory_movements_user_id_index');
            $table->dropIndex('inventory_movements_type_index');
        });

        // purchases
        Schema::table('purchases', function (Blueprint $table) {
            $table->dropIndex('purchases_entity_id_index');
            $table->dropIndex('purchases_warehouse_id_index');
            $table->dropIndex('purchases_user_id_index');
            $table->dropIndex('purchases_payment_method_id_index');
            $table->dropIndex('purchases_reference_index');
            $table->dropIndex('purchases_entity_created_at_index');
        });

        // purchase_details
        Schema::table('purchase_details', function (Blueprint $table) {
            $table->dropIndex('purchase_details_purchase_id_index');
            $table->dropIndex('purchase_details_product_variant_id_index');
        });

        // entities
        Schema::table('entities', function (Blueprint $table) {
            $table->dropIndex('entities_identity_card_index');
            $table->dropIndex('entities_ruc_index');
            $table->dropIndex('entities_phone_index');
            $table->dropIndex('entities_municipality_id_index');
            $table->dropIndex('entities_is_client_index');
            $table->dropIndex('entities_is_supplier_index');
        });

        // users
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_is_active_index');
        });

        // profiles
        Schema::table('profiles', function (Blueprint $table) {
            $table->dropIndex('profiles_user_id_index');
            if (Schema::hasColumn('profiles', 'identity_card')) {
                $table->dropIndex('profiles_identity_card_index');
            }
        });
    }
};
