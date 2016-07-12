<?php
namespace Ageras\Sherlock\Providers;

interface IAnnualReportProvider
{
    public function __construct($geoCode);

    public function annualReportsByVatNumber($vatNumber);

    public function latestAnnualReportByVatNumber($vatNumber);
}