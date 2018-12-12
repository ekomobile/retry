# Exponential Backoff

[![Build Status][travis image]][travis]
[![GitHub release][release image]][release]
[![Downloads][downloads image]][downloads]
[![Coverage][codeclimate coverage image]][codeclimate]
[![Maintainability][codeclimate maintainability image]][codeclimate]
[![Tech debt][codeclimate tech debt image]][codeclimate]

This is a PHP port of https://github.com/cenkalti/backoff
(thanks, @cenkalti), which is a port of the exponential backoff algorithm
from [Google's HTTP Client Library for Java][google-http-java-client].

[Exponential backoff][exponential backoff wiki]
is an algorithm that uses feedback to multiplicatively decrease the rate of some process,
in order to gradually find an acceptable rate.
The retries exponentially increase and stop increasing when a certain threshold is met.

[travis]: https://travis-ci.org/ekomobile/retry
[travis image]: https://travis-ci.org/ekomobile/retry.svg

[release]: https://github.com/ekomobile/retry/releases
[release image]: https://img.shields.io/github/release/ekomobile/retry.svg

[downloads]: https://packagist.org/packages/ekomobile/retry
[downloads image]: https://img.shields.io/packagist/dt/ekomobile/retry.svg

[codeclimate]: https://codeclimate.com/github/ekomobile/retry
[codeclimate coverage image]: https://img.shields.io/codeclimate/coverage/ekomobile/retry.svg
[codeclimate maintainability image]: https://img.shields.io/codeclimate/maintainability-percentage/ekomobile/retry.svg
[codeclimate tech debt image]: https://img.shields.io/codeclimate/tech-debt/ekomobile/retry.svg

[google-http-java-client]: https://github.com/google/google-http-java-client/blob/da1aa993e90285ec18579f1553339b00e19b3ab5/google-http-client/src/main/java/com/google/api/client/util/ExponentialBackOff.java
[exponential backoff wiki]: http://en.wikipedia.org/wiki/Exponential_backoff

## Examples

### Simple
Retry with default exponential backoff.
```php
(new Retry(function () {
    // workload ...
}))();

```

### Advanced

```php
$operation = function () {
  // workload ...
  if ($somePermanentFailCondition) {
    throw new \Ekomobile\Retry\Exception\Permanent(new \Exception('Unretryable error'))
  }
  // ...
  throw new new Exception('Retryable error')
};

$backoff = new \Ekomobile\Retry\Backoff\WithMaxRetries(new \Ekomobile\Retry\Backoff\Exponential(), 5);

$notify = function (\Throwable $e) {
  // $logger->log($e);
};

$retry = new \Ekomobile\Retry\Retry($operation, $backoff, $notify);
$retry();
```
