<?php

namespace Sculptor\Agent\Actions;

use Exception;
use Sculptor\Agent\Jobs\DatabaseCreate;
use Sculptor\Agent\Jobs\DatabaseDelete;

class Database extends Actions
{
    /**
     * @param string $name
     * @return bool
     * @throws Exception
     */
    public function createDatabase(string $name): bool
    {
        return $this->run(new DatabaseCreate($name));
    }

    /**
     * @param string $name
     * @return bool
     * @throws Exception
     */
    public function deleteDatabase(string $name): bool
    {
        return $this->run(new DatabaseDelete($name));
    }
}
