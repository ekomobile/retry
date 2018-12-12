<?php

namespace Ekomobile\Retry\Backoff;

/**
 *
 */
class WithMaxRetries implements BackoffInterface
{
    /** @var int */
    private $maxRetries;

    /** @var int */
    private $numRetries = 0;

    /** @var BackoffInterface */
    private $backoff;

    /**
     * @param BackoffInterface $backoff
     * @param int              $maxRetries
     */
    public function __construct(BackoffInterface $backoff, int $maxRetries)
    {
        if ($maxRetries <= 0) {
            throw new \InvalidArgumentException('$maxRetries must be greater than 0');
        }

        $this->backoff = $backoff;
        $this->maxRetries = $maxRetries;

        $this->resetBackoff();
    }

    /**
     *
     */
    public function resetBackoff(): void
    {
        $this->numRetries = 0;
        $this->backoff->resetBackoff();
    }

    /**
     * @return int
     */
    public function nextBackoff(): int
    {
        if ($this->numRetries >= $this->maxRetries) {
            return BackoffInterface::STOP;
        }

        $this->numRetries++;

        return $this->backoff->nextBackoff();
    }
}
