<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

/**
 * One-time setup command for the Python SMS modem dependency.
 *
 * Usage:
 *   php artisan sms:setup-modem
 *   php artisan sms:setup-modem --check   (verify only, no install)
 */
class SetupModem extends Command
{
    protected $signature   = 'sms:setup-modem {--check : Only verify, do not install}';
    protected $description = 'Install and verify Python dependencies for the GSM modem SMS driver';

    public function handle(): int
    {
        $this->info("=== UPHMC SMS — Modem Setup ===");
        $this->newLine();

        $checkOnly = (bool) $this->option('check');
        $allGood   = true;

        $pythonBin = PHP_OS_FAMILY === 'Windows' ? 'python' : 'python3';
        $pipBin = PHP_OS_FAMILY === 'Windows' ? 'pip' : 'pip3';
        $requirementsPath = base_path('scripts/sms/requirements.txt');
        $installNotice = "Run: {$pipBin} install -r scripts/sms/requirements.txt";

        // ── 1. Python availability ────────────────────────────────────────────
        $this->comment("1. Python");
        exec("{$pythonBin} --version 2>&1", $pyOut, $pyCode);
        $pythonVersion = implode('', $pyOut);

        if ($pyCode !== 0 || ! str_contains($pythonVersion, 'Python')) {
            $this->error("   Python not found. Install Python 3.8+ from https://python.org");
            $this->error("   Make sure '{$pythonBin}' is on your PATH.");
            return self::FAILURE;
        }

        $this->line("   {$pythonVersion} ✓");

        // ── 2. pip availability ───────────────────────────────────────────────
        $this->comment("2. pip");
        exec("{$pipBin} --version 2>&1", $pipOut, $pipCode);
        $pipVersion = implode('', $pipOut);

        if ($pipCode !== 0) {
            $this->error("   pip not found. Ensure pip is installed with Python.");
            return self::FAILURE;
        }

        $this->line("   " . substr($pipVersion, 0, 40) . " ✓");

        // ── 3. Python dependencies ────────────────────────────────────────────
        $this->comment("3. Python dependencies");

        $dependencies = [
            [
                'label' => 'pyserial',
                'check' => sprintf(
                    '%s -c "import serial; print(serial.__version__)" 2>&1',
                    $pythonBin
                ),
            ],
            [
                'label' => 'psycopg2',
                'check' => sprintf(
                    '%s -c "import psycopg2; print(psycopg2.__version__)" 2>&1',
                    $pythonBin
                ),
            ],
        ];

        $missingDependencies = [];

        foreach ($dependencies as $dependency) {
            exec($dependency['check'], $depOut, $depCode);
            $version = trim(implode('', $depOut));

            if ($depCode !== 0 || $version === '') {
                $this->error("   {$dependency['label']} NOT installed.");
                $missingDependencies[] = $dependency['label'];
                $allGood = false;
                continue;
            }

            $this->line("   {$dependency['label']} {$version} ✓");
        }

        if ($missingDependencies !== []) {
            if ($checkOnly) {
                $this->warn("   {$installNotice}");
            } else {
                $this->warn("   Missing dependencies detected — installing from requirements.txt...");

                $installCmd = "{$pipBin} install -r " . escapeshellarg($requirementsPath) . " 2>&1";
                exec($installCmd, $installOut, $installCode);

                if ($installCode !== 0) {
                    $this->error("   Install failed: " . implode("\n", $installOut));
                    return self::FAILURE;
                }

                $allGood = true;

                foreach ($dependencies as $dependency) {
                    exec($dependency['check'], $depOut, $depCode);
                    $version = trim(implode('', $depOut));

                    if ($depCode !== 0 || $version === '') {
                        $this->error("   {$dependency['label']} still not available after install.");
                        $allGood = false;
                        continue;
                    }

                    $this->info("   {$dependency['label']} {$version} ✓");
                }
            }
        }

        // ── 4. Script existence ───────────────────────────────────────────────
        $this->comment("4. Python scripts");
        $scriptPath = base_path('scripts/sms/sms_send.py');
        $cleanScriptPath = base_path('scripts/sms/clean_sms_inbox.py');

        if (! file_exists($scriptPath)) {
            $this->error("   Script not found at: {$scriptPath}");
            $allGood = false;
        } else {
            $this->line("   Found send script: {$scriptPath} ✓");
        }

        if (! file_exists($cleanScriptPath)) {
            $this->error("   Script not found at: {$cleanScriptPath}");
            $allGood = false;
        } else {
            $this->line("   Found clean script: {$cleanScriptPath} ✓");
        }

        // ── 5. Gateway DB config ──────────────────────────────────────────────
        $this->comment("5. Gateway config");
        try {
            $gateway = \App\Models\SmsGateway::where('is_active', true)->orderBy('priority')->first();
            if ($gateway) {
                $port = $gateway->config['port'] ?? 'NOT SET';
                $baud = $gateway->config['baud_rate'] ?? 'NOT SET';
                $this->line("   Name:      {$gateway->name}");
                $this->line("   Port:      {$port}");
                $this->line("   Baud rate: {$baud}");

                // ── 6. Quick Python port open test ────────────────────────────
                if ($allGood && ! $checkOnly && $port !== 'NOT SET') {
                    $this->comment("6. Port open test via Python");

                    // Use a proper Python script string with correct quoting.
                    // Pass port as a command-line argument to avoid shell quoting issues.
                    $cmd = sprintf(
                        '%s -c "import sys,serial; s=serial.Serial(sys.argv[1],%d,timeout=3,dsrdtr=True); s.close(); print(\'Port OK\')" %s 2>&1',
                        $pythonBin,
                        (int) $baud,
                        escapeshellarg($port)
                    );

                    exec($cmd, $testOut, $testCode);
                    $testResult = implode('', $testOut);

                    if ($testCode === 0 && str_contains($testResult, 'Port OK')) {
                        $this->info("   Port {$port} accessible via pyserial ✓");
                    } else {
                        $this->error("   Port test failed: {$testResult}");
                        $this->warn("   Ensure modem is connected and no other application has {$port} open.");
                        $allGood = false;
                    }
                }
            } else {
                $this->warn("   No active gateway found in database.");
                $allGood = false;
            }
        } catch (\Throwable $e) {
            $this->error("   DB error: " . $e->getMessage());
            $allGood = false;
        }

        $this->newLine();

        if ($allGood) {
            $this->info("=== All checks passed. SMS modem is ready. ===");
            $this->newLine();
            $this->line("Next steps:");
            $this->line("  1. Test send:     {$pythonBin} scripts/sms/sms_send.py --port {$port} --baud {$baud} --to 09XXXXXXXXX --message \"Test\"");
            $this->line("  2. Test cleanup:  {$pythonBin} scripts/sms/clean_sms_inbox.py --port {$port} --baud {$baud}");
            $this->line("  3. Start worker:  php artisan queue:work --queue=sms --sleep=3 --tries=3 --timeout=120");
        } else {
            $this->warn("=== Some checks failed. Review output above. ===");
        }

        return $allGood ? self::SUCCESS : self::FAILURE;
    }
}
