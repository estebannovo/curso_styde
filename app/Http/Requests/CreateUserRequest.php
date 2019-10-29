<?php

namespace App\Http\Requests;

use App\Profession;
use App\Role;
use App\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use phpDocumentor\Reflection\Types\Nullable;

class CreateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required',
            'email' => ['required','email','unique:users,email'],
            'password' => 'required|min:6',
            'bio' => 'required',
            'twitter' =>  ['nullable', 'url', 'present'],
            //'profession_id'=> 'exists:professions,id',
            'profession_id'=> [
                'nullable', 'present',
                //'nullable',
                Rule::exists('professions', 'id')->where('selectable', true),
                'required_without:other_profession'
            ],
            //'profession_id'=> Rule::exists('professions', 'id')->whereNull('deleted_at'), //Para la prueba (Test Case) only_not_deleted_professions_can_be_selected()
            'other_profession' => 'required_without:profession_id',
            'skills' => [
                'array',
                Rule::exists('skills', 'id')
            ],
            'role'=> ['nullable', Rule::in(Role::getList())],
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'The field name is required',
            'email.required' => 'The field email is required',
            'password.required' => 'The field password is required'
        ];
    }

    public function createUser(){
        DB::transaction(function (){
            $data = $this->validated();

            //Si no viene una profession_id es porque viene other_profession, entonces, creamos la nueva proffesion para poder insertarla
            if( is_null($data['profession_id'])){
                $profession_id = Profession::create([
                    'title'=> $data['other_profession'],
                ])->id;
            }else{
                $profession_id = $data['profession_id'];
            }

            $user = new User([
                'name'=> $data['name'],
                'email'=> $data['email'],
                'password' => bcrypt($data['password']),
                //'profession_id'=> array_get('profession_id')
                //'profession_id'=> $data['profession_id']?? null,
            ]);

            $user->role = $data['role']??'user';

            $user->save();

            $user->profile()->create([
                'bio' => $data['bio'],
                'twitter' => $data['twitter'], //Ya no necesitamos usar el operador de fusion de null porque en la validación le dijimos que el campo debe estar presente
                //'twitter' => $data['twitter'] ?? null,
                //'profession_id'=> $data['profession_id']?? null,
                'profession_id'=> $profession_id, //Ya no necesitamos usar el operador de fusion de null porque en la validación le dijimos que el campo debe estar presente
            ]);

            if( !empty($data['skills'])){
                $user->skills()->attach($data['skills']);
            }
        });
    }
}