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
        Schema::create('episodes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('show_id')->constrained()->cascadeOnDelete();
            $table->integer('season');
            $table->integer('episode');
            $table->string('name');
            $table->date('air_date')->nullable();
            $table->text('overview')->nullable();
            $table->boolean('is_new')->default(true);   // unread badge
            $table->boolean('is_seen')->default(false);
            $table->timestamps();

            $table->unique(['show_id', 'season', 'episode']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('episodes');
    }
};
