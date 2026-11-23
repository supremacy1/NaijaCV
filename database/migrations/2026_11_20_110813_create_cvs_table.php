
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cvs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('title')->default('My Professional CV');

            $table->string('full_name');
            $table->string('professional_title')->nullable();
            $table->string('email');
            $table->string('phone', 30);
            $table->text('address')->nullable();

            $table->text('summary')->nullable();

            $table->string('template')
                ->default('professional');

            $table->boolean('is_paid')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cvs');
    }
};
