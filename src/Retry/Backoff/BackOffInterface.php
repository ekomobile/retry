<?php

namespace Ekomobile\Retry\Backoff;

/**
 *
 */
interface BackOffInterface
{
    /** @var int */
    public const STOP = -1;

    public const MICROSECOND = 1;
    public const SECOND      = 1000 * BackOffInterface::MILLISECOND;
    public const MINUTE      = 60 * BackOffInterface::SECOND;
    public const MILLISECOND = 1000 * BackOffInterface::MICROSECOND;

    /**
     *
     */
    public function resetBackOff(): void;

    /**
     * @return int microseconds
     */
    public function nextBackOff(): int;
}
