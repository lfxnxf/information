<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContentAddRequest extends FormRequest
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
        return [
            'title'          => 'required',
            'img_head'       => 'required',
            'content'        => 'required',
            'type'           => 'required',
            'create_user_id' => 'required',
            'source'         => 'required',
            'category'       => 'required',
        ];
    }

    /**
     * @return array
     */
    public function messages()
    {
        return [
            'title.required'          => '标题不能为空',
            'img_head.required'       => '封面不能为空',
            'content.required'        => '文章内容不能为空',
            'type.required'           => '文章类型不能为空',
            'create_user_id.required' => '创建人id不能为空',
            'source.required'         => '来源不能为空',
            'category.required'       => '分类不能为空',
        ];
    }
}
