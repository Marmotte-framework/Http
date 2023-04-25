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

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Message\UriInterface;

final class ServerRequest extends Request implements ServerRequestInterface
{
    /**
     * @param UploadedFileInterface[] $uploaded_files
     * @param array<string, string[]> $headers
     */
    public function __construct(
        private readonly array    $server_params,
        private array             $cookie_params,
        private array             $query_params,
        private array             $uploaded_files,
        private array|object|null $parsed_body,
        private array             $attributes,
        string                    $request_target,
        string                    $method,
        UriInterface              $uri,
        string                    $version,
        array                     $headers,
        StreamInterface           $body
    ) {
        parent::__construct($request_target, $method, $uri, $version, $headers, $body);
    }

    public function getServerParams(): array
    {
        return $this->server_params;
    }

    public function getCookieParams(): array
    {
        return $this->cookie_params;
    }

    public function withCookieParams(array $cookies): ServerRequestInterface
    {
        $new                = clone $this;
        $new->cookie_params = $cookies;

        return $new;
    }

    public function getQueryParams(): array
    {
        return $this->query_params;
    }

    public function withQueryParams(array $query): ServerRequestInterface
    {
        $new               = clone $this;
        $new->query_params = $query;

        return $new;
    }

    /**
     * @return UploadedFileInterface[]
     */
    public function getUploadedFiles(): array
    {
        return $this->uploaded_files;
    }

    /**
     * @psalm-suppress MoreSpecificImplementedParamType,LessSpecificImplementedReturnType
     *
     * @param UploadedFileInterface[] $uploadedFiles
     */
    public function withUploadedFiles(array $uploadedFiles): ServerRequestInterface
    {
        $new                 = clone $this;
        $new->uploaded_files = $uploadedFiles;

        return $new;
    }

    public function getParsedBody(): array|object|null
    {
        return $this->parsed_body;
    }

    public function withParsedBody($data): ServerRequestInterface
    {
        $new              = clone $this;
        $new->parsed_body = $data;

        return $new;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function getAttribute(string $name, $default = null)
    {
        if (!array_key_exists($name, $this->attributes)) {
            return $default;
        }

        return $this->attributes[$name];
    }

    public function withAttribute(string $name, $value): ServerRequestInterface
    {
        $new                    = clone $this;
        $new->attributes[$name] = $value;

        return $new;
    }

    public function withoutAttribute(string $name): ServerRequestInterface
    {
        if (!array_key_exists($name, $this->attributes)) {
            return $this;
        }

        $new = clone $this;
        unset($new->attributes[$name]);

        return $new;
    }
}
