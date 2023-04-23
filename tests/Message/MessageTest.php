<?php
/**
 * MIT License
 *
 * Copyright (c) 2023-Present Kevin Traini
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

declare(strict_types=1);

namespace Marmotte\Http\Message;

use Marmotte\Http\Stream\StreamFactory;
use PHPUnit\Framework\TestCase;

class MessageTest extends TestCase
{
    /** @psalm-suppress PropertyNotSetInConstructor */
    private StreamFactory $stream_factory;

    protected function setUp(): void
    {
        $this->stream_factory = new StreamFactory();
    }

    public function testWithProtocolVersion(): void
    {
        $msg = new Message(
            '1.1',
            [],
            $this->stream_factory->createStream('')
        );

        $msg2 = $msg->withProtocolVersion('1.0');

        self::assertSame('1.1', $msg->getProtocolVersion());
        self::assertSame('1.0', $msg2->getProtocolVersion());
    }

    public function testWithoutHeader(): void
    {
        $msg = new Message(
            '1.1',
            [
                'header' => [],
            ],
            $this->stream_factory->createStream('')
        );

        $msg2 = $msg->withoutHeader('HEADeR');

        self::assertTrue($msg->hasHeader('header'));
        self::assertFalse($msg2->hasHeader('header'));
    }

    public function testHasHeader(): void
    {
        $msg = new Message(
            '1.1',
            [
                'header' => [],
            ],
            $this->stream_factory->createStream('')
        );

        self::assertTrue($msg->hasHeader('header'));
        self::assertTrue($msg->hasHeader('HeaDer'));
        self::assertFalse($msg->hasHeader('gbleskefe'));
    }

    public function testGetHeader(): void
    {
        $msg = new Message(
            '1.1',
            [
                'header' => ['arg=value'],
            ],
            $this->stream_factory->createStream('')
        );

        $header = $msg->getHeader('HEADER');
        self::assertCount(1, $header);
        self::assertSame('arg=value', $header[0]);
    }

    public function testGetProtocolVersion(): void
    {
        $msg = new Message(
            '1.1',
            [
                'header' => [],
            ],
            $this->stream_factory->createStream('')
        );

        self::assertSame('1.1', $msg->getProtocolVersion());
    }

    public function testWithAddedHeader(): void
    {
        $msg = new Message(
            '1.1',
            [
                'header' => ['arg=value'],
            ],
            $this->stream_factory->createStream('')
        );

        $msg2 = $msg->withAddedHeader('header', 'truth');

        self::assertCount(1, $msg->getHeader('header'));
        self::assertCount(2, $msg2->getHeader('header'));
    }

    public function testWithBody(): void
    {
        $msg = new Message(
            '1.1',
            [
                'header' => [],
            ],
            $this->stream_factory->createStream('12')
        );

        $msg2 = $msg->withBody($this->stream_factory->createStream('21'));

        self::assertSame('12', $msg->getBody()->getContents());
        self::assertSame('21', $msg2->getBody()->getContents());
    }

    public function testGetBody(): void
    {
        $msg = new Message(
            '1.1',
            [
                'header' => [],
            ],
            $this->stream_factory->createStream('12')
        );

        self::assertSame('12', $msg->getBody()->getContents());
    }

    public function testGetHeaders(): void
    {
        $msg = new Message(
            '1.1',
            [
                'header' => ['arg=value'],
            ],
            $this->stream_factory->createStream('')
        );

        $headers = $msg->getHeaders();
        self::assertCount(1, $headers);
        self::assertArrayHasKey('header', $headers);
        self::assertEqualsCanonicalizing(['arg=value'], $headers['header']);
    }

    public function testWithHeader(): void
    {
        $msg = new Message(
            '1.1',
            [
                'header' => [],
            ],
            $this->stream_factory->createStream('')
        );

        $msg2 = $msg->withHeader('header2', 'value');

        self::assertCount(1, $msg->getHeaders());
        $headers = $msg2->getHeaders();
        self::assertCount(2, $headers);
        self::assertArrayHasKey('header', $headers);
        self::assertArrayHasKey('header2', $headers);
        self::assertEqualsCanonicalizing(['value'], $headers['header2']);
    }

    public function testGetHeaderLine(): void
    {
        $msg = new Message(
            '1.1',
            [
                'header' => ['value1', 'value2'],
            ],
            $this->stream_factory->createStream('')
        );

        self::assertSame('value1, value2', $msg->getHeaderLine('header'));
    }
}
