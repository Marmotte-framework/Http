<?php

namespace Marmotte\Http\Stream;

use PHPUnit\Framework\TestCase;

class StreamTest extends TestCase
{
    /** @psalm-suppress PropertyNotSetInConstructor */
    private StreamFactory $factory;

    protected function setUp(): void
    {
        $this->factory = new StreamFactory();
    }

    public function testTell(): void
    {
        $stream = $this->factory->createStream('Hello');
        self::assertSame(0, $stream->tell());

        $stream->seek(2);
        self::assertSame(2, $stream->tell());
    }

    public function testIsWritable(): void
    {
        $stream = $this->factory->createStreamFromFile('php://temp', 'r');
        self::assertFalse($stream->isWritable());

        $stream2 = $this->factory->createStreamFromFile('php://temp', 'r+');
        self::assertTrue($stream2->isWritable());
    }

    public function testIsReadable(): void
    {
        $stream = $this->factory->createStreamFromFile(__DIR__ . '/../Fixtures/test.txt', 'a');
        self::assertFalse($stream->isReadable());

        $stream2 = $this->factory->createStreamFromFile('php://temp', 'r');
        self::assertTrue($stream2->isReadable());
    }

    public function testGetSize(): void
    {
        $stream = $this->factory->createStreamFromFile(__DIR__ . '/../Fixtures/test.txt');
        self::assertSame(strlen(file_get_contents(__DIR__ . '/../Fixtures/test.txt')), $stream->getSize());
    }

    public function testClose(): void
    {
        $stream = $this->factory->createStream('Hello');
        $stream->close();

        self::assertFalse($stream->isReadable());
        self::assertFalse($stream->isWritable());
        self::assertFalse($stream->isSeekable());
    }

    public function testIsSeekable(): void
    {
        $stream = $this->factory->createStream('Hello');

        self::assertTrue($stream->isSeekable());
    }

    public function testWrite(): void
    {
        $stream = $this->factory->createStream();
        $stream->write('Hello');
        $stream->rewind();
        self::assertSame('Hello', $stream->getContents());
    }

    public function testDetach(): void
    {
        $resource = fopen('php://temp', 'r+');
        $stream   = $this->factory->createStreamFromResource($resource);
        $stream->detach();

        self::assertFalse($stream->isReadable());
        self::assertFalse($stream->isWritable());
        self::assertFalse($stream->isSeekable());
    }

    public function testRead(): void
    {
        $stream = $this->factory->createStream('Hello');
        self::assertSame('Hello', $stream->read(5));
    }

    public function testRewind(): void
    {
        $stream = $this->factory->createStream('');
        $stream->write('abcdef');
        $stream->rewind();
        self::assertSame('a', $stream->read(1));
    }

    public function testGetMetadata(): void
    {
        $filename = __DIR__ . '/../Fixtures/test.txt';
        $resource = fopen($filename, 'r');
        $stream   = $this->factory->createStreamFromResource($resource);
        self::assertSame($filename, $stream->getMetadata('uri'));
    }

    public function testSeek(): void
    {
        $stream = $this->factory->createStream();
        $stream->write('abcdef');
        $stream->seek(2);
        self::assertSame('c', $stream->read(1));
    }

    public function testGetContents(): void
    {
        $stream = $this->factory->createStream('Hello');
        self::assertSame('Hello', $stream->getContents());
    }

    public function testEof(): void
    {
        $stream = $this->factory->createStream('Hello');
        $stream->getContents();
        self::assertTrue($stream->eof());
    }
}
