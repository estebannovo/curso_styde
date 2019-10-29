<?php

namespace Tests;

use App\Profession;

trait TestHelpers
{
    protected function assertDatabaseEmpty($table, $connection = null){
        $total = $this->getConnection($connection)->table($table)->count();
        $this->assertSame(0, $total, sprintf(
            "Failed asserting the table [%s] is empty. %s %s found.", $table, $total, str_plural('row', $total)
        ));
    }

    /**
     * @return array
     */
    protected function withData(array $custom = []): array
    {

        //dd($this->profession);

        /*return array_filter(array_merge([
            'name' => 'Esteban Novo',
            'email' => 'novo.esteban@gmail.com',
            'password' => 'laravel',
            'profession_id' => $this->profession->id,
            'bio' => 'Programador de Laravel y Vue.js',
            'twitter' => 'https://twitter/estebannovo'
        ], $custom));*/

        //Quitamos el array_filter para que no se eliminen las llaves con valor null ya que en la validación se indica que el campo debe estar presente
        return  array_merge($this->defaultData(), $custom);
    }

    protected function defaultData()
    {
        $this->profession = factory(Profession::class)->create();
        $this->defaultData = array_merge($this->defaultData, [
            'profession_id' => $this->profession->id,
        ]);

        return $this->defaultData;
    }
}