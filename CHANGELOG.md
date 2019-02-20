# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.0-alpha10] - 2019-02-20
 ### Fix
 - Fixing exception message generating in `ResponseError`.

## [1.0.0-alpha9] - 2019-02-19
 ### Fix
 - Resolving recursivity problem in the `Response` and `ResponseError` class.

## [1.0.0-alpha8] - 2019-02-18
 ### Add
 - `ResponseError` class now contains the occurred exception.
 - `BadGatewayHttpException`
 ### Fix
 - URL encoding fix.
 ### Change
 - **[BC BREAK]** `HttpException` `newInstance` function will return with the exception object, not throwing it.

## [1.0.0-alpha7] - 2019-02-14
 ### Add
 - `shardimage\shardimagephpapi\services\Client` has `timeout` property from now.

## [1.0.0-alpha6] - 2019-02-13
 ### Fix
 - Empty string can't be in URI params.

## [1.0.0-alpha5] - 2019-02-13
 ### Fix
 - Fixing Response and ResponseError conversions to array.

 ### Change
 - BaseObject class `toArray` function recieve class attributes from `getToArrayAttributes` function, so it can be expanded by custom attributes, like private attributes with magic methodes.
 - More details in case of "Unsupported content type" response.

## [1.0.0-alpha4] - 2019-02-12
 ### Change
 - `shardimage\shardimagephpapi\api\ResponseError` handle error message as array.

## [1.0.0-alpha3] - 2019-02-12
 ### Change
 - **[BC BREAK]** Response errors are handled through `shardimage\shardimagephpapi\api\ResponseError` object instead of array.

 ### Added
 - Lincence file

## [1.0.0-alpha2] - 2018-12-10
 - Changed `shardimage\shardimagephpapi\api\Request` `$mode` variable default value to "sync/parallel"

## [1.0.0-alpha1] - 2018-11-12
 - Initial release
