<?php

namespace Ageras\CompanyData\Tests;

use Ageras\CompanyData\CompanyService;
use Ageras\CompanyData\Models\Company;

class CompanyServiceTest extends TestCase
{
    public function test()
    {
        $service = new CompanyService();

        $company = $service->companyByVatNumber('33966369', 'dk');

        $this->assertEquals(Company::class, get_class($company));
        $this->assertEquals('AGERAS A/S', $company->company_name);
    }
}
