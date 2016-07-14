<?php

namespace Ageras\Sherlock\Providers;

interface AnnualReportProviderInterface
{
    public function __construct($geoCode);

    public function annualReportsByVatNumber($vatNumber);

    public function latestAnnualReportByVatNumber($vatNumber);
}
