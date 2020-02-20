<?php

namespace App\Exceptions;

use App\Utils\Code;

class UploadException extends \Exception
{

    public static function throwUploadFailedException($msg)
    {
        throw new self($msg, Code::UPLOAD_FAILED);
    }
}