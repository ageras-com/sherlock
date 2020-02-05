<?php

namespace Ageras\Sherlock\Tests;

use Ageras\Sherlock\AnnualReportService;
use Ageras\Sherlock\Models\AnnualReport;

class AnnualReportServiceTest extends TestCase
{
    /**
     * @var AnnualReportService
     */
    private $service;

    protected function setUp()
    {
        parent::setUp();

        $this->service = new AnnualReportService();
    }

    public function test_if_single_annual_report_is_returned()
    {
        $company = $this->service->latestAnnualReport('33966369', 'dk');
        $this->assertEquals(AnnualReport::class, get_class($company));
    }

    /**
     * @expectedException \Ageras\Sherlock\Exceptions\EmptyResult
     */
    public function test_that_empty_result_exception_is_thrown()
    {
        $this->service->latestAnnualReportByVatNumberOrFail('111000', 'dk');
    }

    public function test_that_no_providers_are_returned_when_geo_code_is_empty()
    {
        $providers = $this->service->providers('');
        $this->assertEmpty($providers);
    }

    public function test_document_properties()
    {
        $ar = $this->service->latestAnnualReport('33966369', 'dk');
        $this->assertEquals(AnnualReport::SUPPORTED_FORMAT, $ar->document_mime_type);
    }

    public function test_if_provider_exist()
    {
        $providers = $this->service->providers('dk');
        $this->assertNotEmpty($providers);
    }

    public function test_annual_report_by_vat_number()
    {
        $company_reports = $this->service->annualReportsByVatNumber('33966369', 'dk');
        $first_report = $company_reports[0];
        $this->assertEquals(AnnualReport::class, get_class($first_report));
        $this->assertEquals(! empty($company_reports), true);
        $this->assertEquals(is_array($company_reports), true);
    }

    public function test_annual_report_or_fail_return_right_value()
    {
        $company_reports = $this->service->latestAnnualReportByVatNumberOrFail('33966369', 'dk');
        $this->assertEquals(AnnualReport::SUPPORTED_FORMAT, $company_reports->document_mime_type);
    }
}
