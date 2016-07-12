<?php

namespace Ageras\Sherlock;

use Ageras\Sherlock\Providers\IAnnualReportProvider;
use Ageras\Sherlock\Providers\AnnualReportProvider;

class AnnualReportService
{
    protected $providers = [
        'dk' => AnnualReportProvider::class,
    ];

    public function annualReportsByVatNumber($vat_number, $geoCode)
    {
        $result = null;
        foreach ($this->providers($geoCode) as $provider) {
            /** @var IAnnualReportProvider $provider */
            $provider = new $provider($geoCode);
            $result = $provider->annualReportsByVatNumber($vat_number);
        }

        return $result;
    }

    public function latestAnnalReport($vat_number, $geoCode)
    {
        $result = null;
        foreach ($this->providers($geoCode) as $provider) {
            /** @var IAnnualReportProvider $provider */
            $provider = new $provider($geoCode);
            $result = $provider->latestAnnualReportByVatNumber($vat_number);
        }

        return $result;
    }

    private function providers($geoCode)
    {
        if (! isset($this->providers[$geoCode])) {
            return [];
        }

        return (array) $this->providers[$geoCode];
    }
}