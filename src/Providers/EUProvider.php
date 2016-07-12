<?php

namespace Ageras\Sherlock\Providers;

use Ageras\Sherlock\Exceptions\MethodNoImplemented;
use Ageras\Sherlock\Exceptions\SoapClientException;
use Ageras\Sherlock\Models\Company;
use SoapClient;
use SoapFault;

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
     * Query builder.
     * @param $string
     * @return array
     */
    protected function query($string)
    {
        $geo_code = strtoupper($this->geo_code);
        try {
            $search = new SoapClient($this->serviceUrl);
            $result = $search->checkVat([
                'countryCode' => $geo_code,
                'vatNumber'   => $string,
            ]);

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
        $address = $this->formatCompanyAddress($data->address);
        $result[] = new Company([
            'company_name'                => $data->name,
            'company_status'              => $data->valid ? Company::COMPANY_STATUS_ACTIVE : Company::COMPANY_STATUS_CEASED,
            'company_registration_number' => null,
            'company_vat_number'          => $data->vatNumber,
            'company_address'             => $address['address'],
            'company_city'                => $address['city'],
            'company_postcode'            => $address['postcode'],
            'company_phone_number'        => null,
            'company_email'               => null,
        ]);

        return $result;
    }

    /**
     * Format company address
     * @param $address
     * @return array
     */
    private function formatCompanyAddress($address)
    {
        $result = [];
        $address = explode("\n", $address);
        $address = array_filter($address);
        $cp = isset($address[2]) ? explode(' ', $address[2]) : null;
        $result['address'] = isset($address[1]) ? $this->removeLeadingZeros($address[1]) : null;
        $result['city'] = isset($cp[1]) ? $cp[1] : null;
        $result['postcode'] = isset($cp[0]) ? $cp[0] : null;

        return $result;
    }

    /**
     * Remove all leading zeros from address
     * @param $string
     * @return array|string
     */
    private function removeLeadingZeros($string)
    {
        $strings = explode(' ', $string);
        $result = [];
        foreach ($strings as $string) {
            array_push($result, ltrim($string, '0'));
        }
        $result = implode(' ', $result);

        return $result;
    }

    /**
     * Format vat number, clean it in case it does contains EU standard, DK123345556.
     * @param $string
     * @return string
     */
    private function formatVatNumber($string)
    {
        $string = preg_replace('/[^A-Za-z0-9]/', '', $string);
        $geo_location = substr($string, 0, 2);
        if (strtoupper($this->geo_code) == $geo_location) {
            $string = substr($string, 2);
        }

        return $string;
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
