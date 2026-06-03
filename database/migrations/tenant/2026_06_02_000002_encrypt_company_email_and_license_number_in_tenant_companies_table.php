<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
     *
     * Decrypts company_email and license_number back to plain text before
     * reverting column types, because encrypted ciphertext exceeds the
     * original varchar length constraints.
     */
    public function down(): void
    {
        DB::connection('tenant')->table('companies')->orderBy('id')->each(function ($company) {
            try {
                DB::connection('tenant')->table('companies')->where('id', $company->id)->update([
                    'company_email'  => decrypt($company->company_email),
                    'license_number' => decrypt($company->license_number),
                ]);
            } catch (\Exception $e) {
                // Value is already plain text — no decryption needed
            }
        });

        if (Schema::hasColumn('companies', 'company_email_hash')) {
            Schema::table('companies', function (Blueprint $table) {
                $table->dropUnique(['company_email_hash']);
                $table->dropColumn('company_email_hash');
            });
        }

        if (Schema::hasColumn('companies', 'license_number_hash')) {
            Schema::table('companies', function (Blueprint $table) {
                $table->dropUnique(['license_number_hash']);
                $table->dropColumn('license_number_hash');
            });
        }

        Schema::table('companies', function (Blueprint $table) {
            $table->string('company_email', 150)->nullable(false)->change();
            $table->unique('company_email');

            $table->string('license_number', 50)->nullable(false)->change();
            $table->unique('license_number');
        });
    }
};
