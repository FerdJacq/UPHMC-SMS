<?php

namespace App\Jobs;

use App\Enums\GatewayType;
use App\Models\SmsGateway;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;

class CleanSmsInboxJob implements ShouldBeUnique, ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;

    public int $timeout = 120;

    public int $uniqueFor = 55;

    public function handle(): void
    {
        $gateway = SmsGateway::query()
            ->where('type', GatewayType::MODEM->value)
            ->where('is_active', true)
            ->orderBy('priority')
            ->first();

        if ($gateway === null) {
            Log::info('[CleanSmsInboxJob] No active modem gateway found. Skipping inbox cleanup.');
            return;
        }

        $script = base_path('scripts/sms/clean_sms_inbox.py');

        if (! is_file($script)) {
            Log::error("[CleanSmsInboxJob] Python inbox cleanup script not found at [{$script}]");
            return;
        }

        $config = (array) ($gateway->config ?? []);
        $port = $config['port'] ?? null;

        if (blank($port)) {
            Log::warning("[CleanSmsInboxJob] Modem gateway #{$gateway->id} is missing a port. Skipping inbox cleanup.");
            return;
        }

        $baudRate = (int) ($config['baud_rate'] ?? 115200);
        $timeout = (int) ($config['timeout'] ?? 10);

        $result = Process::path(base_path())
            ->env($this->databaseEnvironment())
            ->run([
                $this->pythonBinary(),
                $script,
                '--port',
                (string) $port,
                '--baud',
                (string) $baudRate,
                '--timeout',
                (string) $timeout,
            ]);

        $output = trim($result->output() ?: $result->errorOutput());

        if ($result->successful()) {
            Log::info(str_starts_with($output, 'SKIP:')
                ? '[CleanSmsInboxJob] Inbox cleanup skipped.'
                : '[CleanSmsInboxJob] Inbox cleanup finished.', [
                'gateway_id' => $gateway->id,
                'port' => $port,
                'output' => $output,
            ]);
            return;
        }

        Log::error('[CleanSmsInboxJob] Inbox cleanup failed.', [
            'gateway_id' => $gateway->id,
            'port' => $port,
            'exit_code' => $result->exitCode(),
            'output' => $output,
        ]);

        throw new \RuntimeException($output !== '' ? $output : 'Inbox cleanup script failed.');
    }

    private function databaseEnvironment(): array
    {
        $connection = config('database.default');
        $database = config("database.connections.{$connection}", []);

        return [
            'DB_CONNECTION' => $connection,
            'DB_HOST' => (string) ($database['host'] ?? env('DB_HOST', '127.0.0.1')),
            'DB_PORT' => (string) ($database['port'] ?? env('DB_PORT', '5432')),
            'DB_DATABASE' => (string) ($database['database'] ?? env('DB_DATABASE', '')),
            'DB_USERNAME' => (string) ($database['username'] ?? env('DB_USERNAME', '')),
            'DB_PASSWORD' => (string) ($database['password'] ?? env('DB_PASSWORD', '')),
            'DB_SSLMODE' => (string) ($database['sslmode'] ?? env('DB_SSLMODE', 'prefer')),
        ];
    }

    public function uniqueId(): string
    {
        return 'clean-sms-inbox';
    }

    private function pythonBinary(): string
    {
        return PHP_OS_FAMILY === 'Windows' ? 'python' : 'python3';
    }
}
