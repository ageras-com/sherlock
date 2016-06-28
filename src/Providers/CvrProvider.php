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
            $virksomhedMetadata = $companyData->virksomhedMetadata;
            $nyesteBeliggenhedsadresse = $virksomhedMetadata->nyesteBeliggenhedsadresse;
            $result[] = new Company([
                'company_name' => $virksomhedMetadata->nyesteNavn->navn,
                'company_status' => $this->getStatus($virksomhedMetadata->sammensatStatus),
                'company_registration_number' => $companyData->regNummer,
                'company_vat_number' => $companyData->cvrNummer,
                'company_address' => $this->formatAddress($nyesteBeliggenhedsadresse),
                'company_city' => $nyesteBeliggenhedsadresse->postdistrikt,
                'company_postcode' => $nyesteBeliggenhedsadresse->postnummer,
                'company_phone_number' => $this->getContact($companyData->virksomhedMetadata->nyesteKontaktoplysninger),
                'company_email' => $this->getContact($companyData->virksomhedMetadata->nyesteKontaktoplysninger, 1),
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
            case 'OPLØSTEFTERKONKURS':
                return Company::COMPANY_STATUS_DISSOLVED_AFTER_BANKRUPTCY;
        }
        
        throw new UnknownCompanyStatus($status);
    }

    /**
     * Get contact information
     * @param $contact
     * @param int $location
     * @return null
     */
    protected function getContact($contact, $location = 0)
    {
        return isset($contact[$location]) ? $contact[$location] : null;
    }

    /**
     * Format address to a friendly format
     * @param $address
     * @return string
     */
    protected function formatAddress($address)
    {
        return trim(sprintf("%s %s%s, %s %s",
            $address->vejnavn,
            $address->husnummerFra,
            $address->bogstavFra,
            $address->etage,
            $address->sidedoer
        ), ' ,');
    }
}
