<?php
namespace App\Traits\Validation;

trait HasProductValidation
{
    private $product_rules = [
        'name' => 'required|max:255',
        'category' => 'required|product_category',
        'specifications' => 'required|product_specifications',
        'qty' => 'required|integer|min:0',
        'price' => 'required|numeric|min:0',
    ];

    public function getProductRules(array $fields = [])
    {
        return empty($fields)
            ? $this->product_rules
            : array_intersect_key($this->product_rules, array_flip($fields));
    }
}