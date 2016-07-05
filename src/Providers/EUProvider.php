<?php

namespace Ageras\Sherlock\Providers;

use Ageras\Sherlock\Exceptions\MethodNoImplemented;
use Ageras\Sherlock\Models\Company;
use SoapClient;
use SoapFault;
use Ageras\Sherlock\Exceptions\SoapClientException;

class EUProvider implements CompanyProviderInterface
{
    /**
     * Service URL.
     * @var string
     */
    protected $serviceUrl = 'http://ec.europa.eu/taxation_customs/vies/checkVatService.wsdl';

    /**
     * Geo code.
     * @var string
     */
    private $geo_code;

    /**
     * EUProvider constructor.
     * @param $geoCode
     */
    public function __construct($geoCode)
    {
        $this->geo_code = $geoCode;
    }

    /**
     * Query builder.
     * @param $string
     * @return array
     */
    protected function query($string)
    {
        $geo_code = strtoupper($this->geo_code);
        try {
            $search = new SoapClient($this->serviceUrl);
            $result = $search->checkVat(['countryCode' => $geo_code, 'vatNumber' => $string]);

            return count($result) > 0 ? $this->formatResult($result) : null;
        } catch (SoapFault $e) {
            throw new SoapClientException($e);
        }
    }

    /**
     * Format result.
     * @param $data
     * @return array
     */
    protected function formatResult($data)
    {
        $result[] = new Company([
            'company_name'                => $data->name,
            'company_status'              => $data->valid ? Company::COMPANY_STATUS_ACTIVE : Company::COMPANY_STATUS_CEASED,
            'company_registration_number' => null,
            'company_vat_number'          => $data->vatNumber,
            'company_address'             => $this->formatAddress($data->address),
            'company_city'                => $this->formatCity($data->address),
            'company_postcode'            => $this->formatPostcode($data->address),
            'company_phone_number'        => null,
            'company_email'               => null,
        ]);

        return $result;
    }

    /**
     * Format address.
     * @param $data
     * @return null|string
     */
    protected function formatAddress($data)
    {
        $datas = explode("\n", $data);
        foreach ($datas as $address) {
            if (! empty($address)) {
                return trim($address);
            }
        }
    }

    /**
     * Format city.
     * @param $data
     * @return null|string
     */
    protected function formatCity($data)
    {
        $data = explode(' ', $data);
        $city = end($data);

        return isset($city) ? trim($city) : null;
    }

    /**
     * Format postcode.
     * @param $data
     * @return null
     */
    protected function formatPostcode($data)
    {
        $data = explode("\n", $data);
        $result = $this->cleanResponse($data);
        if(!isset($result[2])){
            return null;
        }
        $post_code = $result[2];
        $post_code = explode(' ', $post_code);

        return isset($post_code[0]) ? $post_code[0] : null;
    }

    /**
     * Get Company by vat number.
     * @param $vatNumber
     * @return array
     */
    public function companyByVatNumber($vatNumber)
    {
        $result = $this->query($this->formatVatNumber($vatNumber));

        return $result;
    }

    /**
     * Format vat number, clean it in case it does contains EU standard, DK123345556.
     * @param $string
     * @return string
     */
    protected function formatVatNumber($string)
    {
        $string = preg_replace("/[^A-Za-z0-9]/", "", $string);
        $geo_location = substr($string, 0, 2);
        if (strtoupper($this->geo_code) == $geo_location) {
            $string = substr($string, 2);
        }

        return $string;
    }

    /**
     * Remove empty values from array.
     * @param $data
     * @return array
     */
    protected function cleanResponse($data)
    {
        return array_filter($data);
    }

    public function companiesByVatNumber($vatNumber)
    {
        throw new MethodNoImplemented('Method not implemented');
    }

    public function companiesByName($name)
    {
        throw new MethodNoImplemented('Method not implemented');
    }
}
