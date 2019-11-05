<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Profession extends Model
{
    protected $table = 'professions'; // si el nombre de la tabla es diferente al de la clase segun la nomeclatura se puede especificar aquí el nombre de la tabla.

    public $timestamps = true; // False deshabilita la escritura en los cmapos created_at and updated_at

    protected $fillable = ['title']; //Atributos o campos que vamos a permitir cargar de forma masiva, o se aa travez de un array asociativo, con el metodo create entre otros metodos.

    public function profiles()
    {
        return $this->hasMany(UserProfile::class);
    }
}
