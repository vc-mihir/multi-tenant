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
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['name']);
            $table->text('name')->nullable()->change();
            $table->string('name_hash', 64)->nullable()->index()->after('name');

            $table->dropUnique(['email']);
            $table->text('email')->nullable()->change();
            $table->string('email_hash', 64)->nullable()->unique()->after('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['name_hash']);
            $table->dropColumn('name_hash');
            $table->string('name', 100)->nullable(false)->change();
            $table->index('name');

            $table->dropUnique(['email_hash']);
            $table->dropColumn('email_hash');
            $table->string('email', 150)->nullable(false)->change();
            $table->unique('email');
        });
    }
};
