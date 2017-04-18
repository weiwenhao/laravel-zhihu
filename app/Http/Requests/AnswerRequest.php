<?php

namespace App\Http\Requests;

use Dingo\Api\Http\FormRequest;
use Illuminate\Support\Facades\Request;

class AnswerRequest extends FormRequest
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

    /**
     * 重写错误信息
     * @return array
     */
    public function messages()
    {
        return [
            'title.regex' => '你还没有给问题添加问号。',
            'topic_ids.required' => '必须选择一个话题。',
        ];
    }
}
