<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EndGameRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_id' => 'required|integer|exists:users,id',
            'game_id' => [
                'required',
                'integer',
                Rule::exists('games', 'id')->where(function ($query) {
                    $query->where('user_id', $this->request->get('user_id'));
                }),
            ],
            'score' => 'required|integer|min:0|max:1000',
        ];
    }
}
