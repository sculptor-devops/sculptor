<?php

namespace Tests\Unit;

// use PHPUnit\Framework\TestCase;
use Exception;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Sculptor\Agent\Enums\CertificatesTypes;
use Sculptor\Agent\Validation\Validator;
use Tests\TestCase;

class ValidatorTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @var Validator|null
     */
    private $validator;

    function setUp(): void
    {
        parent::setUp();
    }

    /**
     * A basic unit test example.
     *
     * @return void
     * @throws Exception
     */
    public function testFqdnValidator(): void
    {
        $validator = Validator::make('Domain');

        foreach ([
            'example.com',
            'example.com example.org example.net',
            'example.com subdomain.example.org www.example.net',
        ] as $test) {
                $this->assertTrue(!$validator->validate('data', $test), "Validation on must be true when value is [{$test}]");
        }
    }
}
