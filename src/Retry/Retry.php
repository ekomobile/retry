<?php

namespace Ekomobile\Retry;

use Ekomobile\Retry\Backoff\BackoffInterface;
use Ekomobile\Retry\Backoff\Exponential;
use Ekomobile\Retry\Exception\Permanent;

/**
 *
 */
class Retry
{
    /** @var callable */
    private $operation;

    /** @var BackoffInterface */
    private $backoff;

    /** @var callable|null */
    private $notifyVisitor;

    /**
     * @param callable              $operation
     * @param BackoffInterface|null $backoff
     * @param callable|null         $notifyVisitor
     */
    public function __construct(callable $operation, BackoffInterface $backoff = null, callable $notifyVisitor = null)
    {
        $this->operation = $operation;
        $this->backoff = $backoff ?? new Exponential();
        $this->notifyVisitor = $notifyVisitor;
    }

    /**
     * @throws \Throwable
     */
    public function __invoke()
    {
        $this->backoff->resetBackoff();

        while (true) {
            try {
                ($this->operation)();
                break;

            } catch (Permanent $e) {
                throw $e->getPrevious();

            } catch (\Throwable $e) {
                $backoffDuration = $this->backoff->nextBackoff();
                if ($backoffDuration == BackoffInterface::STOP) {
                    throw $e;
                }

                $this->notify($e);
                \usleep($backoffDuration);
            }
        }
    }

    /**
     * @param \Throwable $e
     */
    private function notify(\Throwable $e): void
    {
        if ($this->notifyVisitor) {
            ($this->notifyVisitor)($e);
        }
    }
}
