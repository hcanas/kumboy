<?php
namespace App\Traits\Validation;

trait HasUserAddressValidation
{
    private $user_address_rules = [
        'label' => 'required',
        'contact_person' => 'required',
        'contact_number' => 'required|contact_number',
        'address' => 'required',
        'map_coordinates' => 'required',
        'map_address' => 'required',
    ];

    public function getUserAddressRules(array $fields = [])
    {
        return empty($fields)
            ? $this->user_address_rules
            : array_intersect_key($this->user_address_rules, array_flip($fields));
    }
}