<?php

namespace Ageras\CompanyData\Models;

class Company
{
    protected $attributes = [
        '',
    ];

    protected $data;

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }
}
