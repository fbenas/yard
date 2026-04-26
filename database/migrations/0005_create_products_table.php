<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('organisation_id', 26);
            $table->string('name');
            $table->text('description')->nullable();
            $table->json('variant_dimensions')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();

            $table->index('organisation_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
