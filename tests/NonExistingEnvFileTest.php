<?php

namespace Ageras\Sherlock\Tests;

use Ageras\Sherlock\CompanyService;
use PHPUnit_Framework_TestCase;

class NonExistingEnvFileTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var CompanyService
     */
    private $service;

    protected function setUp()
    {
        parent::setUp();
        $this->service = new CompanyService();
    }

    public function test_if_provider_that_no_required_credentials_is_loaded()
    {
    }

    public function test_if_result_is_not_empty()
    {
    }

    public function test_if_exceptions_are_throw()
    {
    }
}
