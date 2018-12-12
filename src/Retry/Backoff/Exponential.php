<?php

namespace Ekomobile\Retry\Backoff;

/**
 *
 */
class Exponential implements BackoffInterface
{
    public const DEFAULT_TIMEOUT              = 15 * BackoffInterface::MINUTE;
    public const DEFAULT_MAX_INTERVAL         = 60 * BackoffInterface::SECOND;
    public const DEFAULT_INITIAL_INTERVAL     = 0.5 * BackoffInterface::SECOND;
    public const DEFAULT_RANDOMIZATION_FACTOR = 0.5;
    public const DEFAULT_MULTIPLIER           = 1.5;

    /** @var int microseconds */
    private $timeout = self::DEFAULT_TIMEOUT;

    /** @var int microseconds */
    private $maxInterval = self::DEFAULT_MAX_INTERVAL;

    /** @var float microseconds */
    private $initialInterval = self::DEFAULT_INITIAL_INTERVAL;

    /** @var float */
    private $randomizationFactor = self::DEFAULT_RANDOMIZATION_FACTOR;

    /** @var float */
    private $multiplier = self::DEFAULT_MULTIPLIER;

    /** @var int microseconds */
    private $currentInterval;

    /** @var float seconds */
    private $startTime;

    /**
     *
     */
    public function __construct()
    {
        $this->resetBackoff();
    }

    public function resetBackoff(): void
    {
        $this->currentInterval = $this->initialInterval;
        $this->startTime = $this->time();
    }

    public function nextBackoff(): int
    {
        if ($this->timeout != 0 && $this->getElapsedTime() > $this->timeout) {
            return BackoffInterface::STOP;
        }

        $backoffInterval = $this->getRandomizedInterval($this->randomizationFactor, $this->currentInterval);
        $this->incrementInterval();

        return $backoffInterval;
    }

    /**
     * @return int
     */
    public function getTimeout(): int
    {
        return $this->timeout;
    }

    /**
     * @param int $timeout
     *
     * @return self
     */
    public function setTimeout(int $timeout): self
    {
        $this->timeout = $timeout;

        return $this;
    }

    /**
     * @return int
     */
    public function getMaxInterval(): int
    {
        return $this->maxInterval;
    }

    /**
     * @param int $maxInterval
     *
     * @return self
     */
    public function setMaxInterval(int $maxInterval): self
    {
        $this->maxInterval = $maxInterval;

        return $this;
    }

    /**
     * @return float
     */
    public function getInitialInterval(): float
    {
        return $this->initialInterval;
    }

    /**
     * @param float $initialInterval
     *
     * @return self
     */
    public function setInitialInterval(float $initialInterval): self
    {
        $this->initialInterval = $initialInterval;

        return $this;
    }

    /**
     * @return float
     */
    public function getRandomizationFactor(): float
    {
        return $this->randomizationFactor;
    }

    /**
     * @param float $randomizationFactor
     *
     * @return self
     */
    public function setRandomizationFactor(float $randomizationFactor): self
    {
        $this->randomizationFactor = $randomizationFactor;

        return $this;
    }

    /**
     * @return float
     */
    public function getMultiplier(): float
    {
        return $this->multiplier;
    }

    /**
     * @param float $multiplier
     *
     * @return self
     */
    public function setMultiplier(float $multiplier): self
    {
        $this->multiplier = $multiplier;

        return $this;
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
