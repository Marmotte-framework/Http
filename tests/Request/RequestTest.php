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

namespace Marmotte\Http\Request;

use Marmotte\Http\Uri\UriFactory;
use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase
{
    /** @psalm-suppress PropertyNotSetInConstructor */
    private RequestFactory $factory;

    protected function setUp(): void
    {
        $this->factory = new RequestFactory();
    }

    public function testWithMethod(): void
    {
        $request = $this->factory->createRequest(Request::METHOD_GET, 'example.com');

        $request2 = $request->withMethod(Request::METHOD_POST);

        self::assertSame(Request::METHOD_GET, $request->getMethod());
        self::assertSame(Request::METHOD_POST, $request2->getMethod());
    }

    public function testWithRequestTarget(): void
    {
        $request = $this->factory->createRequest(Request::METHOD_GET, 'example.com');

        $request2 = $request->withRequestTarget('target');

        self::assertSame('', $request->getRequestTarget());
        self::assertSame('target', $request2->getRequestTarget());
    }

    public function testGetUri(): void
    {
        $request = $this->factory->createRequest(Request::METHOD_GET, 'example.com');

        self::assertSame('example.com', (string) $request->getUri());
    }

    public function testWithUri(): void
    {
        $request = $this->factory->createRequest(Request::METHOD_GET, 'https://example.com');

        $request2 = $request->withUri((new UriFactory())->createUri('https://my.example.com'));

        self::assertSame('https://example.com', (string) $request->getUri());
        self::assertSame('https://my.example.com', (string) $request2->getUri());
        self::assertSame('my.example.com', $request2->getHeaderLine('host'));
    }
}
