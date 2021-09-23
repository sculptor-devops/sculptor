<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Support\Str;
use Mockery\CountValidator\Exact;
use Sculptor\Agent\Actions\Domains;
use Sculptor\Agent\Support\CommandBase;
use Sculptor\Agent\Support\PhpVersions;
use Sculptor\Agent\Support\Templates;
/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class DomainWizard extends CommandBase
{
    private const WIDTH = 80;

    private int $index = 1;
    private int $steps = 5;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'domain:wizard {domain?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a guided domain';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @param PhpVersions $versions
     * @return int
     */
    public function handle(PhpVersions $versions, Templates $templates): int
    {
        $domain = $this->argument('domain');

        try {
            $type = $this->types("Domain type {$this->step()}", $templates->domains());

            $php = $this->choose("PHP version {$this->step()}", $versions->available());

            $name = $this->input("Domain name {$this->step()}", 'Insert name...', $domain ?? 'example.org');

            $repository = $this->input("Repository {$this->step()}", 'Insert url...', 'https://<<token>>@ghtub.com/username/repository.git');

            $database = $this->database();

            $this->table(['Name', 'Value'], [
                ['name' => 'Template', 'value' => $type],
                ['name' => 'PHP', 'value' => $php],
                ['name' => 'Domain', 'value' => $name],
                ['name' => 'Repository', 'value' => $repository],
                ['name' => 'Database', 'value' => $database],
                ['name' => 'Database user', 'value' => "{$database}_user"],
            ]);

            if (!$this->askYesNo('Continue? (yes/no)')) {
                throw new Exception('Command cancelled');
            }

            // $this->runCommand()
        } catch (Exception $e) {
            $this->error($e->getMessage());

            return 1;
        }

        $this->warn("Now you need to run domain:setup {$name} to make modifications");

        return 0;
    }

    private function step(): string
    {
        return "[{$this->index}/{$this->steps}]";
    }

    private function database()
    {
        $menu = $this->menu('Database [5/5]')
            ->addQuestion('Insert name', 'database_name')
            ->addOption('no_database', 'None');

        return $this->finalize($menu);
    }

    private function types(string $title, array $options)
    {
        $menu = $this->menu($title);

        foreach ($options as $folder => $data) {
            $menu->addOption($folder, __($data->name()));
        }

        return $this->finalize($menu);
    }

    private function finalize($menu)
    {
        $result = $menu->setForegroundColour('white')
            ->setBackgroundColour('magenta')
            ->setWidth(80)
            ->addLineBreak('', 1)
            ->addLineBreak('-', 1)
            ->setExitButtonText("Cancel")
            ->open();

        if ($result == null) {
            throw new Exception('Command cancelled');
        }

        $this->index++;

        return $result;
    }

    private function choose(string $title, array $options)
    {
        $menu = $this->menu($title);

        foreach ($options as $option) {
            $menu->addOption($option, __($option));
        }

        return $this->finalize($menu);
    }

    private function input(string $title, string $question, string $default)
    {
        $menu = $this->menu($title)
            ->addQuestion($question, $default);

        return $this->finalize($menu);
    }
}
