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

use Marmotte\Http\Message\Message;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

class Request extends Message implements RequestInterface
{
    // Methods from RFC2616
    public const METHOD_OPTIONS = 'OPTIONS';
    public const METHOD_GET     = 'GET';
    public const METHOD_HEAD    = 'HEAD';
    public const METHOD_POST    = 'POST';
    public const METHOD_PUT     = 'PUT';
    public const METHOD_DELETE  = 'DELETE';
    public const METHOD_TRACE   = 'TRACE';
    public const METHOD_CONNECT = 'CONNECT';

    /**
     * @param array<string, string[]> $headers
     */
    public function __construct(
        protected string       $request_target,
        protected string       $method,
        protected UriInterface $uri,
        string                 $version,
        array                  $headers,
        StreamInterface        $body,
    ) {
        parent::__construct($version, $headers, $body);
    }

    public function getRequestTarget(): string
    {
        return $this->request_target;
    }

    public function withRequestTarget(string $requestTarget): RequestInterface
    {
        $new                 = clone $this;
        $new->request_target = $requestTarget;

        return $new;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function withMethod(string $method): RequestInterface
    {
        $new         = clone $this;
        $new->method = $method;

        return $new;
    }

    public function getUri(): UriInterface
    {
        return $this->uri;
    }

    /** @psalm-suppress MoreSpecificReturnType */
    public function withUri(UriInterface $uri, bool $preserveHost = false): RequestInterface
    {
        $new      = clone $this;
        $new->uri = $uri;

        if ($preserveHost) {
            if ((!$new->hasHeader('Host') || empty($new->getHeader('Host'))) &&
                !empty($host = $uri->getHost())) {
                /** @psalm-suppress LessSpecificReturnStatement */
                return $new->withHeader('Host', $host);
            }
        } else if (!empty($host = $uri->getHost())) {
            /** @psalm-suppress LessSpecificReturnStatement */
            return $new->withHeader('Host', $host);
        }

        return $new;
    }
}
