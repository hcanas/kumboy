<?php
namespace App\Traits\Validation;

trait HasStoreApplicationValidation
{
    private $store_application_rules = [
        'name' => 'required|store_application',
        'contact_number' => 'required|contact_number',
        'address' => 'required',
        'map_coordinates' => 'required',
        'map_address' => 'required',
        'open_until' => 'required|date|after:today',
        'attachment' => 'required|file|mimetypes:application/pdf'
    ];

    public function getStoreApplicationRules(array $fields = [])
    {
        return empty($fields)
            ? $this->store_application_rules
            : array_intersect_key($this->store_application_rules, array_flip($fields));
    }
}