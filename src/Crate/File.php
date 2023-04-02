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

use Marmotte\Http\Exceptions\FileException;
use Marmotte\Http\Exceptions\FileNotFoundException;
use SplFileInfo;

class File extends SplFileInfo
{
    private string $name;
    private string $mime_type;
    private int    $size;
    private string $location;
    private int    $error;

    /**
     * @param array{
     *     name: string,
     *     type: string,
     *     size: int,
     *     tmp_name: string,
     *     error: int,
     * } $data
     * @throws FileNotFoundException
     */
    public function __construct(array $data)
    {
        $this->name      = $data['name'];
        $this->mime_type = $data['type'];
        $this->size      = $data['size'];
        $this->location  = $data['tmp_name'];
        $this->error     = $data['error'];

        parent::__construct($this->location);

        if ($this->error === UPLOAD_ERR_OK && !file_exists($this->location)) {
            throw new FileNotFoundException($this->name);
        }
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getMimeType(): string
    {
        return $this->mime_type;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function getLocation(): string
    {
        return $this->location;
    }

    public function getError(): int
    {
        return $this->error;
    }

    public function getContent(): string
    {
        return file_get_contents($this->location);
    }

    /**
     * @throws FileNotFoundException
     * @throws FileException
     */
    public function move(string $destination, string $name): File
    {
        if (move_uploaded_file($this->location, $destination . '/' . $name)) {
            return new File([
                'name'     => $name,
                'type'     => $this->mime_type,
                'size'     => $this->size,
                'tmp_name' => $destination,
                'error'    => UPLOAD_ERR_OK,
            ]);
        } else {
            throw new FileException("Fail to move uploaded file to $destination/$name");
        }
    }
}
