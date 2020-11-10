<?php

namespace Sculptor\Agent\Support;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;

class CommandBase extends Command
{
    private $taskName;

    public function startTask(string $name, string $loading = 'running...'): void
    {
        $this->taskName = $name;

        $this->output->write("$name: <comment>{$loading}</comment>");
    }

    public function completeTask(): int
    {
        $this->endTask(true);

        return 0;
    }

    public function errorTask(string $error = 'failed'): int
    {
        $this->endTask(false, $error);

        return 1;
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

    public function yesNo(?bool $check): string
    {
        return $check ? '<info>YES</info>' : '<error>NO</error>';
    }

    public function toKeyValue(array $values): array
    {
        $result = [];

        foreach ($values as $key => $value) {
            $result[] = [ 'key' => $key, 'value' => $value];
        }

        return $result;
    }

    public function toKeyValueHeaders(Collection $values): array
    {
        return collect($values->first())->keys()->toArray();
    }

    public function toName(?Relation $collection): string
    {
        if ($collection == null) {
            return 'none';
        }

        $name = $collection->get(['name'])
            ->map(function ($user) {
                return $user->name;
            })
            ->join(', ');

        return $name == '' ? 'none' : $name;
    }
}
