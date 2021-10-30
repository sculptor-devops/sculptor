<?php

namespace Tests\Feature;

use Tests\TestCase;
use InvalidArgumentException;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Sculptor\Agent\Backup\Factory;
use Sculptor\Agent\Backup\Subjects\Database;
use Sculptor\Agent\Backup\Subjects\Domain;
use Sculptor\Agent\Backup\Subjects\Blueprint;
use Sculptor\Agent\Enums\BackupType;
use Sculptor\Agent\Repositories\Entities\Backup;

class BackupFactoryTest extends TestCase
{
    use DatabaseMigrations;

    private $factory;
    
    function setUp(): void
    {
        parent::setUp();

        $this->factory = $this->app->make(Factory::class);
    }

    public function test_resolve_database(): void
    {
        $backup = new Backup ([ 'type' => BackupType::DATABASE ]);

        $this->assertInstanceOf(Database::class, $this->factory->make($backup));
    }

    public function test_resolve_domain(): void
    {
        $backup = new Backup ([ 'type' => BackupType::DOMAIN ]);

        $this->assertInstanceOf(Domain::class, $this->factory->make($backup));
    }

    public function test_resolve_blueprint(): void
    {
        $backup = new Backup ([ 'type' => BackupType::BLUEPRINT ]);

        $this->assertInstanceOf(Blueprint::class, $this->factory->make($backup));
    }
    
    public function test_cannot_resolve_type(): void
    {
        $backup = new Backup ([ 'type' => 'not_existent' ]);

        $this->expectException(InvalidArgumentException::class);

        $this->factory->make($backup);
    }
}