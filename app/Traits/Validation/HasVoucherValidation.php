<?php
namespace App\Traits\Validation;

trait HasVoucherValidation
{
    private $voucher_rules = [
        'code' => 'required|alpha_num|voucher_code',
        'amount' => 'required|numeric|min:0|voucher_amount',
        'type' => 'required|in:Flat Amount,Percentage',
        'categories' => 'required|array',
        'categories.*' => 'product_category',
        'limit_per_user' => 'required|numeric|min:0|lte:qty',
        'qty' => 'required|numeric|min:0',
        'valid_from' => 'required|date|after_or_equal:today',
        'valid_to' => 'required|date|after_or_equal:valid_from',
    ];

    public function getVoucherRules(array $fields = [])
    {
        return empty($fields)
            ? $this->voucher_rules
            : array_intersect_key($this->voucher_rules, array_flip($fields));
    }
}