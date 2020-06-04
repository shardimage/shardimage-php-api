# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.0-alph31] - 2020-06-04
 ### Change
 - Changed response decoding process, added more checks and validation before decode.

## [1.0.0-alph30] - 2020-05-19
 ### Change
 - `HttpException` `$contentId` variable is private from now.

## [1.0.0-alpha29] - 2020-05-13
 ### Change
 - Changed the `HttpException` class constructor to accept the Content ID as parameter.

## [1.0.0-alpha28] - 2020-05-12
 ### Add
 - Added extra details to request send exception.

## [1.0.0-alpha27] - 2020-01-06
 ### Fix
 - Fixing response parsing error which occured during batch delete tasks.

## [1.0.0-alpha26] - 2019-11-14
 ### Change
 - Using Msgpack extension is not default from now.
 - Checking Msgpack extension is separated to one place.
 ### Add
 - Content-ID to header for debugging.

## [1.0.0-alpha25] - 2019-07-17
 ### Fix
 - Fixing the exception handling during the response parse.
 - Fixing the missing content ID during the response parse exception.

## [1.0.0-alpha24] - 2019-07-11
 ### Change
 - Changed the minimum PHP version requirement to PHP 7.0.
 ### Fix
 - Handling multipart request errors.

## [1.0.0-alpha23] - 2019-06-06
 ### Add
 - Added dumping service classes to dump request and response data out for further examination.

## [1.0.0-alpha22] - 2019-05-30
 ### Change
 - Added more information to the debug if an error occurs during the parsing of the response.

## [1.0.0-alpha21] - 2019-05-09
 ### Add
 - Add `acceptLanguage` property to Client service. By using it, it's possible to set the Accept-Language HTTP header.

## [1.0.0-alpha20] - 2019-04-29
 ### Add
 - Add property tag to `ResponseError` class PHPDoc.

## [1.0.0-alpha19] - 2019-04-26
 ### Add
 - Add property tag to `Response` class PHPDoc.

## [1.0.0-alpha18] - 2019-04-25
 ### Fix
 - Implement `FailedDependencyHttpException` into the `HttpException` class.

## [1.0.0-alpha17] - 2019-04-23
 ### Add
 - Added `FailedDependencyHttpException` exception class.

## [1.0.0-alpha16] - 2019-04-08
 ### Add
 - Added license information to composer.json file.
 ### Change
 - `MultipartRequest` class is setting the `Content-Id` header from the API request.

## [1.0.0-alpha15] - 2019-03-13
 ### Add
 - Added `getRetryAfter` and `getRetryAfterAbsolute` functions to `ServiceUnavailableHttpException` class.

## [1.0.0-alpha14] - 2019-03-13
 ### Add
 - Added `NotImplementedHttpException` class

## [1.0.0-alpha13] - 2019-03-13
 ### Fix
 - `ensureClass` function won't create class from attribute with `null` value.

## [1.0.0-alpha12] - 2019-02-22
 ### Fix
 - Setting up default status code for generating exception.

## [1.0.0-alpha11] - 2019-02-22
 ### Change
 - Generated exceptions will revieve the response headers.
 ### Fix
 - Fixing the `TooManyRequestsHttpException` class header handling.

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
