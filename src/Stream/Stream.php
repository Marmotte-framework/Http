<?php

namespace Marmotte\Http\Stream;

use Psr\Http\Message\StreamInterface;

final class Stream implements StreamInterface
{
    private string $filename;
    private string $mode;
    private bool   $readable = false;
    private bool   $writable = false;
    private bool   $seekable = false;
    private ?int   $size;

    /**
     * @param resource|closed-resource $stream
     * @throws StreamException
     */
    public function __construct(
        private mixed $stream,
    ) {
        /** @var string */
        $this->filename = $this->getMetadata('uri');
        /** @var string */
        $this->mode = $this->getMetadata('mode');

        switch (true) {
            case str_starts_with($this->mode, 'r'):
                $this->readable = true;
                break;
            case str_starts_with($this->mode, 'r+'):
            case str_starts_with($this->mode, 'w+'):
            case str_starts_with($this->mode, 'a+'):
            case str_starts_with($this->mode, 'x+'):
            case str_starts_with($this->mode, 'c+'):
                $this->readable = true;
                $this->writable = true;
                break;
            case str_starts_with($this->mode, 'w'):
            case str_starts_with($this->mode, 'a'):
            case str_starts_with($this->mode, 'x'):
            case str_starts_with($this->mode, 'c'):
                $this->writable = true;
                break;

            default:
                throw new StreamException(sprintf('Unknown mode: %s', $this->mode));
        }
        /** @var bool */
        $this->seekable = $this->getMetadata('seekable');

        if ($this->filename === 'php://temp') {
            $this->size = null;
        } else {
            $this->size = filesize($this->filename) ?: null;
        }
    }

    /**
     * @throws StreamException
     */
    public function __toString(): string
    {
        if ($this->isSeekable() && $this->isReadable()) {
            $this->rewind();

            return $this->getContents();
        }

        throw new StreamException('Stream is not readable nor seekable');
    }

    /**
     * @throws StreamException
     */
    public function close(): void
    {
        if (!is_resource($this->stream)) {
            throw new StreamException('Stream already closed');
        }

        if (!fclose($this->stream)) {
            throw new StreamException('Failed to close stream');
        }
        $this->readable = $this->writable = $this->seekable = false;
    }

    /**
     * @return resource
     * @throws StreamException
     */
    public function detach()
    {
        if (!is_resource($this->stream)) {
            throw new StreamException('Stream cannot be detached');
        }

        $this->close();

        return fopen($this->filename, $this->mode);
    }

    public function getSize(): ?int
    {
        return $this->size;
    }

    /**
     * @throws StreamException
     */
    public function tell(): int
    {
        if (!is_resource($this->stream)) {
            throw new StreamException('Stream closed');
        }

        return ftell($this->stream);
    }

    /**
     * @throws StreamException
     */
    public function eof(): bool
    {
        if (!is_resource($this->stream)) {
            throw new StreamException('Stream closed');
        }

        return feof($this->stream);
    }

    public function isSeekable(): bool
    {
        return $this->seekable;
    }

    /**
     * @throws StreamException
     */
    public function seek(int $offset, int $whence = SEEK_SET): void
    {
        if (!$this->isSeekable() || !is_resource($this->stream)) {
            throw new StreamException('Stream is not seekable');
        }

        fseek($this->stream, $offset, $whence);
    }

    /**
     * @throws StreamException
     */
    public function rewind(): void
    {
        $this->seek(0);
    }

    public function isWritable(): bool
    {
        return $this->writable;
    }

    /**
     * @throws StreamException
     */
    public function write(string $string): int
    {
        if (!is_resource($this->stream)) {
            throw new StreamException('Stream cannot be written');
        }

        if (!$this->isWritable()) {
            return 0;
        }

        if ($result = fwrite($this->stream, $string)) {
            return $result;
        }

        return 0;
    }

    public function isReadable(): bool
    {
        return $this->readable;
    }

    /**
     * @throws StreamException
     */
    public function read(int $length): string
    {
        if (!is_resource($this->stream) || !$this->isReadable()) {
            throw new StreamException('Stream cannot be read');
        }

        return fread($this->stream, $length);
    }

    /**
     * @throws StreamException
     */
    public function getContents(): string
    {
        $result = '';

        while (!$this->eof()) {
            $result .= $this->read(1024);
        }

        return $result;
    }

    /**
     * @throws StreamException
     */
    public function getMetadata(?string $key = null)
    {
        if (!is_resource($this->stream)) {
            throw new StreamException('Cannot read metadata of stream');
        }

        $metadata = stream_get_meta_data($this->stream);

        return $key == null ? $metadata : $metadata[$key];
    }
}
