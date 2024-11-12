<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {

        Schema::create("brands", function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->timestamps();
        });

        Schema::create("sub_brands", function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->foreignId("brand_id")->constrained()->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create("product_categories", function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->timestamps();
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('code', 128)->unique();
            $table->string('name');
            $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sub_brand_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_category_id')->constrained()->cascadeOnDelete();
            $table->string('class');
            $table->double('buying_price');
            $table->double('selling_price');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
        Schema::dropIfExists('sub_brands');
        Schema::dropIfExists('product_categories');
        Schema::dropIfExists('brands');
    }
};
