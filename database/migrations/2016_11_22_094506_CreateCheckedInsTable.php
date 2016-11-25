<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCheckedInsTable extends Migration
{
    private $table = "checked_ins";

    public function fields()
    {
        return ['id','user_id', 'lat','long','checked_in','checked_out','created_at','updated_at'];
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->index();
            $table->double('lat')->default(0);
            $table->double('long')->default(0);
            $table->dateTime('checked_in');
            $table->dateTime('checked_out');
            $table->timestamps();

            $table->foreign('user_id')
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
