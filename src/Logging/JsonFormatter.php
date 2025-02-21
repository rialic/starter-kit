<?php

namespace App\Logging;

use Illuminate\Support\Facades\Route;
use Monolog\Formatter\JsonFormatter as BaseJsonFormatter;
use Monolog\LogRecord;

class JsonFormatter extends BaseJsonFormatter
{
    /**
     * {@inheritdoc}
     */
    public function format(LogRecord $record): string
    {
        $requestId = request()->header('X-Request-ID');
        $datetime = $record['datetime']->format('Y-m-d H:i:s');
        $routeParameters = null;
        $requestParameters = null;

        if (request()->route()) {
            $routeParameters = request()->route()->parameters();
            $requestParameters = request()->all();
        }

        $record = [
            "[{$datetime}]" => [
                "reqId" => $requestId,
                'time' => $record['datetime']->format('Y-m-d H:i:s'),
                'routeAction' => Route::currentRouteAction(),
                'currentUrl' => url()?->current() || null,
                'routeParameters' => json_encode($routeParameters),
                'requestParameters' => json_encode($requestParameters),
                'remote-addrress' => request()->server('REMOTE_ADDR'),
                'level' => $record['level_name'],
                'message' => $record['message'],
                'context' => $record['context'],
            ]
        ];

        if (!empty($record['extra'])) {
            $record['payload']['extra'] = $record['extra'];
        }

        if (!empty($record['context'])) {
            $record['payload']['context'] = $record['context'];
        }

        return $this->toJson($this->normalize($record), true) . ($this->appendNewline ? "\n" : '');
    }
}