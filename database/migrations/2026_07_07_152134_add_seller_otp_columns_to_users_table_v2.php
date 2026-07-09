<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Check if columns exist before adding
        if (!Schema::hasColumn('users', 'seller_otp')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('seller_otp', 6)->nullable()->after('mobile_verified_at');
            });
        }

        if (!Schema::hasColumn('users', 'seller_otp_expires_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->timestamp('seller_otp_expires_at')->nullable()->after('seller_otp');
            });
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['seller_otp', 'seller_otp_expires_at']);
        });
    }
};