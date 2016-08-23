<?php

namespace GCSDemo;

use CedricZiel\FlysystemGcs\GoogleCloudStorageAdapter;
use Dotenv\Dotenv;
use League\Flysystem\Filesystem;
use Silex\Application as BaseApplication;
use Silex\Provider\TwigServiceProvider;

class Application extends BaseApplication
{
    use BaseApplication\TwigTrait;

    public function __construct()
    {
        parent::__construct();

        $this->loadConfigFromEnv();
        $this->configure();
    }

    protected function loadConfigFromEnv()
    {
        $dotenv = new Dotenv(__DIR__.'/../../');
        $dotenv->load();
    }

    protected function configure()
    {
        $app = $this;

        $this->register(
            new TwigServiceProvider(),
            [
                'twig.path' => __DIR__.'/../../res/views',
            ]
        );

        $app['debug'] = true;

        $app['flysystem'] = function () use ($app) {

            $gcsAdapter = new GoogleCloudStorageAdapter(null, ['bucket' => getenv('GCLOUD_BUCKET')]);

            return new Filesystem($gcsAdapter);
        };
    }
}
