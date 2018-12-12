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
    private $numRetries;

    /** @var BackoffInterface */
    private $backoff;

    /**
     * @param BackoffInterface $backoff
     * @param int              $maxRetries
     */
    public function __construct(BackoffInterface $backoff, int $maxRetries)
    {
        $this->backoff = $backoff;
        $this->maxRetries = $maxRetries;

        $this->resetBackoff();
    }

    /**
     *
     */
    public function resetBackoff(): void
    {
        $this->numRetries = $this->maxRetries;
        $this->backoff->resetBackoff();
    }

    /**
     * @return int
     */
    public function nextBackoff(): int
    {
        if ($this->maxRetries > 0) {
            if ($this->numRetries >= $this->maxRetries) {
                return BackoffInterface::STOP;
            }
            $this->numRetries++;
        }

        return $this->backoff->nextBackoff();
    }
}
