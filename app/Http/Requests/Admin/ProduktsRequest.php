<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ProduktsRequest extends FormRequest
{
    /**
     * Produkta validācija (tabula "produkti" + zonas/problēmas starptabulām).
     * Piekļuvi jau pārbauda 'admin' starpprogrammatūra.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'kategorijas_id' => ['required', 'integer', 'exists:kategorijas,kategorijas_id'],
            'nosaukums' => ['required', 'string', 'max:255'],
            'apraksts' => ['nullable', 'string'],
            'cena' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'krajums' => ['required', 'integer', 'min:0'],
            'attels' => ['nullable', 'string', 'max:255'],
            'zonas' => ['array'],
            'zonas.*' => ['integer', 'distinct', 'exists:zonas,zonas_id'],
            'problemas' => ['array'],
            'problemas.*' => ['integer', 'distinct', 'exists:problemas,problemas_id'],
        ];
    }

    public function messages(): array
    {
        return [
            'kategorijas_id.required' => 'Lūdzu, izvēlies kategoriju.',
            'kategorijas_id.exists' => 'Izvēlētā kategorija neeksistē.',
            'nosaukums.required' => 'Lūdzu, ievadi nosaukumu.',
            'nosaukums.max' => 'Nosaukums nedrīkst būt garāks par 255 rakstzīmēm.',
            'cena.required' => 'Lūdzu, ievadi cenu.',
            'cena.numeric' => 'Cenai jābūt skaitlim.',
            'cena.min' => 'Cena nedrīkst būt negatīva.',
            'cena.max' => 'Cena ir pārāk liela.',
            'krajums.required' => 'Lūdzu, ievadi krājumu.',
            'krajums.integer' => 'Krājumam jābūt veselam skaitlim.',
            'krajums.min' => 'Krājums nedrīkst būt negatīvs.',
            'attels.max' => 'Attēla ceļš nedrīkst būt garāks par 255 rakstzīmēm.',
            'zonas.*.exists' => 'Izvēlētā zona neeksistē.',
            'problemas.*.exists' => 'Izvēlētā problēma neeksistē.',
        ];
    }
}
