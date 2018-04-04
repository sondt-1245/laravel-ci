<?php

namespace App\Http\Controllers;

use Docker\Docker;
use Docker\API\Model\ContainersCreatePostBody;
use Docker\DockerClientFactory;
use Docker\Stream\AttachWebsocketStream;
use GuzzleHttp\Client as GuzzleClient;
use Http\Adapter\Guzzle6\Client as GuzzleAdapter;
use Docker\Context\Context;
use Docker\Context\ContextBuilder;
use Docker\API\Model\BuildInfo;
use Illuminate\Http\Request;

/**
 * @codeCoverageIgnore
 */
class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
//        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('home');
    }

    /**
     * @param Request $request
     */
    public function docker(Request $request)
    {
        $client = DockerClientFactory::create([
            'remote_socket' => 'unix:///var/run/docker.sock',
            'ssl' => false,
        ]);
        $docker = Docker::create($client);

        $containerConfig = new ContainersCreatePostBody();
        $containerConfig->setImage('ubuntu:latest');
        $containerConfig->setCmd(['echo', 'I am running a command']);
        // You need to attach stream of the container to docker
        $containerConfig->setAttachStdin(true);
        $containerConfig->setAttachStdout(true);
        $containerConfig->setAttachStderr(true);
        // Open the stdin stream from docker engine to the container
        $containerConfig->setOpenStdin(true);
        // Needed if you want to use process that rely on a tty, be aware as there is, in fact, no tty this may cause bug to
        // the underlying process in your container
        $containerConfig->setTty(true);

//        $containerCreateResult = $docker->containerCreate($containerConfig);

//        echo $containerCreateResult->getId();

        $containerId = '463bc03d8c2fedc4a6c14a11cdcac0caa24ee335f0baedee5c9f3cf931dfe6ca';

        $docker->containerStart($containerId);

        $attachStream = $docker->containerAttach($containerId, [
            'stream' => true,
            'stdin' => true,
            'stdout' => true,
            'stderr' => true
        ]);

        $attachStream->onStdout(function ($stdout) {
            echo $stdout;
        });
        $attachStream->onStderr(function ($stderr) {
            echo $stderr;
        });

        $attachStream->wait();

        $webSocketStream = $docker->containerAttachWebsocket($containerId, [
            'stream' => true,
            'stdout' => true,
            'stderr' => true,
            'stdin'  => true,
        ]);

        $line = $webSocketStream->read();
        $webSocketStream->write('i send input to the container');
    }

    /**
     * @param Request $request
     */
    public function image(Request $request)
    {
        $client = DockerClientFactory::create([
            'remote_socket' => 'tcp://0.0.0.0:4243',
            'ssl' => false,
        ]);
        $config = [
            'remote_socket' => 'tcp://0.0.0.0:4243'
        ];
        $guzzle = new GuzzleClient($config);
        $adapter = new GuzzleAdapter($guzzle);
        //$docker = Docker::create($adapter);

        $docker = Docker::create($client);

        $containerConfig = new ContainersCreatePostBody();
        $containerConfig->setImage('ubuntu:16.04', ['name' => 'mysql-workspace-example']);

        // You need to attach stream of the container to docker
        $containerConfig->setAttachStdin(true);
        $containerConfig->setAttachStdout(true);
        $containerConfig->setAttachStderr(true);

        // Open the stdin stream from docker engine to the container
        $containerConfig->setOpenStdin(true);
//        $containerConfig->setOpen
        // Needed if you want to use process that rely on a tty, be aware as there is, in fact, no tty this may cause bug to
        // the underlying process in your container
            $containerConfig->setTty(true);

        $containerCreateResult = $docker->containerCreate($containerConfig);
        $containerId = $containerCreateResult->getId();

        //$containerId = '01e8df6e5c97a1ec9e93bdbb026bbf873ae90733c922bfdda22f4a2f9ff9d10c';

        // You also need to set stream to true to get the logs, and tell which stream you want to attach
        $attachStream = $docker->containerAttach($containerId, [
            'stream' => true,
            'stdin' => true,
            'stdout' => true,
            'stderr' => true
        ]);

        $webSocketStream = $docker->containerAttachWebsocket($containerId, [
            'stream' => true,
            'stdout' => true,
            'stderr' => true,
            'stdin'  => true,
        ]);

        $docker->containerStart($containerId);

        $attachStream->onStdout(function ($stdout) {
            echo ( $stdout);
        });
        $attachStream->onStderr(function ($stderr) {
            echo $stderr;
        });

//        $attachStream->wait();
    }
}
