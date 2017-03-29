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
        /*if (Request::isMethod('PATCH') || Request::isMethod('PUT')){
            $id = Request::get('id');
            $rules['id'] = 'required';
            $rules['title'] = 'required|alpha|unique:roles,name,'.$id;
            $rules['display_name'] = 'required|unique:roles,display_name,'.$id;
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
            'title.regex' => '你还没有给问题添加问号',
            'topic_ids.required' => '必须选择一个话题。',
        ];
    }
}
