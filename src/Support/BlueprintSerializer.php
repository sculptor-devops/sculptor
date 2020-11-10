<?php

namespace Sculptor\Agent\Support;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

trait BlueprintSerializer
{
    public function serializeFiler(array $filtered = null): array
    {
        $filters = [
            'id',
            'created_at',
            'updated_at',
            'password'
        ];

        if ($filtered != null) {
            $filters = array_merge($filtered, $filters);
        }

        return collect($this->toArray())
            ->reject(function ($value, $key) use ($filters) {
                return in_array($key, $filters);
            })->toArray();
    }

    public function toName(?object $named): ?string
    {
        if ($named == null) {
            return null;
        }

        return $named->name;
    }
}
