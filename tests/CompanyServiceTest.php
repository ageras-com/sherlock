<?php

namespace Ageras\Sherlock\Tests;

use Ageras\Sherlock\CompanyService;
use Ageras\Sherlock\Models\Company;
use Dotenv\Dotenv;

class CompanyServiceTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();
        $baseDir = dirname(dirname(__FILE__));
        if (is_readable($baseDir . '/.env')) {
            (new Dotenv($baseDir))->load();
        }
    }

    public function test_that_the_correct_company_by_vat_number_is_returned_from_the_cvr_provider()
    {
        $service = new CompanyService();

        $company = $service->companyByVatNumber('33966369', 'dk');

        $this->assertEquals(Company::class, get_class($company));
        $this->assertEquals('AGERAS A/S', $company->company_name);
    }

    public function test_that_correct_company_name_is_return_by_eu_provider()
    {
        $service = new CompanyService();
        $company = $service->companyByVatNumber('NL853220888B01', 'nl');
        $this->assertEquals(Company::class, get_class($company));
        $this->assertEquals('Thinq B.v.', $company->company_name);
    }

    public function test_that_empty_result()
    {
        $service = new CompanyService();
        $service->companyByVatNumberOrFail('000000', 'nl');
    }

    /**
     * @expectedException \Ageras\Sherlock\Exceptions\EmptyResult
     */
    public function test_that_empty_result_exception_is_thrown()
    {
        $service = new CompanyService();

        $service->companyByVatNumberOrFail('000000', 'dk');
    }

    public function test_that_the_correct_list_of_companies_by_name_is_returned_from_the_cvr_provider()
    {
        $service = new CompanyService();

        $companies = $service->companiesByName('Ageras', 'dk');

        $names = array_map(function (Company $company) {
            return $company->company_name;
        }, $companies);

        $this->assertContains('AGERAS A/S', $names);
    }

    public function test_that_no_providers_are_returned_when_geo_code_is_empty()
    {
        $service = new CompanyService();

        $providers = $service->providers('');

        $this->assertEmpty($providers);
    }

    public function test_that_a_list_of_providers_is_returned_when_geo_code_exists()
    {
        $service = new CompanyService();

        $providers = $service->providers('dk');
        $nl_provider = $service->providers('nl');
        $this->assertNotEmpty($nl_provider);
        $this->assertNotEmpty($providers);
    }
}
