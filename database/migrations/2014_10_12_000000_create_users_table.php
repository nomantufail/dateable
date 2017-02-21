<?php

use Illuminate\Support\Facades\Schema;
use Migrations\Migration;

class CreateUsersTable extends Migration
{
    public function fields()
    {
        return ['id','first_name','last_name','email','about', 'gender','birthday','password','fb_id','device_id', 'device_type', 'access_token','remember_token', 'active', 'updated_at','created_at'];
    }
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (\Illuminate\Database\Schema\Blueprint $table) {
            $table->increments('id');
            $table->string('first_name');
            $table->string('last_name');
            $table->tinyInteger('gender');
            $table->date('birthday');
            $table->string('email')->unique();
            $table->text('about')->nullable();
            $table->string('password');
            $table->bigInteger('fb_id');
            $table->text('device_id');
            $table->string('device_type')->default('android');
            $table->rememberToken();
            $table->string('access_token')->default('');
            $table->tinyInteger('active')->default(1);
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
        Schema::dropIfExists('users');
    }
}
