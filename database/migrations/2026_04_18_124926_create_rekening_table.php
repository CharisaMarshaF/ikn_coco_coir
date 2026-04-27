<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('rekening', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100);
            $table->enum('jenis', ['kas','bank']);
            $table->decimal('saldo', 15, 2)->default(0); // 🔥 hanya 1 saldo
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rekening');
    }
};
