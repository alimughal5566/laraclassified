<?php

namespace extras\plugins\reviews\app\Http\Requests;


use App\Http\Requests\Request;

class ReviewRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if ($this->segment(1) == admin_uri()) {
            return auth()->check();
        } else {
            return true;
        }
    }
    
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [];
    
        $rules['comment'] = 'required|min:10|max:1000';
    
        // For FrontOffice
        if ($this->segment(1) != admin_uri()) {
            $rules['rating'] = 'required|integer|between:1,5';
        }
        
        return $rules;
    }
    
    /**
     * @return array
     */
    public function messages()
    {
        return [
            'comment.required' => trans('reviews::messages.validation.comment.required'),
            'comment.min'      => trans('reviews::messages.validation.comment.min'),
            'comment.max'      => trans('reviews::messages.validation.comment.max'),
            'rating.required'  => trans('reviews::messages.validation.rating.required'),
            'rating.integer'   => trans('reviews::messages.validation.rating.integer'),
            'rating.between'   => trans('reviews::messages.validation.rating.between'),
        ];
    }
}
