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

use Marmotte\Http\File\UploadedFileFactory;
use Marmotte\Http\Stream\StreamFactory;
use PHPUnit\Framework\TestCase;

class ServerRequestTest extends TestCase
{
    /** @psalm-suppress PropertyNotSetInConstructor */
    private RequestFactory $factory;

    protected function setUp(): void
    {
        $this->factory = new RequestFactory();
    }

    public function testGetAttributes(): void
    {
        $request = $this->factory->createServerRequest(ServerRequest::METHOD_GET, 'example.com')
            ->withAttribute('arg', 'value')
            ->withAttribute('answer', 42);

        $attributes = $request->getAttributes();
        self::assertCount(2, $attributes);
        self::assertEqualsCanonicalizing([
            'arg'    => 'value',
            'answer' => 42,
        ], $attributes);
    }

    public function testWithoutAttribute(): void
    {
        $request = $this->factory->createServerRequest(ServerRequest::METHOD_GET, 'example.com')
            ->withAttribute('arg', 'value')
            ->withAttribute('answer', 42)
            ->withoutAttribute('arg');

        $attributes = $request->getAttributes();
        self::assertCount(1, $attributes);
        self::assertEqualsCanonicalizing([
            'answer' => 42,
        ], $attributes);
    }

    public function testGetUploadedFiles(): void
    {
        $request = $this->factory->createServerRequest(ServerRequest::METHOD_GET, 'example.com');

        self::assertCount(0, $request->getUploadedFiles());
    }

    public function testGetAttribute(): void
    {
        $request = $this->factory->createServerRequest(ServerRequest::METHOD_GET, 'example.com')
            ->withAttribute('arg', 'value')
            ->withAttribute('answer', 42);

        self::assertSame(42, $request->getAttribute('answer'));
    }

    public function testWithParsedBody(): void
    {
        $parsed_body = ['hello' => 'world!'];
        $request     = $this->factory->createServerRequest(ServerRequest::METHOD_GET, 'example.com')
            ->withParsedBody($parsed_body);

        self::assertEqualsCanonicalizing($parsed_body, $request->getParsedBody());
    }

    public function testWithUploadedFiles(): void
    {
        $request = $this->factory->createServerRequest(ServerRequest::METHOD_GET, 'example.com')
            ->withUploadedFiles(['name' => (new UploadedFileFactory())->createUploadedFile((new StreamFactory())->createStream())]);

        self::assertCount(1, $request->getUploadedFiles());
    }

    public function testWithCookieParams(): void
    {
        $cookies = ['session_id' => 'gbleskefe'];
        $request = $this->factory->createServerRequest(ServerRequest::METHOD_GET, 'example.com')
            ->withCookieParams($cookies);

        self::assertEqualsCanonicalizing($cookies, $request->getCookieParams());
    }

    public function testGetServerParams(): void
    {
        $request = $this->factory->createServerRequest(ServerRequest::METHOD_GET, 'example.com', [
            'OS' => 'Linux',
        ]);

        self::assertEqualsCanonicalizing(['OS' => 'Linux'], $request->getServerParams());
    }

    public function testWithQueryParams(): void
    {
        $query   = ['arg' => 'value'];
        $request = $this->factory->createServerRequest(ServerRequest::METHOD_GET, 'example.com')
            ->withQueryParams($query);

        self::assertEqualsCanonicalizing($query, $request->getQueryParams());
    }
}
