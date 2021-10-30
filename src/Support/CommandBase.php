<?php

namespace Sculptor\Agent\Support;

use App\Console\Commands\BackupShow;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class CommandBase extends Command
{
    private $taskName;

    public const PAD = 30;

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

    public function noYes(?bool $check): string
    {
        return $check ? '<error>YES</error>' : '<info>NO</info>';
    }

    public function toKeyValue(array $values): array
    {
        $result = [];

        foreach ($values as $key => $value) {
            $result[] = ['key' => $key, 'value' => $value];
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

    public function padded(string $key, string $value, int $pad = CommandBase::PAD): void
    {
        $this->info(Str::padRight($key, $pad) . ": <fg=white>{$value}</>");
    }

    public function askYesNo(string $question = 'Continue? (yes/no)'): bool
    {
        $result = $this->ask($question);

        return Str::lower($result) == 'y' || Str::lower($result) == 'yes';
    }

    public function hasArguments(): bool
    {
        foreach (collect($this->arguments())->forget('command') as $argument) {
            if ($argument != null) {
                return true;
            }
        }

        return false;
    }
}
