
# Laravel ETag & Conditionals

  

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]
[![StyleCI][ico-styleci]][link-styleci]
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/365Werk/etagconditionals/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/365Werk/etagconditionals/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/365Werk/etagconditionals/badges/build.png?b=master)](https://scrutinizer-ci.com/g/365Werk/etagconditionals/build-status/master)


  

This package provides a set of middlewares to both set [ETags](https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/ETag) and handle [HTTP Conditional Requests](https://developer.mozilla.org/en-US/docs/Web/HTTP/Conditional_requests#conditional_headers).

Currently both `If-None-Matched` and `If-Match` are supported.

The package aims to provide the tools to provide better client-side caching when building an API with Laravel, as well as preventing mid-air collisions.

When using the package and enabling the middleware, your client (browser) will take care of handling the caching provided by the `ETag` and `If-None-Match` headers automatically.

  

## Installation

  

Via Composer

  

``` bash

$ composer require werk365/etagconditionals

```

  

## Usage

  You can either use the middleware group, automatically applying all available middleware (recommended if using an apiResource route for example), by setting the `etag` middleware, or apply the middlewares individually. 

Currently available middleware:
* `setEtag`
* `ifMatch`
* `ifNoneMatch`

### setEtag 
`Method: Any`

This middleware will set the `ETag` header on your responses. The `ETag` header is equal to a md5 hash of `$response->getContent()`. `HEAD` requests are supported by transforming the request to a `GET` request and changing it back on the response.

### ifMatch 
`Method: PATCH`

This middleware will create a new request to the `GET` equivalent of the endpoint called and retrieve the current content. After this, a hash of the current content and the `If-Match` hash will be compared. If the hashes match, the `PATCH` request will be allowed through the middleware, but if there is no match, `412` will be returned.

> __Important__ Since the internal `GET` request created will also pass through enabled middleware, you might run in to some cases where this is causing issues. For example: if you have a middleware that changes the response body that was not applied to the response that the `If-Match` etag belongs to, this will result in non-matching hashes. 
>
>For this scenario, this middleware sets a `X-From-Middleware: IfMatch` header which you can use in other middleware to filter these requests. Please note that since this header could also be set by a client, it should never be used to skip anything important like auth middleware.

### ifNoneMatch 
`Method: GET|HEAD`

This middleware will simply compare the submitted `If-None-Match` header to a newly created etag of the response. If there is no match, `200` is returned, with the new response in the case of a `GET` request. If the hashes are matching, `304` is returned with no content, allowing the browser to used cached content instead.

### Comparison algorithms
By default, a weak comparison algorithm will be used for both the `IfMatch` and `IfNoneMatch` ETags. In practise this means that we simply strip any `W/` tags from the ETag, so they can be compared to normal tags created in the middleware. This is to support cases where certain configurations automatically add the `W/` tag to our supplied ETag.

This behaviour can be changed by either publishing the config file: 
```bash
$ php artisan vendor:publish --provider="Werk365\EtagConditionals\EtagConditionalsServiceProvider"
```
And then changing the following values:
```php
return [
    "if_match_weak" => env('IF_MATCH_WEAK', true),
    "if_none_match_weak" => env('IF_NONE_MATCH_WEAK', true),
];
```
Or by setting the ENV values above.

## Change log

  

Please see the [changelog](changelog.md) for more information on what has changed recently.
  

## Contributing

  

Feel free to create issues and submit pull requests. For any PR submitted, make sure it is covered by tests or include new tests.

  

## Security

  

If you discover any security related issues, please email author email instead of using the issue tracker.

  

## Credits

  

-  [Hergen Dillema][link-author]

-  [All Contributors][link-contributors]

  

## License
 Please see the [license file](LICENSE) for more information.

  

[ico-version]: https://img.shields.io/packagist/v/werk365/etagconditionals.svg?style=flat-square

[ico-downloads]: https://img.shields.io/packagist/dt/werk365/etagconditionals.svg?style=flat-square

[ico-styleci]: https://styleci.io/repos/338617549/shield

  

[link-packagist]: https://packagist.org/packages/werk365/etagconditionals

[link-downloads]: https://packagist.org/packages/werk365/etagconditionals

[link-styleci]: https://styleci.io/repos/338617549

[link-author]: https://github.com/HergenD

[link-contributors]: ../../contributors
