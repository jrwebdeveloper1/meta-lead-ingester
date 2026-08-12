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
        Schema::create('meta_leads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meta_account_id')->constrained('meta_accounts')->onDelete('cascade');
            $table->string('leadgen_id')->unique();
            $table->string('form_id')->nullable();
            $table->string('full_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone_number')->nullable();
            $table->json('raw_field_data')->nullable();
            $table->timestamp('lead_created_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meta_leads');
    }
};
