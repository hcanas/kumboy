<?php
namespace App\Traits\Validation;

trait HasUserValidation
{
    private $user_rules = [
        'name' => 'required|max:255',
        'email' => 'required|email|unique:users|max:255',
        'verification_code' => 'required|alpha_num|size:8|valid_code:email',
        'password' => 'required|min:8|max:255',
        'password_confirmation' => 'required|same:password',
    ];

    public function getUserRules(array $fields = [])
    {
        return empty($fields)
            ? $this->user_rules
            : array_intersect_key($this->user_rules, array_flip($fields));
    }
}