<?php

namespace Ageras\CompanyData;

use Ageras\CompanyData\Providers\CompanyProviderInterface;
use Ageras\CompanyData\Providers\CvrProvider;

class CompanyService
{
    protected $providers = [
        'dk' => CvrProvider::class,
    ];

    public function companyByVatNumber($vatNumber, $geoCode)
    {
        /** @var CompanyProviderInterface $provider */
        foreach ($this->providers($geoCode) as $provider) {
            $provider = new $provider;
        }
    }

    public function providers($geoCode)
    {
        return (array)$this->providers[$geoCode];
    }
}
