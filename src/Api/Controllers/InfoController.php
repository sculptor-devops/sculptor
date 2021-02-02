<?php

namespace Sculptor\Agent\Api\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Sculptor\Agent\Configuration;
use Sculptor\Agent\Ip;
use Sculptor\Foundation\Support\Version;

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

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

    public function index(Ip $ip): JsonResponse
    {
        return response()->json([
            'name' => 'Sculptor Devops',
            'version', composerVersion(),
            'php' => $this->configurations->get('sculptor.php.version'),
            'db' => $this->configurations->get('sculptor.database.default'),
            'os' => $this->version->name(),
            'arch' => $this->version->arch(),
            'bits' => $this->version->bits(),
            'modules' => explode(',', env('SCULPTOR_INSTALLED_MODULES')),
            'ip' => $ip->publicIp(),
            'ts' => time()
        ]);
    }

    public function logged(): JsonResponse
    {
        return response()->json(['authenticated' => (Auth::user() != null)]);
    }
}
