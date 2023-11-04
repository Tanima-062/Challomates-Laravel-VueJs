<?php

namespace App\Providers;

use Aws\S3\S3Client;
use League\Flysystem\Filesystem;
use League\Flysystem\Visibility;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use Illuminate\Filesystem\FilesystemAdapter;
use League\Flysystem\AwsS3V3\AwsS3V3Adapter;
use League\Flysystem\AwsS3V3\PortableVisibilityConverter;

class ExoscaleServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        Storage::extend('s3', function ($app, $config) {
            $adapter = new AwsS3V3Adapter(new S3Client([
                        'credentials' => [
                            'key' => $config['key'],
                            'secret' => $config['secret'],
                        ],
                        'region' => $config['region'],
                        'version' => $config['version'],
                        'endpoint' => $config['url'],
                    ]), $config['bucket'], '',  new PortableVisibilityConverter(
                        // Optional default for directories
                        Visibility::PUBLIC // or ::PRIVATE
                    ));

            return new FilesystemAdapter(
                new Filesystem($adapter, $config),
                $adapter,
                $config
            );
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
