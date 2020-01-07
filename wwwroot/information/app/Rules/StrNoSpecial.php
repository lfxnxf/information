<?php

/**
 * Created by PhpStorm.
 * User: xuefeng
 * Date: 2019/9/25
 * Time: 14:50
 */

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class StrNoSpecial implements Rule
{

    public $attribute = null;

    const CONTACT_NAME = 'contact_name';
    const TITLE = 'title';

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $this->attribute = $attribute;
        switch ($attribute) {
            case 'contact_name':
                return preg_match("/^[\x7f-\xffa-zA-Z\s*]+$/", $value, $m);
            default:
                return preg_match("/^[\x7f-\xffa-zA-Z（）《》]+$/", $value, $m);
        }

    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return self::showText($this->attribute) . '{' . $this->attribute . '}不符合规则';
    }

    /**
     * @param $attribute
     * @return mixed
     */
    public static function showText($attribute)
    {
        $_Text = [
            self::CONTACT_NAME => '联系人',
            self::TITLE => '客户名称',
        ];
        return $_Text[$attribute];
    }
}
