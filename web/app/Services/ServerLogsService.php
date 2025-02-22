<?php

namespace App\Services;

class ServerLogsService
{
    public function handleLogsCollection()
    {
        try {
            $logFile = file(storage_path().'/logs/laravel.log');
        } catch (\Exception $e) {
            $logFile = [];
        }
        $logCollection = [];
        $logString = "";

        foreach ($logFile as $line_number => $line) {
            $logString .= $line;
        }

        // check if app is running in production or local
        if (config('app.env') == 'production') {
            $errorName = 'production.ERROR:';
        } else if (config('app.env') == 'local') {
            $errorName = 'local.ERROR:';
        } else {
            $errorName = 'ERROR';
        }

        // file with logs is empty
        if (!str_contains($logString, $errorName)) {
            return response()->json([
                'data' => ''
            ]);
        }

        $logArray = explode($errorName, $logString);

        for ($i = 0; $i < count($logArray); $i += 2) {
            if ($i == 0) {
                $logCollection[] = array(
                    'date' => substr($logArray[$i], 1, 19),
                    'content' => substr($logArray[$i+1], 0, 500)
                );
            } else {
                $lengthPreviousContent = strlen($logArray[$i-1]);
                $logCollection[] = array(
                    'date' => substr($logArray[$i-1], $lengthPreviousContent-21, 19),
                    'content' => substr($logArray[$i], 0, 500)
                );
            }
        }
    }

}
