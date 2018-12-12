<?php

namespace Ekomobile\Retry\Backoff;

/**
 *
 */
interface BackoffInterface
{
    /** @var int */
    public const STOP = -1;

    public const MICROSECOND = 1;
    public const SECOND      = 1000 * BackoffInterface::MILLISECOND;
    public const MINUTE      = 60 * BackoffInterface::SECOND;
    public const MILLISECOND = 1000 * BackoffInterface::MICROSECOND;

    /**
     *
     */
    public function resetBackoff(): void;

    /**
     * @return int microseconds
     */
    public function nextBackoff(): int;
}
