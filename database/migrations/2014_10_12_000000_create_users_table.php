<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        DB::table('users')->insert(
            array(
                'name' => 'Александр',
                'email' => '1@domain.com',
                'password' => Illuminate\Support\Facades\Hash::make('00000000')
            )
        );
        DB::table('users')->insert(
            array(
                'name' => 'Иван',
                'email' => '2@domain.com',
                'password' => Illuminate\Support\Facades\Hash::make('00000000')
            )
        );
        DB::table('users')->insert(
            array(
                'name' => 'Василий',
                'email' => '3@domain.com',
                'password' => Illuminate\Support\Facades\Hash::make('00000000')
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
        Schema::dropIfExists('users');
    }
}
