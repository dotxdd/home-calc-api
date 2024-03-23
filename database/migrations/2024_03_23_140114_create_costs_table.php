<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCostsTable extends Migration
{
    public function up()
    {
        Schema::create('costs', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->foreignId('cost_type_id')->constrained('cost_types');
            $table->foreignId('user_id')->constrained('users');
            $table->text('desc');
            $table->float('price');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('costs');
    }
}
