<?php

namespace Sculptor\Agent\Support;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

class CommandHide
{
    public const HIDDEN = [
        'vendor:publish',
        'inspire',
        'serve',
        'tinker',
        'db:seed',
        'db:wipe',
        'make:bindings',
        'make:cast',
        'make:channel',
        'make:command',
        'make:component',
        'make:controller',
        'make:criteria',
        'make:entity',
        'make:event',
        'make:exception',
        'make:factory',
        'make:job',
        'make:listener',
        'make:mail',
        'make:middleware',
        'make:migration',
        'make:model',
        'make:notification',
        'make:observer',
        'make:policy',
        'make:presenter',
        'make:provider',
        'make:repository',
        'make:request',
        'make:resource',
        'make:rest-controller',
        'make:rule',
        'make:seeder',
        'make:test',
        'make:transformer',
        'make:validator',
        'notifications:table',
        'schema:dump',
        'session:table',
        'storage:link',
        'stub:publish',
        'cache:table',
        'queue:batches-table',
        'queue:failed-table',
        'queue:table'
    ];

    public static function hide(): void
    {
        if (!App::environment('production')) {
            return;
        }

        foreach (Artisan::all() as $key => $command) {
            if (in_array($key, CommandHide::HIDDEN)) {
                $command->setHidden(true);
            }
        }
    }
}