<?php

namespace Ageras\Sherlock\Tests;

use Ageras\Sherlock\CompanyService;
use Ageras\Sherlock\Models\Company;

class CompanyServiceTest extends TestCase
{
    /**
     * @var CompanyService
     */
    private $service;

    protected function setUp()
    {
        parent::setUp();

        $this->service = new CompanyService();
    }

    public function test_that_the_correct_company_by_vat_number_is_returned_from_the_cvr_provider()
    {
        $company = $this->service->companyByVatNumber('33966369', 'dk');
        $this->assertEquals(Company::class, get_class($company));
        $this->assertEquals('AGERAS A/S', $company->company_name);
    }

    public function test_that_correct_company_name_is_return_by_eu_provider()
    {
        $company = $this->service->companyByVatNumber('NL853220888B01', 'nl');
        $this->assertEquals(Company::class, get_class($company));
        $this->assertEquals('Thinq B.v.', $company->company_name);
    }

    public function test_that_empty_result()
    {
        $this->service->companyByVatNumberOrFail('000000', 'nl');
    }

    /**
     * @expectedException \Ageras\Sherlock\Exceptions\EmptyResult
     */
    public function test_that_empty_result_exception_is_thrown()
    {
        $this->service->companyByVatNumberOrFail('000000', 'dk');
    }

    public function test_that_the_correct_list_of_companies_by_name_is_returned_from_the_cvr_provider()
    {
        $companies = $this->service->companiesByName('Ageras', 'dk');
        $names = array_map(function (Company $company) {
            return $company->company_name;
        }, $companies);
        $this->assertContains('AGERAS A/S', $names);
    }

    public function test_that_no_providers_are_returned_when_geo_code_is_empty()
    {
        $providers = $this->service->providers('');
        $this->assertEmpty($providers);
    }

    public function test_that_a_list_of_providers_is_returned_when_geo_code_exists()
    {
        $providers = $this->service->providers('dk');
        $nl_provider = $this->service->providers('nl');
        $this->assertNotEmpty($nl_provider);
        $this->assertNotEmpty($providers);
    }

    public function test_if_list_companies_is_an_array()
    {
        $cbn = $this->service->companiesByName('Rodriguez', 'dk');
        $cbv = $this->service->companiesByVatNumber('33966369', 'dk');
        $cbnl = $this->service->companiesByVatNumber('NL853220888B01', 'nl');
        $this->assertEquals(is_array($cbn), true);
        $this->assertEquals(is_array($cbv), true);
        $this->assertEquals(is_array($cbnl), true);
    }

    /**
     * @expectedException \Ageras\Sherlock\Exceptions\MethodNoImplemented
     */
    public function test_if_non_implemented_method_throws_exception()
    {
        $this->service->companiesByName('Ageras', 'nl');
    }
}
