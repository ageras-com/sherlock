<?php

namespace Ageras\Sherlock;

use Ageras\Sherlock\Exceptions\EmptyResult;
use Ageras\Sherlock\Providers\VirkAnnualReportProvider;

class AnnualReportService
{
    protected $providers = [
        'dk' => VirkAnnualReportProvider::class,
    ];

    public function annualReportsByVatNumber($vat_number, $geoCode)
    {
        $result = null;
        foreach ($this->providers($geoCode) as $provider) {
            /** @var VirkAnnualReportProvider $provider */
            $provider = new $provider($geoCode);
            $result = $provider->annualReportsByVatNumber($vat_number);
        }

        return $result;
    }

    public function latestAnnualReport($vat_number, $geoCode)
    {
        $result = null;
        foreach ($this->providers($geoCode) as $provider) {
            /** @var VirkAnnualReportProvider $provider */
            $provider = new $provider($geoCode);
            $result = $provider->latestAnnualReportByVatNumber($vat_number);
        }

        return $result;
    }

    public function providers($geoCode)
    {
        if (! isset($this->providers[$geoCode])) {
            return [];
        }

        return (array) $this->providers[$geoCode];
    }

    public function latestAnnualReportByVatNumberOrFail($vatNumber, $geoCode)
    {
        $result = $this->latestAnnualReport($vatNumber, $geoCode);

        if (is_null($result)) {
            throw new EmptyResult();
        }

        return $result;
    }
}
