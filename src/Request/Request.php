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

namespace Marmotte\Http\Request;

use Marmotte\Brick\Services\Service;
use Marmotte\Http\Crate\FileCrate;
use Marmotte\Http\Crate\ParameterCrate;
use Marmotte\Http\Crate\ServerCrate;
use Marmotte\Http\Exceptions\FileNotFoundException;

/**
 * This class represent an HTTP request
 */
#[Service]
final class Request
{
    public const METHOD_GET     = 'GET';
    public const METHOD_POST    = 'POST';
    public const METHOD_PUT     = 'PUT';
    public const METHOD_PATCH   = 'PATCH';
    public const METHOD_DELETE  = 'DELETE';
    public const METHOD_OPTIONS = 'OPTIONS';
    public const METHOD_HEAD    = 'HEAD';
    public const METHOD_PURGE   = 'PURGE';
    public const METHOD_TRACE   = 'TRACE';
    public const METHOD_CONNECT = 'CONNECT';

    // _.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-.

    public readonly string         $method;
    public readonly ServerCrate    $server;
    public readonly ParameterCrate $headers;
    public readonly ParameterCrate $query;
    public readonly ParameterCrate $request;
    public readonly string         $body;
    public readonly ParameterCrate $cookies;
    public readonly FileCrate      $files;

    /**
     * @throws FileNotFoundException
     */
    public function __construct()
    {
        $this->method  = $_SERVER['REQUEST_METHOD'] ?? self::METHOD_GET;
        $this->server  = new ServerCrate($_SERVER);
        $this->headers = $this->server->getHeaders();
        /** @var array<string, mixed> $_GET */
        $this->query = new ParameterCrate($_GET);
        /** @var array<string, mixed> $_POST */
        $this->request = new ParameterCrate($_POST);
        $this->body    = file_get_contents('php://input') ?: '';
        $this->cookies = new ParameterCrate($_COOKIE);
        $this->files   = new FileCrate($_FILES);
    }
}
