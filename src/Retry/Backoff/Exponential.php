<?php

namespace Ekomobile\Retry\Backoff;

/**
 *
 */
class Exponential implements BackOffInterface
{
    public const DEFAULT_TIMEOUT              = 15 * BackOffInterface::MINUTE;
    public const DEFAULT_MAX_INTERVAL         = 60 * BackOffInterface::SECOND;
    public const DEFAULT_INITIAL_INTERVAL     = 0.5 * BackOffInterface::SECOND;
    public const DEFAULT_RANDOMIZATION_FACTOR = 0.5;
    public const DEFAULT_MULTIPLIER           = 1.5;

    /** @var int microseconds */
    private $timeout;

    /** @var int microseconds */
    private $maxInterval;

    /** @var float microseconds */
    private $initialInterval;

    /** @var float */
    private $randomizationFactor;

    /** @var float */
    private $multiplier;

    /** @var int microseconds */
    private $currentInterval;

    /** @var float seconds */
    private $startTime;

    /**
     * @param int   $timeout
     * @param int   $maxInterval
     * @param int   $initialInterval
     * @param float $randomizationFactor
     * @param float $multiplier
     */
    public function __construct(
        int $timeout = self::DEFAULT_TIMEOUT,
        int $maxInterval = self::DEFAULT_MAX_INTERVAL,
        int $initialInterval = self::DEFAULT_INITIAL_INTERVAL,
        float $randomizationFactor = self::DEFAULT_RANDOMIZATION_FACTOR,
        float $multiplier = self::DEFAULT_MULTIPLIER
    )
    {
        $this->timeout = $timeout;
        $this->maxInterval = $maxInterval;
        $this->initialInterval = $initialInterval;
        $this->randomizationFactor = $randomizationFactor;
        $this->multiplier = $multiplier;

        $this->resetBackOff();
    }

    public function resetBackOff(): void
    {
        $this->currentInterval = $this->initialInterval;
        $this->startTime = $this->time();
    }

    public function nextBackOff(): int
    {
        if ($this->timeout != 0 && $this->getElapsedTime() > $this->timeout) {
            return BackOffInterface::STOP;
        }

        $backOffInterval = $this->getRandomizedInterval($this->randomizationFactor, $this->currentInterval);
        $this->incrementInterval();

        return $backOffInterval;
    }

    /**
     * @return float seconds
     */
    private function time(): float
    {
        return \microtime(true);
    }

    /**
     * @return float seconds
     */
    private function getElapsedTime(): float
    {
        return $this->time() - $this->startTime;
    }

    /**
     *
     */
    private function incrementInterval(): void
    {
        if ($this->currentInterval >= $this->maxInterval / $this->multiplier) {
            $this->currentInterval = $this->maxInterval;
        } else {
            $this->currentInterval = round($this->currentInterval * $this->multiplier);
        }
    }

    /**
     * @param float $randomizationFactor
     * @param int   $currentInterval
     *
     * @return int
     */
    private function getRandomizedInterval(float $randomizationFactor, int $currentInterval): int
    {
        $random = $this->randomNumber();
        $delta = $randomizationFactor * $this->currentInterval;
        $minInterval = $currentInterval - $delta;
        $maxInterval = $currentInterval + $delta;

        return round($minInterval + $random * ($maxInterval - $minInterval + 1));
    }

    /**
     * @return float random [0, 1)
     */
    private function randomNumber(): float
    {
        return mt_rand(0, mt_getrandmax() - 1) / mt_getrandmax();
    }
}
