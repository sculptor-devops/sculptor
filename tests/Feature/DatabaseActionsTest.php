<?php

namespace Tests\Feature;

use Exception;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Sculptor\Foundation\Contracts\Database;
use Sculptor\Agent\Actions\Database as Actions;
use Tests\Stubs\MySql;
use Tests\TestCase;

class DatabaseActionsTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @var Actions|null
     */
    private $actions;

    /**
     * @var MySql|null
     */
    private $mysql;

    function setUp(): void
    {
        parent::setUp();

        $this->actions = resolve(Actions::class);

        $mysql = new MySql(true);

        $this->mysql = $mysql;

        app()->bind(Database::class, function () use($mysql) {
            return $mysql;
        });
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testDatabaseCreate(): void
    {
        $this->mysql->response(true);

        $action = $this->actions->create('test_database');

        $this->assertTrue($action, "Action not taken: {$this->actions->error()}");

        $this->assertDatabaseHas('databases', [ 'name' => 'test_database' ]);
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testDatabaseCreateError(): void
    {
        $this->mysql->response(false);

        $action = $this->actions->create('test_database');

        $this->assertFalse($action, "Action not taken: {$this->actions->error()}");

        $this->assertDatabaseMissing('databases', [ 'name' => 'test_database' ]);
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testDatabaseDelete(): void
    {
        $action = $this->actions->delete('test_database');

        $this->assertFalse($action, 'Database found');

        $this->actions->create('test_database');

        $action = $this->actions->delete('test_database');

        $this->assertTrue($action, "Action not taken: {$this->actions->error()}");

        $this->assertDatabaseMissing('databases', [ 'name' => 'test_database' ]);
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testDatabaseDeleteError(): void
    {
        $this->mysql->response(false);

        $this->actions->create('test_database');

        $action = $this->actions->delete('test_database');

        $this->assertFalse($action, "Action not taken: {$this->actions->error()}");

        $this->assertDatabaseMissing('databases', [ 'name' => 'test_database' ]);
    }

    /**
     * A basic test example.
     *
     * @return void
     * @throws Exception
     */
    public function testDatabaseUserCreate(): void
    {
        $this->mysql->response(true);

        $this->actions->create('test_database');

        $action = $this->actions->user('username', 'password', 'test_database');

        $this->assertTrue($action, "Action not taken: {$this->actions->error()}");

        $this->assertDatabaseHas('database_users', [ 'name' => 'username' ]);
    }

    /**
     * A basic test example.
     *
     * @return void
     * @throws Exception
     */
    public function testDatabaseUserCreateError(): void
    {
        $this->mysql->response(false);

        $action = $this->actions->user('username', 'password', 'test_database');

        $this->assertFalse($action, "Action not taken: {$this->actions->error()}");

        $this->actions->create('test_database');

        $action = $this->actions->user('username', 'password', 'test_database');

        $this->assertFalse($action, "Action not taken: {$this->actions->error()}");

        $this->assertDatabaseMissing('database_users', [ 'name' => 'username' ]);
    }


    /**
     * A basic test example.
     *
     * @return void
     * @throws Exception
     */
    public function testDatabaseUserDelete(): void
    {
        $this->mysql->response(true);

        $this->actions->create('test_database');

        $this->actions->user('username', 'password', 'test_database');

        $this->assertDatabaseHas('database_users', [ 'name' => 'username' ]);

        $action = $this->actions->drop('username', 'test_database');

        $this->assertTrue($action, "Action not taken: {$this->actions->error()}");

        $this->assertDatabaseMissing('database_users', [ 'name' => 'username' ]);
    }

    /**
     * A basic test example.
     *
     * @return void
     * @throws Exception
     */
    public function testDatabaseUserDeleteError(): void
    {
        $this->mysql->response(true);

        $this->actions->create('test_database');

        $this->actions->user('username', 'password', 'test_database');

        $this->assertDatabaseHas('database_users', [ 'name' => 'username' ]);

        $this->mysql->response(false);

        $action = $this->actions->drop('username', 'test_database');

        $this->assertFalse($action, "Action not taken: {$this->actions->error()}");

        $this->assertDatabaseHas('database_users', [ 'name' => 'username' ]);
    }

    /**
     * A basic test example.
     *
     * @return void
     * @throws Exception
     */
    public function testDatabaseUserPassword(): void
    {
        $this->mysql->response(true);

        $this->actions->create('test_database');

        $this->actions->user('username', 'password', 'test_database');

        $this->assertDatabaseHas('database_users', [ 'name' => 'username' ]);

        $action = $this->actions->password('username', 'password','test_database');

        $this->assertTrue($action, "Action not taken: {$this->actions->error()}");

        $this->assertDatabaseHas('database_users', [ 'name' => 'username' ]);
    }

    /**
     * A basic test example.
     *
     * @return void
     * @throws Exception
     */
    public function testDatabaseUserPasswordError(): void
    {
        $this->mysql->response(true);

        $this->actions->create('test_database');

        $this->actions->user('username', 'password', 'test_database');

        $this->assertDatabaseHas('database_users', [ 'name' => 'username' ]);

        $this->mysql->response(false);

        $action = $this->actions->password('username', 'password','test_database');

        $this->assertFalse($action, "Action not taken: {$this->actions->error()}");

        $this->assertDatabaseHas('database_users', [ 'name' => 'username' ]);
    }
}
