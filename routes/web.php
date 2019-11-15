<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/usuarios', 'UserController@index')
    ->name('users.index');

Route::get('/usuarios/{user}', 'UserController@show')
    ->where('user', '[0-9]+')
    ->name('users.show');

Route::get('/usuarios/nuevo', 'UserController@create')
    ->name('users.create');

Route::post('/usuarios', 'UserController@store')
    ->name('users.store');

Route::get('usuarios/{user}/editar', 'UserController@edit')
    ->name('users.edit');

Route::put('/usuarios/{user}', 'UserController@update')
    ->name('users.update');

Route::delete('/usuarios/{id}', 'UserController@destroy')
    ->name('users.destroy');

Route::patch('/usuarios/{user}/trash', 'UserController@trash')
    ->name('users.trash');

Route::get('/usuarios/destroyOldUsers', 'UserController@destroyOldTrashedUsers')
    ->name('users.destroyOldUsers');


Route::get('/usuarios/{id}/restore', 'UserController@restore')
    ->name('user.restore');


Route::get('/saludo/{name}/{nickname?}', 'WelcomeUserController');


/*Route::get('/usuarios/{user_id}/edit', function ($user_id){
   return "Editamos el usuario {$user_id}";
})->where('user_id',  '[\d+]');*/
Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');


// Profile
Route::get('/editar-perfil/', 'ProfileController@edit');

Route::put('/editar-perfil/', 'ProfileController@update');

// Professions
Route::get('/professions/', 'ProfessionController@index')
    ->name('profession.index');

Route::patch('/profession/{profession}/trash', 'ProfessionController@trash')
    ->name('profession.trash');

Route::delete('/profession/{id}', 'ProfessionController@destroy')
    ->name('profession.destroy');

Route::get('/profession/{id}/restore', 'ProfessionController@restore')
    ->name('profession.restore');

// Skills
Route::get('/skills/', 'SkillController@index')
    ->name('skill.index');

Route::delete('/skill/{id}', 'SkillController@destroy')
    ->name('skill.destroy');

Route::patch('/skill/{skill}/trash', 'SkillController@trash')
    ->name('skill.trash');

Route::get('/skill/{id}/restore', 'SkillController@restore')
    ->name('skill.restore');

//Trashed items
Route::get('/trahed-items/', 'TrashController@index')
    ->name('trashed.index');