<?php

namespace TeamTeaTime\Forum\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;

class VoteRequest extends FormRequest {
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array {
        return [
            'post_id' => ['required', 'integer', 'exists:forum_posts,id'],
            'type' => ['required', 'in:upvote,downvote'],
        ];
    }
}
