<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('profession_id')->nullable();
            $table->foreign('profession_id')
                ->references('id')
                ->on('professions');
                //->onDelete('CASCADE') //Borra el registro de la tabla user_profiles cuando borro una profession;
                //->onDelete('SET NULL'); //Asigna null al profession_id en la tabla user_profiles cuando es borrada una profession
            $table->string('bio', 1000);
            $table->string('twitter')->nullable();
            $table->unsignedInteger('user_id');

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('CASCADE'); //Cuando borramos un usuario si tenemos que borrar todd su información relacionada por la nueva ley Europea GDPR.

            $table->timestamps();

            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_profiles');
    }
}
