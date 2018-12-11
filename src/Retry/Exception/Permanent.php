<?php

namespace Ekomobile\Retry\Exception;

/**
 *
 */
class Permanent extends \Exception
{
    public function __construct(\Throwable $previous)
    {
        parent::__construct('', 0, $previous);
    }
}
