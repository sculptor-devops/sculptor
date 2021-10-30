<?php

namespace Tests\Unit;

use Exception;
use Tests\TestCase;
use Sculptor\Agent\Support\Parametrizer;
use Sculptor\Agent\Exceptions\InvalidNumberOfParameters;

class ParametrizerTest extends TestCase
{
    public function test_expected()
    {
        $params = new Parametrizer('a::b', 2);

        $this->assertEquals(2, $params->expected());
    }

    public function test_tokens()
    {
        $params = new Parametrizer('a::b', 2);

        $this->assertEquals(['a', 'b'], $params->tokens());
    }

    public function test_token()
    {
        $params = new Parametrizer('a::b', 2);

        $this->assertEquals('a', $params->token(0));

        $this->assertEquals('b', $params->token(1));
    }
    
    public function test_first_last()
    {
        $params = new Parametrizer('a::b', 2);

        $this->assertEquals('a', $params->first());

        $this->assertEquals('b', $params->last());
    }

    public function test_expect_exception()
    {
        $this->expectException(Exception::class);

        $params = new Parametrizer('a::b', 3);
    }

    public function test_invalid_tokenizer()
    {
        $this->expectException(InvalidNumberOfParameters::class);

        $params = new Parametrizer('a:b', 3);
    }
}

