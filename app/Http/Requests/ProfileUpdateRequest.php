<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Lietotāja profila datu validācija (tabula "lietotaji").
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'vards' => ['required', 'string', 'max:30'],
            'uzvards' => ['required', 'string', 'max:30'],
            'e_pasts' => [
                'required',
                'string',
                'email',
                'max:30',
                // e-pastam jābūt unikālam, izņemot paša lietotāja ierakstu
                Rule::unique('lietotaji', 'e_pasts')->ignore($this->user()->lietotaja_id, 'lietotaja_id'),
            ],
            'adrese' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'vards.required' => 'Lūdzu, ievadi vārdu.',
            'vards.max' => 'Vārds nedrīkst būt garāks par 30 rakstzīmēm.',
            'uzvards.required' => 'Lūdzu, ievadi uzvārdu.',
            'uzvards.max' => 'Uzvārds nedrīkst būt garāks par 30 rakstzīmēm.',
            'e_pasts.required' => 'Lūdzu, ievadi e-pastu.',
            'e_pasts.email' => 'Nederīga e-pasta adrese.',
            'e_pasts.max' => 'E-pasts nedrīkst būt garāks par 30 rakstzīmēm.',
            'e_pasts.unique' => 'Šis e-pasts jau ir reģistrēts.',
            'adrese.max' => 'Adrese nedrīkst būt garāka par 255 rakstzīmēm.',
        ];
    }
}
