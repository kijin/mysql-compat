MySQL Compat
============

This library provides functions to emulate the infamous and deprecated `mysql` extension,
using the modern `mysqli` extension behind the scenes.

The purpose is to allow legacy applications using the `mysql` extension
to function adequately in PHP 7.0 and beyond,
where the `mysql` extension will not be available.

All functions are designed to behave as similarly as possible
to the way the original `mysql` extension does.
There may be minor differences, however, in unusual cases.
Please report any discrepancy that you find.

You can also access all advanced `mysqli` features,
such as transactions and prepared statements,
by calling the corresponding methods directly on the connection object
(which is an instance of the `mysqli` class).

### Disclaimer

This library should not be taken as a justification
for writing new applications using the `mysql` extension.
Legacy applications using the `mysql` extension
should be ported to `mysqli` or `PDO` as soon as possible.

Please feel free to consult the inner workings of this library
to learn how to port `mysql` code to `mysqli` code that behaves in the same way.

### License

This library is released under the MIT license.
