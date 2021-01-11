<?php
namespace App\Traits\Validation;

trait HasStoreTransferValidation
{
    private $store_transfer_rules = [
        'email' => 'required|email|exists:users',
        'attachment' => 'required|file|mimetypes:application/pdf'
    ];

    public function getStoreTransferRules(array $fields = [])
    {
        return empty($fields)
            ? $this->store_transfer_rules
            : array_intersect_key($this->store_transfer_rules, array_flip($fields));
    }
}