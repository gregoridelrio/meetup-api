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
        Schema::create('matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organizer_id')->constrained('users')->onDelete('cascade');
            $table->string('description')->nullable();
            $table->timestamp('starts_at');
            $table->integer('duration');
            $table->enum('match_type', ['5v5', '7v7', '11v11']);
            $table->integer('max_players');
            $table->enum('required_level', ['beginner', 'intermediate', 'advanced']);
            $table->decimal('price', 8, 2)->default(0);
            $table->string('location_name', 100);
            $table->string('address');
            $table->string('city', 100);
            $table->enum('status', ['open', 'full', 'cancelled'])->default('open');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('matches');
    }
};
