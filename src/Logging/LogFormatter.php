<?php

namespace App\Logging;

use App\Logging\JsonFormatter;

class LogFormatter
{
  /**
   * [README]
   * Configure .env LOG_STACK=DAILY
   * Go to logging.php in daily index copy the code bellow and past it in the right place
   * 'daily' => [
   *        'driver' => 'daily',
   *        'path' => storage_path('logs/laravel.log'),
   *        'level' => env('LOG_LEVEL', 'debug'),
   *        'days' => env('LOG_DAILY_DAYS', 14),
   *        'tap' => [App\Logging\LogFormatter::class],
   *        'replace_placeholders' => true,
   *   ],
   */

    public function __invoke(\Illuminate\Log\Logger $logger)
    {
        foreach ($logger->getHandlers() as $handler) {
            $formatter = new JsonFormatter();

            $formatter->setJsonPrettyPrint(true);
            $handler->setFormatter($formatter);
        }
    }
}