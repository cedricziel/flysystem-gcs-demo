<?php
include_once __DIR__.'/../vendor/autoload.php';

use GCSDemo\Application;
use League\Flysystem\Filesystem;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

putenv('GOOGLE_APPLICATION_CREDENTIALS='.__DIR__.'/../res/test-key.json');

$app = new Application();

/**
 * List a folders content
 */
$app->get(
    '/',
    function (Request $request) use ($app) {
        $directory = $request->query->get('dir', '/');

        /** @var Filesystem $filesystem */
        $filesystem = $app['flysystem'];
        $files = $filesystem->listContents($directory);

        return $app->render(
            'list.html.twig',
            [
                'directory' => $directory,
                'files'     => $files,
            ]
        );
    }
)->bind('list');

/**
 * Uploads a file to a given directory
 */
$app->post(
    '/upload',
    function (Request $request) use ($app) {
        $directory = $request->query->get('dir', '/');

        /** @var \Symfony\Component\HttpFoundation\File\UploadedFile $file */
        $file = $request->files->get('file');

        if (!$file) {
            return new RedirectResponse('/');
        }

        /** @var Filesystem $filesystem */
        $filesystem = $app['flysystem'];
        $filesystem->put($directory.'/'.$file->getClientOriginalName(), file_get_contents($file->getPathname()));

        return new RedirectResponse('/?dir='.$directory);
    }
)->bind('upload');

$app->run();
