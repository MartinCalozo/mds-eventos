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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('invitation_redemption_id');
            $table->string('code')->unique();
            $table->boolean('used')->default(false);
            $table->unsignedBigInteger('validated_by')->nullable();
            $table->dateTime('validated_at')->nullable();
            $table->timestamps();

            $table->foreign('invitation_redemption_id')
                ->references('id')->on('invitation_redemptions')
                ->onDelete('cascade');

            $table->foreign('validated_by')
                ->references('id')->on('users')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
