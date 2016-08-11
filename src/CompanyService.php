<?php

namespace Ageras\Sherlock;

use Ageras\Sherlock\Exceptions\EmptyResult;
use Ageras\Sherlock\Exceptions\SingleResultExpected;
use Ageras\Sherlock\Providers\CompanyProviderInterface;
use Ageras\Sherlock\Providers\VirkProvider;
use Ageras\Sherlock\Providers\VIESProvider;

class CompanyService
{
    protected $providers = [
        'dk' => VirkProvider::class,
        'nl' => VIESProvider::class,
        'de' => VIESProvider::class,
        'se' => VIESProvider::class,
    ];

    /**
     * This method returns a single Company. If the API
     * returns more than one result, a SingleResultExpected
     * is thrown. The method returns null if no result
     * was returned by the API.
     *
     * @param $vatNumber
     * @param $geoCode
     *
     * @throws SingleResultExpected
     *
     * @return Company|null
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
     *
     * @throws EmptyResult|SingleResultExpected
     *
     * @return Company
     */
    public function companyByVatNumberOrFail($vatNumber, $geoCode)
    {
        $result = $this->companyByVatNumber($vatNumber, $geoCode);

        /* Throw exception if no company was found */
        if (is_null($result)) {
            throw new EmptyResult();
        }

        return $result;
    }

    /**
     * Returns an array of companies.
     *
     * @param $vatNumber
     * @param $geoCode
     *
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
     *
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
     * Returns an array of providers based on geo code.
     *
     * @param $geoCode
     *
     * @return array
     */
    public function providers($geoCode)
    {
        if (! isset($this->providers[$geoCode])) {
            return [];
        }

        return (array) $this->providers[$geoCode];
    }
}
