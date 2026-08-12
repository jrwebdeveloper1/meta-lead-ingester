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
        Schema::create('google_leads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('google_account_id')->constrained('google_accounts')->onDelete('cascade');
            $table->string('lead_id')->unique();
            $table->string('form_id')->nullable();
            $table->string('campaign_id')->nullable();
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
        Schema::dropIfExists('google_leads');
    }
};
