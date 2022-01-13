# Generator

This module allows the creation of runtime implementations of PHP interfaces.

## How to use:

Create a new instance of `AnonymousClassGenerator` and call the method `instantiate`. You'll need to give an instance
of `MethodCallHandler` in order to handle the method calls, since all calls will be forwarded to this
`MethodCallHandler` object.