<?php

namespace Sculptor\Agent\Logs;


class Parser
{
    private $pattern = array(
        'default' =>  '/^\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}([\+-]\d{4})?)\](?:.*?(\w+)\.|.*?)',
        'error'   => '/\[(?P<date>.*)\] (?P<logger>[\w-]+).(?P<level>\w+): (?P<message>(.*)+) (?P<context>[^ ]+) (?P<extra>[^ ]+)/'
    );

    public function parse($log)
    {
        //$filename = function_exists('config') ? config('logviewer.storage_path', storage_path('logs')) : storage_path('logs');

        $filename = "/home/seneca/sculptor/storage/logs/laravel.log";

        $log = file_get_contents($filename);

        preg_match($this->pattern['default'], $log, $data);

        dd($data);

        if (!isset($data['date'])) {
            return array();
        }

        $date = \DateTime::createFromFormat('Y-m-d H:i:s', $data['date']);

        $array = array(
            'date'    => $date,
            'logger'  => $data['logger'],
            'level'   => $data['level'],
            'message' => $data['message'],
            'context' => json_decode($data['context'], true),
            'extra'   => json_decode($data['extra'], true)
        );


        return $array;
    }

}
