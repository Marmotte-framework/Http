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

namespace Marmotte\Http\Crate;

use Marmotte\Http\Exceptions\FileNotFoundException;

class FileCrate extends ParameterCrate
{
    /**
     * @throws FileNotFoundException
     */
    public function __construct(array $files)
    {
        $res = [];
        /** @var string $input_name
         * @var array{
         *     name: string,
         *     type: string,
         *     size: int,
         *     tmp_name: string,
         *     error: int,
         * } $data
         */
        foreach ($files as $input_name => $data) {
            $res[$input_name] = new File($data);
        }

        parent::__construct($res);
    }

    /**
     * @return File|mixed
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return parent::get($key, $default);
    }
}
