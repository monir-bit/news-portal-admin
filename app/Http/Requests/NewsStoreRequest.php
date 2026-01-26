<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NewsStoreRequest extends FormRequest
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
            'shoulder' => ['nullable', 'string'],
            'title' => ['required', 'string', 'max:255'],
            'ticker' => ['nullable', 'string'],
            'sort_description' => ['required', 'string'],
            'proofreader' => ['nullable', 'integer'],
            'image' => ['nullable', 'max:255', 'image'],
            'timeline_id' => ['nullable', 'exists:timelines,id'],
            'published' => ['boolean'],
            'latest' => ['boolean'],
            'news_marquee' => ['boolean'],
            'live_news' => ['boolean'],
            'is_visible_shoulder' => ['boolean'],
            'is_visible_ticker' => ['boolean'],
            'date' => ['required', 'date'],

            'details' => ['nullable', 'string'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['string'],
            'category_id' => ['nullable' , 'numeric', 'exists:categories,id'],
            'section_layout_id' => ['nullable'],
            'section_layout_news_position' => ['nullable', 'integer'],
            'is_pinned' => ['boolean'],
        ];
    }
}
