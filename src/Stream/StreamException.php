<?php

namespace Marmotte\Http\Stream;

final class StreamException extends \Exception
{
    public function __construct(string $msg)
    {
        parent::__construct($msg);
    }
}
