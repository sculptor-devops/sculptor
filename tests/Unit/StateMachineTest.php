<?php

namespace Tests\Unit;

// use PHPUnit\Framework\TestCase;
use Exception;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Sculptor\Agent\Actions\Domains\StatusMachine as Domain;
use Sculptor\Agent\Enums\DomainStatusType;
use Sculptor\Agent\Exceptions\StatusMachineException;
use Sculptor\Agent\Support\StateMachine;
use Tests\TestCase;

class StateMachineTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @var StateMachine|null
     */
    private $machine;
    /**
     * @var Domain|null
     */
    private $domain;

    function setUp(): void
    {
        parent::setUp();

        $this->machine = new StateMachine([
            'state1' => [
                'state2',
                'state3'
            ],
            'state2' => [
                'state3'
            ],
            'state3' => []
        ]);

        $this->domain = resolve(Domain::class);
    }

    /**
     * A basic unit test example.
     *
     * @return void
     * @throws Exception
     */
    public function testGenericOK(): void
    {
        $this->assertTrue($this->machine->can('state1', 'state2'));

        $this->assertTrue($this->machine->can('state1', 'state3'));

        $this->assertTrue($this->machine->can('state2', 'state3'));

        $this->assertTrue($this->machine->can('state3', 'state3'));

        $this->assertTrue($this->machine->can('state3', 'state2'));

        $this->assertTrue($this->machine->can('state3', 'state1'));

        $this->assertEquals('state2, state3', $this->machine->next('state1'));

        $this->assertEquals('state3', $this->machine->next('state2'));

        $this->assertEquals('Can go in any status', $this->machine->next('state3'));
    }
    /**
     * A basic unit test example.
     *
     * @return void
     * @throws Exception
     */
    public function testGenericError(): void
    {
        $this->expectException(StatusMachineException::class);

        $this->assertTrue($this->machine->can('state1', 'state1'));

        $this->assertTrue($this->machine->can('state2', 'state1'));
    }

    /**
     * A basic unit test example.
     *
     * @return void
     * @throws Exception
     */
    public function testDomainOK(): void
    {
        $this->assertTrue($this->domain->can(DomainStatusType::NEW, DomainStatusType::CONFIGURED));

        $this->assertTrue($this->domain->can(DomainStatusType::NEW, DomainStatusType::NEW));
    }
}
