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
        Schema::create('invitation_redemptions', function (Blueprint $table) {
            $table->id();
            $table->string('invitation_id');
            $table->string('hash')->unique();
            $table->unsignedBigInteger('event_id');
            $table->integer('guest_count');
            $table->timestamps();

            $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invitation_redemptions');
    }
};
