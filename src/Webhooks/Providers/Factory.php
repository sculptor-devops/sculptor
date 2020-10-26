<?php

namespace Sculptor\Agent\Webhooks\Providers;

use Illuminate\Http\Request;
use Sculptor\Agent\Contracts\DeployProvider;

class Factory
{
    public static function deploy(Request $request): DeployProvider
    {
        // if (false) {
            // return new Github();
        // }

        return new Custom();
    }
}
