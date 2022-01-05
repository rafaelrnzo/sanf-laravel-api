<?php

namespace NbsPhp\Notification;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;
use NbsPhp\Notification\Repositories\EloquentUserNotificationRepository;
use NbsPhp\Notification\Repositories\UserNotificationRepositoryInterface;
use NbsPhp\Notification\Services\FcmService;
use NbsPhp\Notification\Services\PushNotificationServiceInterface;

class NotificationServiceProvider extends ServiceProvider
{
    public function boot(Filesystem $filesystem)
    {
        if ($this->app->runningInConsole()) {
            $this->registerPublishing($filesystem);
        }
    }
    public function register()
    {
        $this->app->bind(PushNotificationServiceInterface::class, FcmService::class);
        $this->app->bind(UserNotificationRepositoryInterface::class, EloquentUserNotificationRepository::class);

        $this->loadRoutesFrom(__DIR__ . '/routes.php');
    }

    public function registerPublishing(Filesystem $filesystem): void
    {
        $this->publishes([
            __DIR__ . '/../config/fcm.php' => $this->app->configPath('fcm.php'),
            __DIR__ . '/../config/notifications.php' => $this->app->configPath('notifications.php'),
            __DIR__ . '/../config/firebase-keys/.gitignore' => $this->app->configPath('firebase-keys/.gitignore'),
        ], ['config', 'notification-config']);

        $this->publishes([
            __DIR__ . '/../database/migrations/create_notification_channel_table.php.stub' => $this->getMigrationFileName($filesystem, 'create_notification_channel_table'),
            __DIR__ . '/../database/migrations/alter_notification_user_session_table.php.stub' => $this->getMigrationFileName($filesystem, 'alter_notification_user_session_table'),
            __DIR__ . '/../database/migrations/create_user_notification_table.php.stub' => $this->getMigrationFileName($filesystem, 'create_user_notification_table'),
        ], ['migrations', 'notification-migrations']);
    }

    protected function getMigrationFileName(Filesystem $filesystem, $filename)
    {
        $timestamp = date('Y_m_d_His');

        return Collection::make($this->app->databasePath() . DIRECTORY_SEPARATOR . 'migrations' . DIRECTORY_SEPARATOR)
            ->flatMap(function ($path) use ($filesystem, $filename) {
                return $filesystem->glob($path . "*_{$filename}.php");
            })
            ->push($this->app->databasePath("/migrations/{$timestamp}_{$filename}.php"))
            ->first();
    }
}
