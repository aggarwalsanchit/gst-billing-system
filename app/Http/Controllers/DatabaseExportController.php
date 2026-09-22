<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Symfony\Component\Process\Process;

class DatabaseExportController extends Controller
{
    public function export(Request $request)
    {
        set_time_limit(600);
        ini_set('memory_limit', '512M');

        $database = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');
        $host     = config('database.connections.mysql.host');
        $port     = config('database.connections.mysql.port', 3306);

        // Temp file in system temp dir (NOT storage)
        $filename = 'backup_' . $database . '_' . now()->format('Y-m-d_His') . '.sql';
        $tempPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $filename;

        // Try mysqldump first
        $dumpBinary = $this->findMysqldump();

        if ($dumpBinary) {
            $this->exportWithMysqldump(
                $dumpBinary, $host, $port, $username, $password, $database, $tempPath
            );
        } else {
            $this->exportWithPhp($tempPath);
        }

        if (!file_exists($tempPath) || filesize($tempPath) === 0) {
            return back()->with('error', 'Database export failed.');
        }

        // Download to user's system, then delete the temp file
        return Response::download($tempPath, $filename, [
            'Content-Type' => 'application/sql',
        ])->deleteFileAfterSend(true);
    }

    private function findMysqldump(): ?string
    {
        $candidates = [
            'C:\xampp\mysql\bin\mysqldump.exe',
            'C:\wamp64\bin\mysql\mysql8.0.31\bin\mysqldump.exe',
            'C:\wamp\bin\mysql\mysql5.7.36\bin\mysqldump.exe',
            'C:\laragon\bin\mysql\mysql-8.0.30-winx64\bin\mysqldump.exe',
            '/usr/bin/mysqldump',
            '/usr/local/bin/mysqldump',
            '/opt/homebrew/bin/mysqldump',
        ];

        foreach ($candidates as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        // Try PATH lookup (Windows: where, Linux: which)
        $cmd = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' ? 'where' : 'which';
        $process = new Process([$cmd, 'mysqldump']);
        $process->run();

        if ($process->isSuccessful()) {
            $path = trim($process->getOutput());
            return $path ?: null;
        }

        return null;
    }

    private function exportWithMysqldump(
        string $binary, string $host, string $port,
        string $username, string $password,
        string $database, string $filePath
    ): void {
        $args = [
            $binary,
            '--host=' . $host,
            '--port=' . $port,
            '--user=' . $username,
            '--single-transaction',
            '--routines',
            '--triggers',
            '--add-drop-table',
            '--default-character-set=utf8mb4',
        ];

        if (!empty($password)) {
            $args[] = '--password=' . $password;
        }

        $args[] = $database;

        $process = new Process($args);
        $process->setTimeout(600);

        $handle = fopen($filePath, 'w');
        $process->run(function ($type, $buffer) use ($handle) {
            fwrite($handle, $buffer);
        });
        fclose($handle);

        if (!$process->isSuccessful()) {
            \Log::error('mysqldump failed', ['error' => $process->getErrorOutput()]);
            throw new \RuntimeException('mysqldump failed: ' . $process->getErrorOutput());
        }
    }

    private function exportWithPhp(string $filePath): void
    {
        $handle = fopen($filePath, 'w');

        fwrite($handle, "-- Database Export\n");
        fwrite($handle, "-- Generated: " . now()->toDateTimeString() . "\n\n");
        fwrite($handle, "SET FOREIGN_KEY_CHECKS=0;\n");
        fwrite($handle, "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n\n");

        $dbName = config('database.connections.mysql.database');
        $tables = DB::select('SHOW TABLES');
        $key    = 'Tables_in_' . $dbName;

        foreach ($tables as $tableRow) {
            $table = $tableRow->$key;

            $create    = DB::select("SHOW CREATE TABLE `{$table}`");
            $createSql = $create[0]->{'Create Table'} ?? null;

            if ($createSql) {
                fwrite($handle, "DROP TABLE IF EXISTS `{$table}`;\n");
                fwrite($handle, $createSql . ";\n\n");
            }

            $rows = DB::table($table)->get();

            foreach ($rows as $row) {
                $values = [];
                foreach ((array) $row as $value) {
                    if (is_null($value)) {
                        $values[] = 'NULL';
                    } elseif (is_numeric($value)) {
                        $values[] = $value;
                    } else {
                        $values[] = "'" . addslashes($value) . "'";
                    }
                }
                fwrite($handle, "INSERT INTO `{$table}` VALUES (" . implode(', ', $values) . ");\n");
            }
            fwrite($handle, "\n");
        }

        fwrite($handle, "SET FOREIGN_KEY_CHECKS=1;\n");
        fclose($handle);
    }
}