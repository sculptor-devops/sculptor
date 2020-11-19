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

        $this->validator = Validator::make('Domain');
    }

    /**
     * A basic unit test example.
     *
     * @return void
     * @throws Exception
     */
    public function testOK(): void
    {
        foreach ([
                     'alias' => [
                         'example.com',
                         'example.com example.org example.net',
                         'example.com subdomain.example.org www.example.net',
                     ],
                     'certificate' => CertificatesTypes::toArray()
                 ] as $name => $tests) {

            foreach ($tests as $test) {
                $this->assertTrue(!$this->validator->validate($name, $test), "Validation on {$name} must be true when {$test}");
            }
        }
    }
}
