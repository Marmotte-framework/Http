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

namespace Marmotte\Http\Uri;

use PHPUnit\Framework\TestCase;

class UriTest extends TestCase
{
    /** @psalm-suppress PropertyNotSetInConstructor */
    private UriFactory $factory;

    private const URI = 'http://username:password@hostname:9090/path?arg=value#anchor';

    protected function setUp(): void
    {
        $this->factory = new UriFactory();
    }

    public function test__toString(): void
    {
        self::assertSame(self::URI, (string) $this->factory->createUri(self::URI));
    }

    public function testWithFragment(): void
    {
        $uri = $this->factory->createUri('');
        $uri = $uri->withFragment('anchor');
        self::assertSame('anchor', $uri->getFragment());
    }

    public function testGetUserInfo(): void
    {
        $uri = $this->factory->createUri(self::URI);

        self::assertSame('username:password', $uri->getUserInfo());
    }

    public function testGetPort(): void
    {
        $uri = $this->factory->createUri(self::URI);

        self::assertSame(9090, $uri->getPort());
    }

    public function testWithPort(): void
    {
        $uri = $this->factory->createUri('');
        $uri = $uri->withPort(8080);
        self::assertSame(8080, $uri->getPort());
    }

    public function testGetQuery(): void
    {
        $uri = $this->factory->createUri(self::URI);

        self::assertSame('arg=value', $uri->getQuery());
    }

    public function testWithScheme(): void
    {
        $uri = $this->factory->createUri('');
        $uri = $uri->withScheme('https');
        self::assertSame('https', $uri->getScheme());
    }

    public function testWithHost(): void
    {
        $uri = $this->factory->createUri('');
        $uri = $uri->withHost('my-host');
        self::assertSame('my-host', $uri->getHost());
    }

    public function testWithUserInfo(): void
    {
        $uri = $this->factory->createUri('');
        $uri = $uri->withUserInfo('user', 'pass');
        self::assertSame('user:pass', $uri->getUserInfo());
    }

    public function testGetAuthority(): void
    {
        $uri = $this->factory->createUri(self::URI);

        self::assertSame('username:password@hostname:9090', $uri->getAuthority());
    }

    public function testWithQuery(): void
    {
        $uri = $this->factory->createUri('');
        $uri = $uri->withQuery('answer=42');
        self::assertSame('answer=42', $uri->getQuery());
    }

    public function testWithPath(): void
    {
        $uri = $this->factory->createUri('');
        $uri = $uri->withPath('/my/path');
        self::assertSame('/my/path', $uri->getPath());
    }

    public function testGetPath(): void
    {
        $uri = $this->factory->createUri(self::URI);

        self::assertSame('/path', $uri->getPath());
    }

    public function testGetHost(): void
    {
        $uri = $this->factory->createUri(self::URI);

        self::assertSame('hostname', $uri->getHost());
    }

    public function testGetFragment(): void
    {
        $uri = $this->factory->createUri(self::URI);

        self::assertSame('anchor', $uri->getFragment());
    }

    public function testGetScheme(): void
    {
        $uri = $this->factory->createUri(self::URI);

        self::assertSame('http', $uri->getScheme());
    }
}
