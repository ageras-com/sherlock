<?php

namespace Ageras\Sherlock\Providers;

use Ageras\Sherlock\Exceptions\UnknownCompanyStatus;
use Ageras\Sherlock\Models\Company;
use Ageras\Sherlock\Models\SingleResultExpected;
use GuzzleHttp\Client;

class CvrProvider implements CompanyProviderInterface
{
    protected $serviceUrl = 'http://distribution.virk.dk/cvr-permanent';

    public function __construct($geoCode)
    {
    }

    public function companyByVatNumber($vatNumber)
    {
        $result = $this->companiesByVatNumber($vatNumber);

        if(count($result) > 1) {
            throw new SingleResultExpected();
        }

        return isset($result[0]) ? $result[0] : null;
    }

    public function companiesByVatNumber($vatNumber)
    {
        $vatNumber = urlencode($vatNumber);
        return $this->query('cvrNummer:' . $vatNumber);
    }

    public function companiesByName($name)
    {
        $name = urlencode($name);
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
                'company_status' => $this->getStatus($companyData->virksomhedMetadata->sammensatStatus),
            ]);
        }
        return $result;
    }

    protected function getStatus($status)
    {
        switch ($status) {
            case 'Aktiv':
                return Company::COMPANY_STATUS_ACTIVE;
            case 'Ophørt':
                return Company::COMPANY_STATUS_CEASED;
            case 'NORMAL':
                return Company::COMPANY_STATUS_NORMAL;
            case 'OPLØSTEFTERFRIVILLIGLIKVIDATION':
                return Company::COMPANY_STATUS_DISSOLVED_UNDER_VOLUNTARY_LIQUIDATION;
            case 'UNDERKONKURS':
                return Company::COMPANY_STATUS_IN_BANKRUPTCY;
            case 'TVANGSOPLØST':
                return Company::COMPANY_STATUS_FORCED_DISSOLVED;
            case 'OPLØSTEFTERERKLÆRING':
                return Company::COMPANY_STATUS_DISSOLVED_FOLLOWING_STATEMENT;
            case 'UNDERFRIVILLIGLIKVIDATION':
                return Company::COMPANY_STATUS_UNDER_VOLUNTARY_LIQUIDATION;
        }
        
        throw new UnknownCompanyStatus($status);
    }
}
