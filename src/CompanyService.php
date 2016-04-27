<?php

namespace Ageras\CompanyData;

use Ageras\CompanyData\Models\EmptyResult;
use Ageras\CompanyData\Providers\CompanyProviderInterface;
use Ageras\CompanyData\Providers\CvrProvider;
use Dotenv\Dotenv;

class CompanyService
{
    protected $providers = [
        'dk' => CvrProvider::class,
    ];

    public function __construct()
    {
        $baseDir = dirname(dirname(__FILE__));
        (new Dotenv($baseDir))->load();
    }

    public function companyByVatNumber($vatNumber, $geoCode)
    {
        $result = null;
        foreach ($this->providers($geoCode) as $provider) {
            /** @var CompanyProviderInterface $provider */
            $provider = new $provider($geoCode);
            $result = $provider->companyByVatNumber($vatNumber);
        }

        return $result;
    }

    public function companyByVatNumberOrFail($vatNumber, $geoCode)
    {
        $result = $this->companyByVatNumber($vatNumber, $geoCode);

        /* Throw exception if no company was found */
        if(is_null($result)) {
            throw new EmptyResult();
        }
        
        return $result;
    }

    public function companiesByVatNumber($vatNumber, $geoCode)
    {
        $result = [];
        foreach ($this->providers($geoCode) as $provider) {
            /** @var CompanyProviderInterface $provider */
            $provider = new $provider($geoCode);
            $result = array_merge(
                $result,
                $provider->companiesByVatNumber($vatNumber)
            );
        }

        return $result;
    }

    public function companiesByName($name, $geoCode)
    {
        $result = [];
        foreach ($this->providers($geoCode) as $provider) {
            /** @var CompanyProviderInterface $provider */
            $provider = new $provider($geoCode);
            $result = array_merge(
                $result,
                $provider->companiesByName($name)
            );
        }

        return $result;
    }

    public function providers($geoCode)
    {
        return (array)$this->providers[$geoCode];
    }
}
