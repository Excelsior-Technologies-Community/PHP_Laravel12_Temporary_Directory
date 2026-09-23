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
        Schema::create('temporary_file_activities', function (Blueprint $table) {
            $table->id();

            $table->string('file_name');

            $table->string('file_type', 20)
                ->default('TXT');

            $table->string('operation', 50);

            $table->unsignedBigInteger('file_size')
                ->default(0);

            $table->string('status', 30)
                ->default('Success');

            $table->timestamps();

            $table->index('file_name');
            $table->index('file_type');
            $table->index('operation');
            $table->index('status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('temporary_file_activities');
    }
};