<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
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
     *
     * Decrypts name and email back to plain text before reverting column types,
     * because encrypted ciphertext exceeds the original varchar length constraints.
     */
    public function down(): void
    {
        DB::table('users')->orderBy('id')->each(function ($user) {
            try {
                DB::table('users')->where('id', $user->id)->update([
                    'name'  => Crypt::decryptString($user->name),
                    'email' => Crypt::decryptString($user->email),
                ]);
            } catch (\Exception $e) {
                // Value is already plain text — no decryption needed
            }
        });

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
