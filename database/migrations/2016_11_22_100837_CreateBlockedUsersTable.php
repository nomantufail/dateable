<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class CreateBlockedUsersTable extends \Migrations\Migration
{
    public function fields()
    {
        return ['id','object_id','subject_id','updated_at','created_at'];
    }

    private $table = 'blocked_users';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->increments('id');
            $table->integer('object_id')->unsigned()->index();
            $table->integer('subject_id')->unsigned()->index();
            $table->timestamps();

            $table->foreign('object_id')
                ->references('id')->on('users')
                ->onDelete('cascade');
            $table->foreign('subject_id')
                ->references('id')->on('users')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists($this->table);
    }
}
