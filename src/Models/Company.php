<?php

namespace Ageras\Sherlock\Models;

class Company
{
    protected $attributes = [
        'company_name',
        'company_status',
        'company_bankrupt'
    ];

    protected $data;

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    public function getAttribute($key)
    {
        if(in_array($key, $this->attributes)) {
            return $this->data[$key];
        }
    }

    public function __get($key)
    {
        return $this->getAttribute($key);
    }
}
