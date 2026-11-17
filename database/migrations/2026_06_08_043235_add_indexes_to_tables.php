<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasIndex('products', 'products_name_index')) {
                $table->index('name');
            }
            if (!Schema::hasIndex('products', 'products_category_id_index')) {
                $table->index('category_id');
            }
            if (!Schema::hasIndex('products', 'products_brand_id_index')) {
                $table->index('brand_id');
            }
            if (!Schema::hasIndex('products', 'products_price_index')) {
                $table->index('price');
            }
        });

        Schema::table('cart_items', function (Blueprint $table) {
            if (!Schema::hasIndex('cart_items', 'cart_items_user_id_index')) {
                $table->index('user_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            //
        });
    }
};
