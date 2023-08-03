<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ozon_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 1024);
            $table->timestamps();
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\Category::class, 'ozon_category_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ozon_categories');
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('ozon_category_id');
        });
    }
};
