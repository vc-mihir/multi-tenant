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
        Schema::table('companies', function (Blueprint $table) {
            $table->dropUnique(['company_email']);
            $table->text('company_email')->nullable()->change();
            $table->string('company_email_hash', 64)->nullable()->unique()->after('company_email');

            $table->dropUnique(['license_number']);
            $table->text('license_number')->nullable()->change();
            $table->string('license_number_hash', 64)->nullable()->unique()->after('license_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropUnique(['company_email_hash']);
            $table->dropColumn('company_email_hash');
            $table->string('company_email', 150)->nullable(false)->change();
            $table->unique('company_email');

            $table->dropUnique(['license_number_hash']);
            $table->dropColumn('license_number_hash');
            $table->string('license_number', 50)->nullable(false)->change();
            $table->unique('license_number');
        });
    }
};
