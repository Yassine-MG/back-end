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
        Schema::create('freelancers', function (Blueprint $table) {
            $table->id();
            $table->string('first name');
            $table->string('last name');
            $table->string('displayed name');
            $table->text('description');
            $table->string('languages');
            $table->string('Occupation')->nullable();
            $table->string('phone',30);
            $table->string('personal website')->nullable();
            $table->text('photo');
            $table->string('education')->nullable();
            $table->string('certification')->nullable();
            $table->foreignId('user_id')->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('freelancers');
    }
};
