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

use Psr\Http\Message\UriInterface;

final class Uri implements UriInterface
{
    private string $scheme;
    private string $user;
    private string $password;
    private string $host;
    private ?int   $port;
    private string $path;
    private string $query;
    private string $fragment;

    /**
     * @throws UriException
     */
    public function __construct(string $uri)
    {
        $components = parse_url($uri);
        if (!$components) {
            throw new UriException(sprintf('Failed to parse uri : %s', $uri));
        }
        $this->scheme   = $components['scheme'] ?? '';
        $this->user     = $components['user'] ?? '';
        $this->password = $components['pass'] ?? '';
        $this->host     = $components['host'] ?? '';
        $this->port     = $components['port'] ?? null;
        $this->path     = $components['path'] ?? '';
        $this->query    = $components['query'] ?? '';
        $this->fragment = $components['fragment'] ?? '';
    }

    public function getScheme(): string
    {
        return $this->scheme;
    }

    public function getAuthority(): string
    {
        $result = $this->getUserInfo();
        if (empty($result)) {
            $result .= $this->getHost();
        } else {
            $result .= '@' . $this->getHost();
        }

        if ($port = $this->getPort()) {
            $result .= ':' . $port;
        }

        return $result;
    }

    public function getUserInfo(): string
    {
        $result = $this->user;
        if (!empty($this->password)) {
            $result .= ':' . $this->password;
        }

        return $result;
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function getPort(): ?int
    {
        if (($this->getScheme() === 'http' && $this->port === 80) ||
            ($this->getScheme() === 'https' && $this->port === 443)) {
            return null;
        }

        return $this->port;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getQuery(): string
    {
        return $this->query;
    }

    public function getFragment(): string
    {
        return $this->fragment;
    }

    public function withScheme(string $scheme): UriInterface
    {
        $new         = clone $this;
        $new->scheme = $scheme;

        return $new;
    }

    public function withUserInfo(string $user, ?string $password = null): UriInterface
    {
        $new           = clone $this;
        $new->user     = $user;
        $new->password = $password ?? '';

        return $new;
    }

    public function withHost(string $host): UriInterface
    {
        $new       = clone $this;
        $new->host = $host;

        return $new;
    }

    public function withPort(?int $port): UriInterface
    {
        $new       = clone $this;
        $new->port = $port;

        return $new;
    }

    public function withPath(string $path): UriInterface
    {
        $new       = clone $this;
        $new->path = $path;

        return $new;
    }

    public function withQuery(string $query): UriInterface
    {
        $new        = clone $this;
        $new->query = $query;

        return $new;
    }

    public function withFragment(string $fragment): UriInterface
    {
        $new           = clone $this;
        $new->fragment = $fragment;

        return $new;
    }

    public function __toString(): string
    {
        $result = '';
        if (!empty($scheme = $this->getScheme())) {
            $result .= $scheme . ':';
        }
        if (!empty($authority = $this->getAuthority())) {
            $result .= '//' . $authority;
        }
        if (!empty($path = $this->getPath())) {
            if (!str_starts_with($path, '/') && !empty($authority)) {
                $result .= '/' . $path;
            } else if (str_starts_with($path, '/') && empty($authority)) {
                $result .= '/' . ltrim($path, '/');
            } else {
                $result .= $path;
            }
        }
        if (!empty($query = $this->getQuery())) {
            $result .= '?' . $query;
        }
        if (!empty($fragment = $this->getFragment())) {
            $result .= '#' . $fragment;
        }

        return $result;
    }
}
