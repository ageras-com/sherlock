<?php

namespace Ageras\CompanyData\Providers;

interface CompanyProviderInterface
{
    public function __construct($geoCode);

    /**
     * @param $vatNumber
     * @return \Ageras\CompanyData\Models\Company
     */
    public function companyByVatNumber($vatNumber);

    /**
     * @param $name
     * @return array
     */
    public function companiesByName($name);
}
