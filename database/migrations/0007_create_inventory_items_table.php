<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_variant_id')->constrained()->cascadeOnDelete();
            $table->string('sku')->unique();
            $table->string('name')->nullable();
            $table->timestamps();

            $table->index('product_variant_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_items');
    }
};
