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

use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\StreamInterface;

final class Message implements MessageInterface
{
    /**
     * @var array<string, string>
     */
    private array $headers_map = [];

    /**
     * @param array<string, string[]> $headers
     */
    public function __construct(
        protected string          $version,
        protected array           $headers,
        protected StreamInterface $body,
    ) {
        foreach ($this->headers as $name => $_values) {
            $this->headers_map[strtoupper($name)] = $name;
        }
    }

    public function getProtocolVersion(): string
    {
        return $this->version;
    }

    public function withProtocolVersion(string $version): MessageInterface
    {
        $new          = clone $this;
        $new->version = $version;

        return $new;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function hasHeader(string $name): bool
    {
        return array_key_exists(strtoupper($name), $this->headers_map);
    }

    public function getHeader(string $name): array
    {
        if ($this->hasHeader($name)) {
            return $this->headers[$this->headers_map[strtoupper($name)]];
        }

        return [];
    }

    public function getHeaderLine(string $name): string
    {
        return implode(', ', $this->getHeader($name));
    }

    public function withHeader(string $name, $value): MessageInterface
    {
        $new                                 = clone $this;
        $new->headers_map[strtoupper($name)] = $name;
        if (is_array($value)) {
            $new->headers[$name] = $value;
        } else {
            $new->headers[$name] = [$value];
        }

        return $new;
    }

    public function withAddedHeader(string $name, $value): MessageInterface
    {
        $new                                 = clone $this;
        $new->headers_map[strtoupper($name)] = $name;
        if (is_array($value)) {
            $values = $value;
        } else {
            $values = [$value];
        }
        $values = [
            ...$new->getHeader($name),
            ...$values,
        ];

        $new->headers[$new->headers_map[strtoupper($name)]] = $values;

        return $new;
    }

    public function withoutHeader(string $name): MessageInterface
    {
        $new = clone $this;
        if ($new->hasHeader($name)) {
            unset($new->headers[$new->headers_map[strtoupper($name)]]);
            unset($new->headers_map[strtoupper($name)]);
        }

        return $new;
    }

    public function getBody(): StreamInterface
    {
        return $this->body;
    }

    public function withBody(StreamInterface $body): MessageInterface
    {
        $new       = clone $this;
        $new->body = $body;

        return $new;
    }
}
