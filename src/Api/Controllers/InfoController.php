<?php

namespace Sculptor\Agent\Api\Controllers;

use App\Http\Controllers\Controller;
use Sculptor\Agent\Configuration;
use Sculptor\Foundation\Support\Version;

class InfoController extends Controller
{
    /**
     * @var Configuration
     */
    private $configurations;
    /**
     * @var Version
     */
    private $version;

    public function __construct(Configuration $configurations, Version $version)
    {
        $this->configurations = $configurations;

        $this->version = $version;
    }

    public function index()
    {
        return response()->json([
            'name' => 'Sculptor Devops',
            'php' => $this->configurations->get('sculptor.php.version'),
            'db' => $this->configurations->get('sculptor.database.default'),
            'command' => COMMAND_INTERFACE,
            'api' => API_VERSION,
            'blueprint' => BLUEPRINT_VERSION,
            'os' => $this->version->name(),
            'arch' => $this->version->arch(),
            'bits' => $this->version->bits(),
            'modules' => explode(',', env('SCULPTOR_INSTALLED_MODULES'))
        ]);
    }
}
