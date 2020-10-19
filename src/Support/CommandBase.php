<?php

namespace Sculptor\Agent\Support;

use Illuminate\Console\Command;

class CommandBase extends Command
{
    private $taskName;

    public function startTask(string $name, string $loading = 'running...'): void
    {
        $this->taskName = $name;

        $this->output->write("$name: <comment>{$loading}</comment>");
    }

    public function completeTask(): void
    {
        $this->endTask( true);
    }

    public function errorTask(string $error = 'failed'): void
    {
        $this->endTask(false, $error);
    }

    public function endTask(bool $completed, string $error = 'failed'): void
    {
        $message = "{$this->taskName}: <info>✔</info>";

        if (!$completed) {
            $message = "{$this->taskName}: <error>{$error}</error>";
        }

        if ($this->output->isDecorated()) {
            $this->output->write("\x0D");
            $this->output->write("\x1B[2K");
            $this->output->writeln($message);

            return;
        }

        $this->output->writeln('');
        $this->output->writeln($message);
    }

    public function YesNo(bool $check): string
    {
        return $check ? '<info>YES</info>' : '<error>NO</error>';
    }
}
