<?php

namespace Marmotte\Http\Stream;

use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;

class StreamFactory implements StreamFactoryInterface
{
    /**
     * @throws StreamException
     */
    public function createStream(string $content = ''): StreamInterface
    {
        $file   = fopen('php://temp', 'r+');
        $stream = $this->createStreamFromResource($file);
        $stream->write($content);

        return $stream;
    }

    /**
     * @throws StreamException
     */
    public function createStreamFromFile(string $filename, string $mode = 'r'): StreamInterface
    {
        return $this->createStreamFromResource(fopen($filename, $mode));
    }

    /**
     * @throws StreamException
     */
    public function createStreamFromResource($resource): StreamInterface
    {
        return new Stream($resource);
    }
}
