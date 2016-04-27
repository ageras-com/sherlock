<?php

namespace Ageras\CompanyData\Providers;

use Ageras\CompanyData\Models\Company;
use Ageras\CompanyData\Models\EmptyResult;
use Ageras\CompanyData\Models\SingleResultExpected;
use GuzzleHttp\Client;

class CvrProvider implements CompanyProviderInterface
{
    protected $serviceUrl = 'http://distribution.virk.dk/cvr-permanent';

    public function __construct($geoCode)
    {
    }

    public function companyByVatNumber($vatNumber)
    {
        $result = $this->query('cvrNummer:' . $vatNumber);

        if(count($result) > 1) {
            throw new SingleResultExpected();
        }

        return isset($result[0]) ? $result[0] : null;
    }

    public function companyByVatNumberOrFail($vatNumber)
    {
        $company = $this->companyByVatNumber($vatNumber);

        if(is_null($company)) {
            throw new EmptyResult();
        }

        return $company;
    }

    public function companiesByVatNumber($vatNumber)
    {
        // TODO: Implement companiesByVatNumber() method.
    }

    public function companiesByName($name)
    {
        return $this->query('Vrvirksomhed.virksomhedMetadata.nyesteNavn.navn:' . $name);
    }

    /**
     * @param $string
     * @return array
     */
    protected function query($string)
    {
        $url = $this->serviceUrl . '/_search';
        $client = new Client();

        $response = $client->get($url, [
            'query' => [
                'q' => $string,
            ],
            'auth' => [
                getenv('COMPANY_SERVICE_CVR_USERNAME'),
                getenv('COMPANY_SERVICE_CVR_PASSWORD'),
            ],
        ]);

        return $this->formatResult($response->getBody());
    }

    /**
     * @param $json
     * @return array
     */
    protected function formatResult($json)
    {
        $data = \GuzzleHttp\json_decode($json);
        $result = [];

        foreach($data->hits->hits as $hit) {
            $companyData = $hit->_source->Vrvirksomhed;
            $result[] = new Company([
                'company_name' => $companyData->virksomhedMetadata->nyesteNavn->navn,
            ]);
        }

        return $result;
    }
}
