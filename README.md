# Exponential Backoff

[![Build Status][travis image]][travis]
[![GitHub release][release image]][release]

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

[google-http-java-client]: https://github.com/google/google-http-java-client/blob/da1aa993e90285ec18579f1553339b00e19b3ab5/google-http-client/src/main/java/com/google/api/client/util/ExponentialBackOff.java
[exponential backoff wiki]: http://en.wikipedia.org/wiki/Exponential_backoff
