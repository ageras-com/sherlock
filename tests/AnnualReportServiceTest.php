<?php

namespace Ageras\Sherlock\Tests;

use Ageras\Sherlock\AnnualReportService;
use Ageras\Sherlock\Models\AnnualReport;
use Dotenv\Dotenv;

class AnnualReportServiceTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();
        $baseDir = dirname(dirname(__FILE__));
        if (is_readable($baseDir . '/.env')) {
            (new Dotenv($baseDir))->load();
        }
    }

    public function test_if_single_annual_report_is_returned()
    {
        $service = new AnnualReportService();

        $company = $service->latestAnnualReport('33966369', 'dk');
        $this->assertEquals(AnnualReport::class, get_class($company));
    }

    /**
     * @expectedException \Ageras\Sherlock\Exceptions\EmptyResult
     */
    public function test_that_empty_result_exception_is_thrown()
    {
        $service = new AnnualReportService();
        $service->latestAnnualReportByVatNumberOrFail('111000', 'dk');
    }

    public function test_that_no_providers_are_returned_when_geo_code_is_empty()
    {
        $service = new AnnualReportService();
        $providers = $service->providers('');

        $this->assertEmpty($providers);
    }

    public function test_document_properties()
    {
        $service = new AnnualReportService();
        $ar = $service->latestAnnualReport('33966369', 'dk');
        $this->assertEquals(AnnualReport::SUPPORTED_FORMAT, $ar->document_mime_type);
    }

    public function test_if_provider_exist()
    {
        $service = new AnnualReportService();

        $providers = $service->providers('dk');
        $this->assertNotEmpty($providers);
    }
}
