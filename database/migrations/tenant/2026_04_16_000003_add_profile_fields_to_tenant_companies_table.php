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
            if (! Schema::hasColumn('companies', 'website')) {
                $table->string('website')->nullable()->after('company_email');
            }

            if (! Schema::hasColumn('companies', 'license_number')) {
                $table->string('license_number', 50)->nullable()->after('website');
            }

            if (! Schema::hasColumn('companies', 'address')) {
                $table->string('address', 500)->nullable()->after('license_number');
            }

            if (! Schema::hasColumn('companies', 'country')) {
                $table->string('country', 100)->nullable()->after('address');
            }

            if (! Schema::hasColumn('companies', 'state')) {
                $table->string('state', 100)->nullable()->after('country');
            }

            if (! Schema::hasColumn('companies', 'city')) {
                $table->string('city', 100)->nullable()->after('state');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $dropColumns = array_values(array_filter([
                Schema::hasColumn('companies', 'website') ? 'website' : null,
                Schema::hasColumn('companies', 'license_number') ? 'license_number' : null,
                Schema::hasColumn('companies', 'address') ? 'address' : null,
                Schema::hasColumn('companies', 'country') ? 'country' : null,
                Schema::hasColumn('companies', 'state') ? 'state' : null,
                Schema::hasColumn('companies', 'city') ? 'city' : null,
            ]));

            if ($dropColumns !== []) {
                $table->dropColumn($dropColumns);
            }
        });
    }
};
