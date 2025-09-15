<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCourseRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
         return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:255',

            'modules' => 'required|array|min:1',
            'modules.*.title' => 'required|string|max:255',
            'modules.*.description' => 'nullable|string',
            'modules.*.position' => 'nullable|integer',

            'modules.*.contents' => 'nullable|array',
            'modules.*.contents.*.type' => 'required_with:modules.*.contents|string|in:text,image,video,link,html',
            'modules.*.contents.*.text' => 'nullable|string',
            'modules.*.contents.*.video_url' => 'nullable|url',
            'modules.*.contents.*.link' => 'nullable|url',
            'modules.*.contents.*.image' => 'nullable|file|image|max:5120', 
            'modules.*.contents.*.position' => 'nullable|integer',
        ];
    }

    public function messages()
    {
        return [
            'modules.required' => 'You must add at least one module.',
            'modules.*.title.required' => 'Each module needs a title.',
            'modules.*.contents.*.type.required_with' => 'Content type is required.',
            'modules.*.contents.*.type.in' => 'Content type must be: text, image, video, link, or html.',
            'modules.*.contents.*.image.max' => 'Image file size cannot exceed 5MB.',
            'modules.*.contents.*.video_url.url' => 'Please enter a valid video URL.',
            'modules.*.contents.*.link.url' => 'Please enter a valid link URL.',
        ];

    }
}
