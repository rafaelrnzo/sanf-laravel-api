<?php

namespace App\Console\Commands;

use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;

class SyncMidtransLogoCommand extends Command
{
    protected $signature = 'midtrans:sync-logo';

    protected $description = 'Sync Midtrans logos from GitHub into disk:/midtrans-logo.';

    private Client $httpClient;

    private const EXIT_SUCCESS = 0;

    private const EXIT_FAILURE = 1;

    public function __construct()
    {
        parent::__construct();

        $this->httpClient = new Client([
            'headers' => ['User-Agent' => 'sanf-api'],
            'http_errors' => false,
            'timeout' => 30,
        ]);
    }

    public function handle(): int
    {
        $this->info('Fetching Midtrans logo catalog...');

        $response = $this->httpClient->get('https://api.github.com/repos/veritrans/logo/contents/logo');

        if ($response->getStatusCode() !== 200) {
            $this->error(sprintf('Unable to fetch logo list. HTTP %s', $response->getStatusCode()));

            return self::EXIT_FAILURE;
        }

        $files = collect(json_decode((string) $response->getBody(), true))
            ->filter(fn (array $item): bool => ($item['type'] ?? null) === 'file')
            ->values();

        if ($files->isEmpty()) {
            $this->warn('No files returned from GitHub. Aborting.');

            return self::EXIT_FAILURE;
        }

        $disk = Storage::disk($this->resolveDiskName());
        $targetDirectory = config('midtrans.logo.directory');

        if (! $disk->exists($targetDirectory)) {
            $disk->makeDirectory($targetDirectory);
        }

        collect($disk->files($targetDirectory))->each(fn (string $path) => $disk->delete($path));

        $totalBytes = 0;

        $files->each(function (array $file) use ($disk, $targetDirectory, &$totalBytes): void {
            $totalBytes += $this->downloadFile($file, $disk, $targetDirectory);
        });

        $this->info(sprintf(
            'Synced %d Midtrans logo files (%s).',
            $files->count(),
            $this->formatBytes($totalBytes)
        ));

        return self::EXIT_SUCCESS;
    }

    private function downloadFile(array $file, FilesystemAdapter $disk, string $targetDirectory): int
    {
        $filename = $file['name'] ?? null;
        $downloadUrl = $file['download_url'] ?? null;

        if (! $filename || ! $downloadUrl) {
            $this->warn('Skipping entry with missing filename or download URL.');

            return 0;
        }

        $this->line(sprintf('Downloading %s...', $filename));

        $response = $this->httpClient->get($downloadUrl);

        if ($response->getStatusCode() !== 200) {
            $this->warn(sprintf('Failed to download %s (HTTP %s).', $filename, $response->getStatusCode()));

            return 0;
        }

        $contents = (string) $response->getBody();

        $disk->put($targetDirectory . '/' . $filename, $contents);

        return strlen($contents);
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes === 0) {
            return '0 B';
        }

        $units = ['B', 'KB', 'MB', 'GB'];
        $power = min((int) floor(log($bytes, 1024)), count($units) - 1);
        $value = $bytes / (1024 ** $power);

        return sprintf('%.2f %s', $value, $units[$power]);
    }

    private function resolveDiskName(): string
    {
        return config('midtrans.logo.disk') ?? config('filesystems.default', 'local');
    }
}
