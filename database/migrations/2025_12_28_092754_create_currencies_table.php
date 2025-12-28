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
        Schema::create('currencies', function (Blueprint $table): void {
            $table->id();
            $table->tinyText('code');
            $table->string('name');
            $table->tinyText('symbol');
            $table->unsignedTinyInteger('decimal');
            $table->tinyText('decimal_separator');
            $table->tinyText('group_separator');
            $table->tinyText('currency_position');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('currencies');
    }
};
