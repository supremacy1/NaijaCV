
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cvs', function (Blueprint $table) {
            // Allow CVs to belong to guests.
            $table->dropForeign(['user_id']);

            $table->unsignedBigInteger('user_id')
                ->nullable()
                ->change();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            // Guest access and expiry.
            $table->uuid('guest_token')
                ->nullable()
                ->unique();

            $table->timestamp('expires_at')->nullable();

            // Payment information.
            $table->string('payment_status')
                ->default('free');

            $table->string('payment_reference')
                ->nullable()
                ->unique();

            $table->timestamp('payment_verified_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('cvs', function (Blueprint $table) {
            $table->dropForeign(['user_id']);

            $table->dropUnique(['guest_token']);
            $table->dropUnique(['payment_reference']);

            $table->dropColumn([
                'guest_token',
                'expires_at',
                'payment_status',
                'payment_reference',
                'payment_verified_at',
            ]);

            $table->unsignedBigInteger('user_id')
                ->nullable(false)
                ->change();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();
        });
    }
};
