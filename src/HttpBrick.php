<?php

namespace Marmotte\Http;

use Marmotte\Brick\Brick;
use Marmotte\Brick\Services\ServiceManager;
use Marmotte\Http\File\UploadedFileFactory;
use Marmotte\Http\Request\ServerRequest;
use Marmotte\Http\Stream\StreamException;
use Marmotte\Http\Stream\StreamFactory;
use Marmotte\Http\Uri\UriFactory;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Message\UriInterface;

class HttpBrick implements Brick
{
    public function init(ServiceManager $service_manager): void
    {
        $service_manager->addService($this->getRequestFromGlobals());
    }

    private function getRequestFromGlobals(): ServerRequestInterface
    {
        /** @psalm-suppress MixedArgument for getallheaders() */
        return new ServerRequest(
            $_SERVER,
            $_COOKIE,
            $_GET,
            $this->getFilesFromGlobals(),
            $_POST,
            [],
            '',
            $_SERVER['REQUEST_METHOD'] ?? ServerRequest::METHOD_GET,
            $this->getUriFromGlobals(),
            isset($_SERVER['SERVER_PROTOCOL']) ? str_replace('HTTP/', '', $_SERVER['SERVER_PROTOCOL']) : '1.1',
            getallheaders() ?: [],
            $this->getBodyFromGlobals()
        );
    }

    /**
     * @return array<string, UploadedFileInterface>
     */
    private function getFilesFromGlobals(): array
    {
        $files = [];

        $file_factory   = new UploadedFileFactory();
        $stream_factory = new StreamFactory();
        foreach ($_FILES as $upload_name => $values) {
            try {
                $stream = $stream_factory->createStreamFromFile($values['tmp_name'], 'r');
            } catch (StreamException) {
                $stream = $stream_factory->createStream('');
            }

            $files[$upload_name] = $file_factory->createUploadedFile(
                $stream,
                $values['size'],
                $values['error'],
                $values['name'],
                $values['type']
            );
        }

        return $files;
    }

    private function getUriFromGlobals(): UriInterface
    {
        $uri = (new UriFactory())->createUri('');

        $uri = $uri->withScheme(!empty($_SERVER['HTTPS']) ? 'https' : 'http');

        $find_port = false;
        if (isset($_SERVER['HTTP_HOST'])) {
            $components = parse_url($_SERVER['HTTP_HOST']);
            if (isset($components['host'])) {
                $uri = $uri->withHost($components['host']);
            }
            if (isset($components['port'])) {
                $find_port = true;
                $uri       = $uri->withPort($components['port']);
            }
        } else if (isset($_SERVER['SERVER_NAME'])) {
            $uri = $uri->withHost($_SERVER['SERVER_NAME']);
        } else if (isset($_SERVER['SERVER_ADDR'])) {
            $uri = $uri->withHost($_SERVER['SERVER_ADDR']);
        }

        if (!$find_port && isset($_SERVER['SERVER_PORT'])) {
            $uri = $uri->withPort((int) $_SERVER['SERVER_PORT']);
        }

        $find_query = false;
        if (isset($_SERVER['REQUEST_URI'])) {
            $components = parse_url($_SERVER['REQUEST_URI']);
            if (isset($components['path'])) {
                $uri = $uri->withPath($components['path']);
            }
            if (isset($components['query'])) {
                $find_query = true;
                $uri        = $uri->withQuery($components['query']);
            }
            if (isset($components['fragment'])) {
                $uri = $uri->withFragment($components['fragment']);
            }
        }

        if (!$find_query && isset($_SERVER['QUERY_STRING'])) {
            $uri = $uri->withQuery($_SERVER['QUERY_STRING']);
        }

        if (isset($_SERVER['PHP_AUTH_USER'])) {
            $uri = $uri->withUserInfo($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW'] ?? null);
        }

        return $uri;
    }

    private function getBodyFromGlobals(): StreamInterface
    {
        return (new StreamFactory())->createStreamFromFile('php://input', 'r');
    }
}
