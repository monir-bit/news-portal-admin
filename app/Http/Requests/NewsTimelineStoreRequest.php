<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NewsTimelineStoreRequest extends FormRequest
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
            'news_id' => 'required|exists:news,id',
            'title' => 'required|string|max:500',
            'details' => 'required|string',
            'date' => 'required|date',
        ];
    }
}

