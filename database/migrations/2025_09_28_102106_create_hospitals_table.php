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
        Schema::create('hospitals', function (Blueprint $table) {
        $table->id();
        $table->string('hospcode',191)->unique();   // รหัส รพ. เช่น 10989
        $table->string('name');
        $table->string('token_api');
        $table->string('contact');
        $table->string('ip_whitelist')->nullable(); // ถ้าต้องการจำกัด IP
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hospitals');
    }
};
