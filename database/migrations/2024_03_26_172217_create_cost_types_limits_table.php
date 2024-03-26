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
        Schema::create('cost_type_limits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cost_type_id')->constrained('cost_types');
            $table->foreignId('user_id')->constrained('users');
            $table->double('weekly_limit')->nullable();
            $table->double('monthly_limit')->nullable();
            $table->double('quarter_limit')->nullable();
            $table->double('yearly_limit')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cost_type_limits');
    }
};
