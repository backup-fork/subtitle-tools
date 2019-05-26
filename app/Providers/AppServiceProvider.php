<?php

namespace App\Providers;

use App\Events\FileGroupChanged;
use App\Models\FileGroup;
use App\Subtitles\TextFileFormat;
use App\Subtitles\VobSub\VobSub2Srt;
use App\Subtitles\VobSub\VobSub2SrtInterface;
use App\Support\Utils\FileName;
use App\Support\Utils\TempDir;
use App\Support\Utils\TempFile;
use Faker\Generator as Faker;
use FileNameSeeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;
use League\Flysystem\Filesystem;
use Spatie\Dropbox\Client as DropboxClient;
use Spatie\FlysystemDropbox\DropboxAdapter;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        Passport::ignoreMigrations();

        $this->app->bind('TextFileFormat', function ($app, $args) {
            return new TextFileFormat();
        });

        $this->app->bind('FileName', function ($app, $args) {
            return new FileName();
        });

        $this->app->bind('TempFile', function ($app, $args) {
            return new TempFile();
        });

        $this->app->bind('TempDir', function ($app, $args) {
            return new TempDir();
        });

        $this->app->singleton(VobSub2SrtInterface::class, VobSub2Srt::class);
    }

    public function boot()
    {
        if ($this->app->environment('local', 'testing')) {
            $faker = app(Faker::class);

            $faker->addProvider(new FileNameSeeder($faker));
        }

        FileGroup::updated(function ($fileGroup) {
            FileGroupChanged::dispatch($fileGroup);
        });

        Storage::extend('dropbox', function ($app, $config) {
            $client = new DropboxClient(
                config('filesystems.disks.dropbox.key')
            );

            return new Filesystem(new DropboxAdapter($client));
        });
    }
}
