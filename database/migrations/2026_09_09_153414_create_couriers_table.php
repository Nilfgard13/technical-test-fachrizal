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
        Schema::create('couriers', function (Blueprint $table) {
            $table->id();

            $table->string('name')->index();

            $table->string('phone_number')->unique();

            $table->string('email')->nullable()->unique();

            $table->text('address')->nullable();

            $table->unsignedTinyInteger('level')->index();

            $table->string('status')
                ->default('active');

            $table->date('joined_at')->index();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('couriers');
    }
};
