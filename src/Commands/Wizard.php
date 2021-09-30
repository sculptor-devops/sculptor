<?php

namespace Sculptor\Agent\Commands;

use Closure;
use Exception;
use Illuminate\Support\Facades\Artisan;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/
trait Wizard {
    private int $width = 80;
    private int $index = 1;
    private int $steps = 5;

    private function step(): string
    {
        return "[{$this->index}/{$this->steps}]";
    }

    private function finalize($menu, bool $skippable = false)
    {
        $result = $menu->setForegroundColour('white')
            ->setBackgroundColour('magenta')
            ->setWidth($this->width)
            ->addLineBreak('', 1)
            ->addLineBreak('-', 1)
            ->setExitButtonText("Cancel")
            ->open();

        if ($result == null && !$skippable) {
            throw new Exception('Command cancelled');
        }

        $this->index++;

        return $result;
    }

    private function choose(string $title, array $options, Closure $property = null)
    {
        $menu = $this->menu("$title {$this->step()}");

        foreach ($options as $option => $data) {
            if ($property) {
                $menu->addOption($option, $property($data));                

                continue;
            }

            $menu->addOption($data, __($data));
        }

        return $this->finalize($menu);
    }

    private function input(string $title, string $question, string $default, bool $skip = false)
    {
        $menu = $this->menu("$title {$this->step()}")
            ->addQuestion($question, $default);

        if ($skip) {
            $menu->addOption('', 'Skip');

        }

        return $this->finalize($menu, $skip);
    }

    private function command(string $mesage, string $command, array $params): bool
    {
        $this->info("Step {$mesage}...");
            
        if (Artisan::call($command, $params)) {
            $this->error("Step {$mesage} error");

            $this->info(Artisan::output());

            return false;
        }

        return true;
    }
}
