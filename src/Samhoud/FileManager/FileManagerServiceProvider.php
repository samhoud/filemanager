<?php
namespace Samhoud\FileManager;

use Illuminate\Support\ServiceProvider;

class FileManagerServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../../config/filemanager.php' => config_path('filemanager.php'),
        ], 'config');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/filemanager.php', 'filemanager'
        );
        $this->app->singleton('filesystem', function () {
            return new FilesystemManager($this->app);
        });
        $this->app->bindShared(ImageManager::class, function ($app) {
            $disk = $this->getDisk('imagemanagerdisk');
            $filesystem = $this->app['filesystem']->disk($disk);
            $settings = ['uploadSettings' => $this->app['config']->get("filemanager.uploadlocation")];

            return new ImageManager($filesystem, $settings, new \Intervention\Image\ImageManager());
        });
        $this->app->bindShared(FileManager::class, function ($app) {
            $disk = $this->getDisk('filemanagerdisk');
            $filesystem = $this->app['filesystem']->disk($disk);
            $settings = ['uploadSettings' => $this->app['config']->get("filemanager.uploadlocation")];

            return new FileManager($filesystem, $settings);
        });
    }

    private function getDisk($name)
    {
        $configItem = $this->app['config']->get("filemanager.{$name}");

        return ($configItem ? $configItem : $this->app['config']->get("filesystems.default"));
    }
}
