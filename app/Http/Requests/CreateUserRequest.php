<?php

namespace App\Http\Requests;

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
                Rule::exists('professions', 'id')->where('selectable', true)],
            //'profession_id'=> Rule::exists('professions', 'id')->whereNull('deleted_at'), //Para la prueba (Test Case) only_not_deleted_professions_can_be_selected()
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

            $user = User::create([
                'name'=> $data['name'],
                'email'=> $data['email'],
                'password' => bcrypt($data['password']),
                //'profession_id'=> array_get('profession_id')
                //'profession_id'=> $data['profession_id']?? null,
            ]);

            $user->profile()->create([
                'bio' => $data['bio'],
                'twitter' => $data['twitter'], //Ya no necesitamos usar el operador de fusion de null porque en la validación le dijimos que el campo debe estar presente
                //'twitter' => $data['twitter'] ?? null,
                //'profession_id'=> $data['profession_id']?? null,
                'profession_id'=> $data['profession_id'], //Ya no necesitamos usar el operador de fusion de null porque en la validación le dijimos que el campo debe estar presente
            ]);
        });
    }
}