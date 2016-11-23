<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRanksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ranks', function(Blueprint $table) {   
            $table->string('title',80)->primary();
            $table->float('order'); 
            $table->string('directParent',80)->nullable();
            $table->string('mainParent',80)->nullable();
            $table->smallInteger('isMainRank')->unsigned();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('ranks');
    }
}
