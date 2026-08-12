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
        Schema::create('meta_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('company_name')->nullable();
            $table->string('page_id')->unique();
            $table->string('page_access_token', 500);
            $table->string('verify_token')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meta_accounts');
    }
};
