<?php

namespace Ageras\Sherlock\Providers;

use GuzzleHttp\Client;
use Ageras\Sherlock\Models\AnnualReport;

class VirkAnnualReportProvider implements AnnualReportProviderInterface
{
    protected $_service_url = 'http://distribution.virk.dk/offentliggoerelser';

    public function __construct($geoCode)
    {
    }

    public function annualReportsByVatNumber($vatNumber)
    {
        $vatNumber = urlencode($vatNumber);

        return $this->query($vatNumber);
    }

    public function latestAnnualReportByVatNumber($vatNumber)
    {
        $arp = $this->annualReportsByVatNumber($vatNumber);

        return $arp ? end($arp) : null;
    }

    protected function query($vatNumber)
    {
        $url = $this->_service_url . '/_search';
        $client = new Client();

        $response = $client->get($url, [
            'json' => [
                'query' => [
                    'match' => [
                        'cvrNummer' => $vatNumber,
                    ],
                ],
                'sort' =>  ['offentliggoerelsesTidspunkt' => ['order' => 'asc']],
            ],
            'auth' => [
                getenv('COMPANY_SERVICE_CVR_USERNAME'),
                getenv('COMPANY_SERVICE_CVR_PASSWORD'),
            ],
        ]);

        return $this->formatResult($response->getBody());
    }

    protected function formatResult($json)
    {
        $data = \GuzzleHttp\json_decode($json);
        $result = [];

        foreach ($data->hits->hits as $hit) {
            $annual_report = $hit->_source;
            $document = $this->getAnnualReportData($annual_report->dokumenter);
            $result[] = new AnnualReport([
                'period_start' => $annual_report->regnskab->regnskabsperiode->startDato,
                'period_end' => $annual_report->regnskab->regnskabsperiode->slutDato,
                'created_at' => $annual_report->offentliggoerelsesTidspunkt,
                'publish_at' => $annual_report->indlaesningsTidspunkt,
                'updated_at' => $annual_report->sidstOpdateret,
                'document_url' => isset($document['url']) ? $document['url'] : null,
                'document_mime_type' => isset($document['mime_type']) ? $document['mime_type'] : null,
            ]);
        }

        return $result;
    }

    /**
     * Get pdf documents.
     * @param $documents
     * @return array
     */
    private function getAnnualReportData($documents)
    {
        $result = [];
        foreach ($documents as $document) {
            if ($document->dokumentMimeType == AnnualReport::SUPPORTED_FORMAT &&
                $document->dokumentType == AnnualReport::DOCUMENT_TYPE) {
                $result = [
                    'url' => $document->dokumentUrl,
                    'mime_type' => $document->dokumentMimeType,
                ];
            }
        }

        return $result;
    }
}
