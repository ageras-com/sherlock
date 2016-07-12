<?php

namespace Ageras\Sherlock\Models;

class AnnualReport
{
    const SUPPORTED_FORMAT = 'application/pdf';

    const DOCUMENT_TYPE = 'AARSRAPPORT';

    protected $attributes = [
        'period_start',
        'period_end',
        'created_at',
        'publish_at',
        'updated_at',
        'document_url',
        'document_mine_type',
    ];

    protected $data;

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    public function getAttribute($key)
    {
        if (in_array($key, $this->attributes)) {
            return $this->data[$key];
        }
    }

    public function __get($key)
    {
        return $this->getAttribute($key);
    }
}
