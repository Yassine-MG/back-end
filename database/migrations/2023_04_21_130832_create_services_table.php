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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->text('details')->nullable();
            $table->text('offer_name')->nullable();
            $table->string('image1')->nullable();
            $table->string('image2')->nullable();
            $table->string('image3')->nullable();
            $table->string('video')->nullable();
            $table->float('price',8, 2)->nullable();
            $table->string('category');
            $table->json('skills')->nullable();
            $table->text('delevery')->nullable();
            $table->integer('like')->nullable();
            $table->integer('dislike')->nullable();
            $table->string('tags')->nullable();
            $table->json('dynamic_inputs')->nullable();
            $table->foreignId('freelancer_id')->constrained('freelancers');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
