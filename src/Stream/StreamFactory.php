<?php

namespace Marmotte\Http\Stream;

use Marmotte\Brick\Services\Service;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;

#[Service]
final class StreamFactory implements StreamFactoryInterface
{
    /**
     * @throws StreamException
     */
    public function createStream(string $content = ''): StreamInterface
    {
        $file   = fopen('php://temp', 'r+');
        $stream = $this->createStreamFromResource($file);
        if ($stream->write($content) !== strlen($content)) {
            throw new StreamException('Fail to write in stream');
        }
        $stream->rewind();

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
