<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('category_specifications', function (Blueprint $table) {
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('specification_id')->constrained()->cascadeOnDelete();
            $table->primary(['category_id', 'specification_id']);
            $table->timestamps();
        });

        Schema::create('category_attribute_types', function (Blueprint $table) {
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('variant_attribute_type_id')->constrained('variant_attribute_types')->cascadeOnDelete();
            $table->primary(['category_id', 'variant_attribute_type_id']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('category_attribute_types');
        Schema::dropIfExists('category_specifications');
    }
};
