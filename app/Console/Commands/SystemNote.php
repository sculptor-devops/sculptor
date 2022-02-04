<?php

namespace App\Console\Commands;

use Illuminate\Support\Str;
use Prettus\Validator\Exceptions\ValidatorException;
use Sculptor\Agent\Enums\LogContextType;
use Sculptor\Agent\Repositories\EventRepository;
use Sculptor\Agent\Support\CommandBase;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class SystemNote extends CommandBase
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'system:note {note} {level=notice}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add system a note on events';
    /**
     * @var EventRepository
     */
    private $events;

    /**
     * Create a new command instance.
     *
     * @param EventRepository $events
     */
    public function __construct(EventRepository $events)
    {
        parent::__construct();

        $this->events = $events;
    }

    /**
     * Execute the console command.
     *
     * @return int
     * @throws ValidatorException
     */
    public function handle(): int
    {
        $note = $this->argument('note');

        $level = $this->argument('level');

        $this->startTask("Adding note level {$level}");
        
        

        $this->events->create([
            'message' => $note,
            'tag' => LogContextType::BATCH,
            'level' => $level
        ]);

        return $this->completeTask();
    }
}
