<?php

namespace App\Http\Requests;

use App\Role;
use App\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
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
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($this->user) // Esta es para q se puede actualizar el mail, para este id
            ],
            'password' => '',
            //'role'=> ['required', Rule::in(Role::getList())],
            'role'=> [Rule::in(Role::getList())],
            'bio' => 'required',
            'profession_id'=> [
                'nullable', 'present',
                //'nullable',
                Rule::exists('professions', 'id')->where('selectable', true),
            ],
            'twitter' =>  ['nullable', 'url', 'present'],
            'skills' => [
                'array',
                Rule::exists('skills', 'id')
            ],
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'The field name is required',
            'email.required' => 'The field email is required',
        ];
    }

    public function updateUser(User $user)
    {
        $data = $this->validated();

        if ($data['password'] != null) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }

        $user->fill($data);
        $user->role = $data['role'] ?? 'user';
        $user->save();

        $user->profile->update($data);

        $user->skills()->sync($data['skills'] ?? []);
    }
}