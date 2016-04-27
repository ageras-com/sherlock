<?php

namespace Ageras\CompanyData;

use Ageras\CompanyData\Exceptions\EmptyResult;
use Ageras\CompanyData\Exceptions\SingleResultExpected;
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

    /**
     * This method returns a single Company. If the API
     * returns more than one result, a SingleResultExpected
     * is thrown. The method returns null if no result
     * was returned by the API.
     *
     * @param $vatNumber
     * @return Company|null
     * @throws SingleResultExpected
     */
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

    /**
     * This method does the same as companyByVatNumber, but
     * throws an EmptyResult exception if no Company was
     * returned by the API.
     *
     * @param $vatNumber
     * @param $geoCode
     * @return Company
     * @throws EmptyResult|SingleResultExpected
     */
    public function companyByVatNumberOrFail($vatNumber, $geoCode)
    {
        $result = $this->companyByVatNumber($vatNumber, $geoCode);

        /* Throw exception if no company was found */
        if(is_null($result)) {
            throw new EmptyResult();
        }
        
        return $result;
    }

    /**
     * Returns an array of companies.
     *
     * @param $vatNumber
     * @param $geoCode
     * @return array
     */
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


    /**
     * Returns an array of companies.
     *
     * @param $name
     * @param $geoCode
     * @return array
     */
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

    /**
     * Returns an array of providers based on geo code
     *
     * @param $geoCode
     * @return array
     */
    public function providers($geoCode)
    {
        if(!isset($this->providers[$geoCode])) {
            return [];
        }

        return (array)$this->providers[$geoCode];
    }
}