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
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->longText('content')->nullable();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->integer('warranty_months')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->string('status')->default('active');
            $table->unsignedBigInteger('views')->default(0);
            $table->unsignedBigInteger('total_sold')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
