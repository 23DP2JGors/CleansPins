<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class LietotajsRequest extends FormRequest
{
    /**
     * Lietotāja konta validācija administratoram (tabula "lietotaji").
     * Parole tiek prasīta tikai, izveidojot kontu — paroles maiņa nav šī kontroliera darbs.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $lietotajs = $this->route('lietotaji'); // null, ja veido jaunu

        $rules = [
            'vards' => ['required', 'string', 'max:30'],
            'uzvards' => ['required', 'string', 'max:30'],
            'e_pasts' => [
                'required',
                'string',
                'email',
                'max:30',
                Rule::unique('lietotaji', 'e_pasts')->ignore($lietotajs?->lietotaja_id, 'lietotaja_id'),
            ],
            'adrese' => ['nullable', 'string', 'max:255'],
            'loma' => ['required', 'integer', Rule::in([0, 1])],
        ];

        if (! $lietotajs) {
            $rules['parole'] = ['required', 'string', Password::defaults()];
        }

        return $rules;
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
            'loma.in' => 'Nederīga loma.',
            'parole.required' => 'Lūdzu, ievadi sākotnējo paroli.',
        ];
    }
}
