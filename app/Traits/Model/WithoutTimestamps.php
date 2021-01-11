<?php
namespace App\Traits\Model;

trait WithoutTimestamps
{
    public function usesTimestamps()
    {
        return false;
    }
}