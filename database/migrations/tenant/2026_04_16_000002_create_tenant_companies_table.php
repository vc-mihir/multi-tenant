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
        Schema::create('companies', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedBigInteger('master_company_id')->unique();
            $table->string('company_name', 100)->index();
            $table->string('subdomain', 63)->unique();
            $table->string('company_email', 150)->unique();
            $table->string('website', 255);
            $table->string('license_number', 50)->unique();
            $table->text('address');
            $table->string('country', 100);
            $table->string('state', 100);
            $table->string('city', 100);
            $table->string('password', 60);
            $table->enum('status', ['active', 'inactive', 'suspended', 'pending'])->default('inactive')->index();
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
