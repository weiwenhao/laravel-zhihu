<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Request;

class QuestionRequest extends FormRequest
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
            'title' => ['required', 'max:20', 'unique:questions,title', 'regex:/？|\?$/', ],
            'topic_ids' => 'required',
        ];
        if (Request::isMethod('PATCH') || Request::isMethod('PUT')){
            $id = Request::get('id');
            $rules['title'] = ['required', 'max:20', 'unique:questions,title,'.$id, 'regex:/？|\?$/', ];
            $rules['topic_ids'] = 'required';
        }
        return $rules;
    }

    /**
     * 重写错误信息
     * @return array
     */
    public function messages()
    {
        return [
            'title.regex' => '你还没有给问题添加问号',
            'topic_ids.required' => '必须选择一个话题。',
        ];
    }
}
