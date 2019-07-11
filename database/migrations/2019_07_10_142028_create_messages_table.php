<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('body');
            $table->bigInteger('date');
            $table->integer('user_from');
            $table->string('user_to');
            $table->string('type');
            $table->string('path')->nullable();
            $table->timestamps();
        });

        DB::table('messages')->insert(
            array(
                'body' => 'Александр : Всем',
                'date' => 1562751982230,
                'user_from' => '1',
                'user_to' => '*',
                'type' => 'text',
            )
        );
        DB::table('messages')->insert(
            array(
                'body' => 'Иван : Всем',
                'date' => 1562752982230,
                'user_from' => '2',
                'user_to' => '*',
                'type' => 'text',
            )
        );
        DB::table('messages')->insert(
            array(
                'body' => 'Василий : Всем',
                'date' => 1562751982230,
                'user_from' => '3',
                'user_to' => '*',
                'type' => 'text',
            )
        );
        DB::table('messages')->insert(
            array(
                'body' => 'Александр : Ивану',
                'date' => 1562661982230,
                'user_from' => '1',
                'user_to' => '2',
                'type' => 'text',
            )
        );
        DB::table('messages')->insert(
            array(
                'body' => 'Александр : Василию',
                'date' => 1562661982230,
                'user_from' => '1',
                'user_to' => '3',
                'type' => 'text',
            )
        );
        DB::table('messages')->insert(
            array(
                'body' => 'Иван : Александру',
                'date' => 1562661982230,
                'user_from' => '2',
                'user_to' => '1',
                'type' => 'text',
            )
        );
        DB::table('messages')->insert(
            array(
                'body' => 'Иван : Василию',
                'date' => 1562861982230,
                'user_from' => '2',
                'user_to' => '3',
                'type' => 'text',
            )
        );
        DB::table('messages')->insert(
            array(
                'body' => 'Василий : Александру',
                'date' => 1562861982230,
                'user_from' => '3',
                'user_to' => '1',
                'type' => 'text',
            )
        );
        DB::table('messages')->insert(
            array(
                'body' => 'Василий : Ивану',
                'date' => 1562861982230,
                'user_from' => '3',
                'user_to' => '2',
                'type' => 'text',
            )
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('messages');
    }
}
