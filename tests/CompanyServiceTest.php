<?php

namespace Ageras\Sherlock\Tests;

use Ageras\Sherlock\CompanyService;
use Ageras\Sherlock\Models\Company;
use Ageras\Sherlock\Providers\VirkProvider;
use Dotenv\Dotenv;

class CompanyServiceTest extends TestCase
{
    /**
     * @var CompanyService
     */
    private $service;

    protected function setUp()
    {
        parent::setUp();
        $baseDir = dirname(dirname(__FILE__));
        if (is_readable($baseDir . '/.env')) {
            (new Dotenv($baseDir))->load();
        }

        $this->service = new CompanyService();
    }

    public function test_that_the_correct_company_by_vat_number_is_returned_from_the_cvr_provider()
    {
        $company = $this->service->companyByVatNumber('33966369', 'dk');
        $empty_company = $this->service->companyByVatNumber('22111', 'dk');
        $this->assertEquals(Company::class, get_class($company));
        $this->assertEquals('AGERAS A/S', $company->company_name);
        $this->assertEquals(count($company), 1);
        $this->assertEquals(is_null($empty_company), true);
    }

    public function test_that_correct_company_name_is_return_by_eu_provider()
    {
        $company = $this->service->companyByVatNumber('NL853220888B01', 'nl');
        $this->assertEquals(Company::class, get_class($company));
        $this->assertEquals('Thinq B.v.', $company->company_name);
        $this->assertEquals(count($company), 1);
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

    public function test_company_status_can_be_found()
    {
        $provider = new VirkProvider('dk');
        $active = $this->invokeMethod($provider, 'getStatus', 'Aktiv');
        $ceased = $this->invokeMethod($provider, 'getStatus', 'Ophørt');
        $normal = $this->invokeMethod($provider, 'getStatus', 'NORMAL');
        $liquidation = $this->invokeMethod($provider, 'getStatus', 'OPLØSTEFTERFRIVILLIGLIKVIDATION');
        $bankrupt = $this->invokeMethod($provider, 'getStatus', 'UNDERKONKURS');
        $dissolved = $this->invokeMethod($provider, 'getStatus', 'TVANGSOPLØST');
        $dft = $this->invokeMethod($provider, 'getStatus', 'OPLØSTEFTERERKLÆRING');
        $dftt = $this->invokeMethod($provider, 'getStatus', 'UNDERFRIVILLIGLIKVIDATION');
        $vl = $this->invokeMethod($provider, 'getStatus', 'OPLØSTEFTERKONKURS');
        $uvl = $this->invokeMethod($provider, 'getStatus', 'OPLØSTEFTERFUSION');

        $this->assertEquals($active, Company::COMPANY_STATUS_ACTIVE);
        $this->assertEquals($ceased, Company::COMPANY_STATUS_CEASED);
        $this->assertEquals($normal, Company::COMPANY_STATUS_NORMAL);
        $this->assertEquals($liquidation, Company::COMPANY_STATUS_DISSOLVED_UNDER_VOLUNTARY_LIQUIDATION);
        $this->assertEquals($bankrupt, Company::COMPANY_STATUS_IN_BANKRUPTCY);
        $this->assertEquals($dissolved, Company::COMPANY_STATUS_FORCED_DISSOLVED);
        $this->assertEquals($dft, Company::COMPANY_STATUS_DISSOLVED_FOLLOWING_STATEMENT);
        $this->assertEquals($dftt, Company::COMPANY_STATUS_UNDER_VOLUNTARY_LIQUIDATION);
        $this->assertEquals($vl, Company::COMPANY_STATUS_DISSOLVED_AFTER_BANKRUPTCY);
        $this->assertEquals($uvl, Company::COMPANY_STATUS_DISSOLVED_AFTER_MERGER);
    }

    public function invokeMethod(&$object, $methodName, $parameter)
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, (array) $parameter);
    }
}
