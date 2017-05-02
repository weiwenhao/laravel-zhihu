<?php

namespace App\Http\Requests;

use Dingo\Api\Http\FormRequest;
use Illuminate\Support\Facades\Request;

class CommentRequest extends FormRequest
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
        $rules =  [
            'content' => 'required',
        ];
        /*if (Request::isMethod('PATCH') || Request::isMethod('PUT')){
            $id = Request::get('id');
        }*/
        return $rules;
    }
}
