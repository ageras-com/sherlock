<?php

namespace Ageras\CompanyData\Providers;

use Ageras\CompanyData\Models\Company;
use Ageras\CompanyData\Models\EmptyResult;
use Ageras\CompanyData\Models\SingleResultExpected;

interface CompanyProviderInterface
{
    /**
     * Use the $geoCode parameter to identify the
     * country of which the companies should
     * be received, if the API allows
     * more than one country.
     *
     * @param $geoCode
     */
    public function __construct($geoCode);

    /**
     * This method returns a single result. If the API
     * returns more than one result, a SingleResultExpected
     * exception should be thrown. The method returns null
     * if no result was returned by the API.
     *
     * @param $vatNumber
     * @return Company|null
     * @throws SingleResultExpected
     */
    public function companyByVatNumber($vatNumber);

    /**
     * Returns an array of companies.
     *
     * @param $vatNumber
     * @return array
     */
    public function companiesByVatNumber($vatNumber);

    /**
     * Returns an array of companies.
     *
     * @param $name
     * @return array
     */
    public function companiesByName($name);
}
