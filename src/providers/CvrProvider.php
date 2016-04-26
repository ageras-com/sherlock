<?php

namespace Ageras\CompanyData\Providers;

use Ageras\CompanyData\Models\Company;
use HttpRequest;

class CvrProvider implements CompanyProviderInterface
{
    public function __construct($geoCode)
    {
    }

    public function companyByVatNumber($vatNumber)
    {
        //
    }

    public function companiesByName($name)
    {
        //
    }

    protected function query($string)
    {
        $request = new HttpRequest($this->serviceUrl, HttpRequest::METH_GET, [
            ''
        ]);

        $request->setQueryData([
            'q' => $string
        ]);
    }

    protected function formatResult($result)
    {
        return new Company([
            '' => '',
            '' => '',
            '' => '',
            '' => '',
            '' => '',
            '' => '',
            '' => '',
        ]);
    }
}
