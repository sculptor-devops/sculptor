<?php

namespace Tests\Unit;

use Tests\TestCase;
use Sculptor\Agent\Backup\Tag;

class TagTest extends TestCase
{
    private $tag;

    private $ts = '00000000-000000';

    function setUp(): void
    {
        parent::setUp();

        $this->tag = $this->app->get(Tag::class)->extensions('type', 'txt', 'zip')->tag($this->ts);
    }
    
    public function test_temp()
    {
        $this->assertEquals("/tmp/type-name-{$this->ts}.txt", $this->tag->temp('name'));
    }

    public function test_compressed()
    {
        $this->assertEquals("/tmp/type-name-{$this->ts}.zip", $this->tag->compressed('name'));
    }

    public function test_destination()
    {
        $this->assertEquals("type-name-{$this->ts}.zip", $this->tag->destination('name', 'destination'));
    }

    public function test_match()
    {
        $this->assertTrue($this->tag->match('name', "type-name-{$this->ts}.zip"));
    }
}
