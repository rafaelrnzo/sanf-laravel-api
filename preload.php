<?php

class Preloader
{
    private array $ignores = [];

    private static int $count = 0;

    private array $paths;

    private array $fileMap;

    public function __construct(string ...$paths)
    {
        $this->paths = $paths;

        // We'll use composer's classmap
        // to easily find which classes to autoload,
        // based on their filename
        $classMap = require __DIR__ . '/vendor/composer/autoload_classmap.php';

        $this->fileMap = array_flip($classMap);
    }

    public function paths(string ...$paths): Preloader
    {
        $this->paths = array_merge(
            $this->paths,
            $paths
        );

        return $this;
    }

    public function ignore(string ...$names): Preloader
    {
        $this->ignores = array_merge(
            $this->ignores,
            $names
        );

        return $this;
    }

    public function load(): void
    {
        // We'll loop over all registered paths
        // and load them one by one
        foreach ($this->paths as $path) {
            $this->loadPath(rtrim($path, '/'));
        }

        $count = self::$count;

        echo "[Preloader] Preloaded {$count} classes" . PHP_EOL;
    }

    private function loadPath(string $path): void
    {
        $ignorePaths = [
            '/var/www/app/vendor/symfony/polyfill-intl-normalizer',
            '/var/www/app/vendor/symfony/polyfill-php80',
            '/var/www/app/vendor/symfony/routing',
            '/var/www/app/vendor/nesbot/carbon/src/Carbon/Translator.php',
            '/var/www/app/vendor/nesbot/carbon/src/Carbon/TranslatorImmutable.php',
            '/var/www/app/vendor/symfony/http-kernel/HttpClientKernel.php',
        ];

        if (in_array($path, $ignorePaths)) {
            return;
        }

        // If the current path is a directory,
        // we'll load all files in it
        if (is_dir($path)) {
            $this->loadDir($path);
            return;
        }

        // Otherwise we'll just load this one file
        $this->loadFile($path);
    }

    private function loadDir(string $path): void
    {
        $handle = opendir($path);

        // We'll loop over all files and directories
        // in the current path,
        // and load them one by one
        while ($file = readdir($handle)) {
            if (in_array($file, ['.', '..'])) {
                continue;
            }

            $this->loadPath("{$path}/{$file}");
        }

        closedir($handle);
    }

    private function loadFile(string $path): void
    {
        // We resolve the classname from composer's autoload mapping
        $class = $this->fileMap[$path] ?? null;

        // And use it to make sure the class shouldn't be ignored
        if ($this->shouldIgnore($class)) {
            return;
        }

        // Finally we require the path,
        // causing all its dependencies to be loaded as well
//        require_once($path);
        opcache_compile_file($path);

        self::$count++;

        //echo "[Preloader] Preloaded `{$class}`" . PHP_EOL;
    }

    private function shouldIgnore(?string $name): bool
    {
        if ($name === null) {
            return true;
        }

        foreach ($this->ignores as $ignore) {
            if (strpos($name, $ignore) === 0) {
                return true;
            }
        }

        return false;
    }
}

(new Preloader())
    ->paths(
        __DIR__ . '/vendor/guzzlehttp',
        __DIR__ . '/vendor/laravel',
        __DIR__ . '/vendor/monolog',
        __DIR__ . '/vendor/nesbot/carbon',
        __DIR__ . '/vendor/nikic',
        __DIR__ . '/vendor/php-http',
        __DIR__ . '/vendor/psr',
        __DIR__ . '/vendor/sebastian',
        __DIR__ . '/vendor/spatie',
        __DIR__ . '/vendor/symfony',
        __DIR__ . '/vendor/vlucas',
    )
    ->ignore(
        'Core\Location\Data\Models\MasterCountryModel',
        'Illuminate\Database\PDO',
        'Illuminate\Database\DBAL\TimestampType',
        'Illuminate\Foundation\Auth',
        'Illuminate\Foundation\Http',
        'Illuminate\Http',
        'Illuminate\Session\SymfonySessionDecorator',
        'Illuminate\Validation\Concerns\FilterEmailValidation',
        'Illuminate\Foundation\Testing',
        'Illuminate\Testing',
        'Illuminate\Http\Testing',
        'Illuminate\Support\Testing',
        'Carbon\PHPStan',
        'Monolog\Test\TestCase',
        'Carbon\Doctrine',
        'Carbon\Laravel\ServiceProvider',
        'Symfony\Component\HttpFoundation\ResponseHeaderBag',
        'Symfony\Bridge\PsrHttpMessage\Factory\UploadedFile',
        'Symfony\Component\HttpClient\Internal',
        'Symfony\Component\HttpKernel\Bundle',
        'Symfony\Component\HttpKernel\HttpKernelBrowser',
        'Symfony\Component\HttpKernel\Config\FileLocator',
        'Symfony\Component\HttpFoundation',
        'Symfony\Component\DependencyInjection',
        'Symfony\Component\Console\DependencyInjection',
        'Symfony\Component\EventDispatcher\DependencyInjection',
        'Symfony\Component\HttpClient\DependencyInjection',
        'Symfony\Component\HttpFoundation\DependencyInjection',
        'Symfony\Component\HttpKernel\DependencyInjection',
        'Symfony\Component\Mime\DependencyInjection',
        'Symfony\Component\Translation\DependencyInjection',
        'Symfony\Polyfill\Php80',
        'Symfony\Polyfill\Intl\Normalizer',
        'Symfony\Component\Console\Tester',
        'Symfony\Component\VarDumper\Test',
        'Symfony\Contracts\Translation\Test',
        'Symfony\Component\Translation\Test',
        'Symfony\Contracts\Service\Test',
        'Symfony\Component\Mailer\Test',
        'Symfony\Component\HttpFoundation\Test',
        'Symfony\Contracts\HttpClient\Test',
        'Symfony\Component\Mime\Test',
    )
    ->load();

