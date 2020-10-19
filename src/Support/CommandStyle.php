<?php

namespace Sculptor\Agent\Support;

use Illuminate\Console\Command;

class CommandBase extends Command
{
    public function startTask(string $name, string $loading = 'running...'): void
    {
        $this->output->write("$name: <comment>{$loading}</comment>");
    }

    public function completeTask(string $name): void
    {
        $this->endTask($name, true);
    }

    public function errorTask(string $name, string $error = 'failed'): void
    {
        $this->endTask($name, false, $error);
    }

    public function endTask(string $name, bool $completed, string $error = 'failed'): void
    {
        $message = "$name: <info>✔</info>";

        if (!$completed) {
            $message = "$name: <error>{$error}</error>";
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
}
