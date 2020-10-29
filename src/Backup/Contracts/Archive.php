<?php namespace Sculptor\Agent\Backup\Contracts;

use Sculptor\Agent\Backup\Archives\Local;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

interface Archive
{
    /**
     * @param string $path
     * @return Archive
     */
    public function create(string $path): Archive;

    /**
     * @param string $file
     * @param $content
     * @return mixed
     */
    public function put(string $file, $content);

    /**
     * @param string $file
     * @return mixed
     */
    public function get(string $file);

    /**
     * @param string $file
     * @return mixed
     */
    public function delete(string $file);

    /**
     * @param string $file
     * @return mixed
     */
    public function list(string $file);
}