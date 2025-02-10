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
        Schema::table('earnings', function (Blueprint $table) {
            $table->unsignedBigInteger('earning_categories_id')->after('user_id');
            $table->foreign('earning_categories_id')->references('id')->on('earning_categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('earnings', function (Blueprint $table) {
            $table->dropForeign('earning_categories_id_foreign');
            $table->dropColumn('earning_categories_id');
        });
    }
};
