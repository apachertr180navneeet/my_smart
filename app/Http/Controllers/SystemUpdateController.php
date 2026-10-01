<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class SystemUpdateController extends Controller
{
    /**
     * Run database migrations and clear compiled caches.
     */
    public function updateDb(Request $request)
    {
        try {
            Artisan::call('migrate', ['--force' => true]);
            $migrateOutput = Artisan::output();

            Artisan::call('optimize:clear');
            $cacheOutput = Artisan::output();

            return response()->json([
                'status' => true,
                'message' => 'Database migrations executed and caches cleared successfully.',
                'migrate_output' => trim($migrateOutput),
                'cache_output' => trim($cacheOutput),
            ], 200);
        } catch (\Exception $e) {
            Log::error('DB Update Error: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Failed to update database: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Run composer commands (e.g. dump-autoload, install, update).
     */
    public function composerUpdate(Request $request)
    {
        @set_time_limit(300);
        @ini_set('memory_limit', '1024M');

        $action = $request->input('action', 'dump-autoload');
        $allowedActions = ['dump-autoload', 'dumpautoload', 'update', 'install', 'diagnose'];

        if (!in_array($action, $allowedActions)) {
            $action = 'dump-autoload';
        }

        // Set PHP in environment PATH for composer
        $phpDir = 'C:\\laragon\\bin\\php\\php-8.3.33-Win32-vs16-x64';
        $currentPath = getenv('PATH') ?: '';
        if (is_dir($phpDir) && strpos($currentPath, $phpDir) === false) {
            putenv('PATH=' . $phpDir . ';' . $currentPath);
        }

        $composerBin = 'C:\\composer\\composer.bat';
        if (!file_exists($composerBin)) {
            $composerBin = 'composer';
        }

        $projectRoot = base_path();
        $command = '"' . $composerBin . '" ' . $action . ' --no-interaction --prefer-dist 2>&1';
        if ($action === 'dump-autoload' || $action === 'dumpautoload') {
            $command = '"' . $composerBin . '" dump-autoload -o --no-interaction 2>&1';
        }

        $output = [];
        $returnVar = 0;

        exec('cd /d "' . $projectRoot . '" && ' . $command, $output, $returnVar);

        $outputText = implode("\n", $output);

        if ($returnVar === 0) {
            return response()->json([
                'status' => true,
                'message' => 'Composer command [' . $action . '] executed successfully.',
                'command' => $command,
                'output' => $outputText,
            ], 200);
        } else {
            Log::error('Composer Command Failed: ' . $command . "\nOutput: " . $outputText);
            return response()->json([
                'status' => false,
                'message' => 'Composer command [' . $action . '] failed with exit code ' . $returnVar,
                'command' => $command,
                'output' => $outputText,
            ], 500);
        }
    }

    /**
     * Complete system update: migrate database, composer autoload, clear caches.
     */
    public function systemUpdate(Request $request)
    {
        $dbResult = $this->updateDb($request);
        $composerResult = $this->composerUpdate($request);

        return response()->json([
            'status' => true,
            'message' => 'System update completed.',
            'db_update' => json_decode($dbResult->getContent(), true),
            'composer_update' => json_decode($composerResult->getContent(), true),
        ], 200);
    }
}
