<?php

namespace Ageras\Sherlock\Models;

class Company
{
    protected $attributes = [
        'company_name',
        'company_status',
        'company_registration_number',
        'company_vat_number',
        'company_address',
        'company_city',
        'company_postcode',
        'company_phone_number',
        'company_email',
    ];

    protected $data;

    const COMPANY_STATUS_NORMAL = 1;
    const COMPANY_STATUS_ACTIVE = 2;
    const COMPANY_STATUS_CEASED = 3;
    const COMPANY_STATUS_IN_BANKRUPTCY = 4;
    const COMPANY_STATUS_FORCED_DISSOLVED = 5;
    const COMPANY_STATUS_DISSOLVED_UNDER_VOLUNTARY_LIQUIDATION = 6;
    const COMPANY_STATUS_DISSOLVED_FOLLOWING_STATEMENT = 7;
    const COMPANY_STATUS_UNDER_VOLUNTARY_LIQUIDATION = 8;
    const COMPANY_STATUS_DISSOLVED_AFTER_BANKRUPTCY = 9;

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    public function getAttribute($key)
    {
        if (in_array($key, $this->attributes)) {
            return $this->data[$key];
        }
    }

    public function __get($key)
    {
        return $this->getAttribute($key);
    }
}
