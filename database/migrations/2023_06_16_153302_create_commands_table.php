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
        Schema::create('commands', function (Blueprint $table) {
            $table->id();
            $table->boolean('freelancer_done')->nullable();
            $table->boolean('user_satisfaction')->nullable();
            $table->enum('status', ['Done', 'Accepte', 'Refuse'])->nullable();
            $table->string('project_name'); 
            $table->date('delivery_date')->nullable();
            $table->text('description')->nullable();
            $table->text('files')->nullable();
            $table->text('delivery_product')->nullable();
            $table->foreignId('service_id')->constrained('services');
            $table->foreignId('user_id')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commands');
    }
};
