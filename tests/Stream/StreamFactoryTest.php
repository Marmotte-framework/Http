<?php

namespace Marmotte\Http\Stream;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;

class StreamFactoryTest extends TestCase
{
    /** @psalm-suppress PropertyNotSetInConstructor */
    private StreamFactory $factory;

    protected function setUp(): void
    {
        $this->factory = new StreamFactory();
    }

    public function testItCreateStream(): void
    {
        $stream = $this->factory->createStream('hello');

        self::assertInstanceOf(StreamInterface::class, $stream);
        self::assertSame('hello', $stream->getContents());
    }

    public function testItCreateStreamFromFile(): void
    {
        $filename = __DIR__ . '/../Fixtures/test.txt';
        $stream = $this->factory->createStreamFromFile($filename);

        self::assertInstanceOf(StreamInterface::class, $stream);
        self::assertSame(file_get_contents($filename), $stream->getContents());
    }

    public function testItCreateStreamFromResource(): void
    {
        $file = fopen('php://temp', 'r+');
        fwrite($file, '42');
        fseek($file, 0);

        $stream = $this->factory->createStreamFromResource($file);

        self::assertInstanceOf(StreamInterface::class, $stream);
        self::assertSame('42', $stream->getContents());
    }
}
