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

namespace Marmotte\Http\Response;

use PHPUnit\Framework\TestCase;

class ResponseFactoryTest extends TestCase
{
    /** @psalm-suppress PropertyNotSetInConstructor */
    private ResponseFactory $factory;

    protected function setUp(): void
    {
        $this->factory = new ResponseFactory();
    }

    public function testCreateResponse(): void
    {
        $response = $this->factory->createResponse();

        self::assertSame(200, $response->getStatusCode());
        self::assertSame('OK', $response->getReasonPhrase());
    }

    public function testNotFound(): void
    {
        $response = $this->factory->notFound();

        self::assertSame(404, $response->getStatusCode());
        self::assertSame('Not Found', $response->getReasonPhrase());
    }

    public function testServerError(): void
    {
        $response = $this->factory->serverError();

        self::assertSame(500, $response->getStatusCode());
        self::assertSame('Internal Server Error', $response->getReasonPhrase());
    }

    public function testMovedPermanently(): void
    {
        $response = $this->factory->movedPermanently('example.com');

        self::assertSame(301, $response->getStatusCode());
        self::assertSame('Moved Permanently', $response->getReasonPhrase());
        self::assertEqualsCanonicalizing(['example.com'], $response->getHeader('Location'));
    }

    public function testUnauthorized(): void
    {
        $response = $this->factory->unauthorized();

        self::assertSame(401, $response->getStatusCode());
        self::assertSame('Unauthorized', $response->getReasonPhrase());
    }

    public function testForbidden(): void
    {
        $response = $this->factory->forbidden();

        self::assertSame(403, $response->getStatusCode());
        self::assertSame('Forbidden', $response->getReasonPhrase());
    }
}
