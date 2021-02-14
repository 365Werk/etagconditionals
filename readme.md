
# Laravel ETag & Conditionals

  

[![Latest Version on Packagist][ico-version]][link-packagist]

[![Total Downloads][ico-downloads]][link-downloads]

[![StyleCI][ico-styleci]][link-styleci]

  

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

__setEtag__ `Method: Any`
This middleware will set the `ETag` header on your responses. The `ETag` header is equal to a md5 hash of `$response->getContent()`.

__ifMatch__ `Method: PATCH`
This middleware will resolve the action requested by the patch request, and will attempt to call the `GET` request equivalent. For controllers, this means we assume a method `show` is present on the controller. For closures, the closure will currently be called as is. For this reason, support for closures is not complete and is only recommended to be used for testing (see `tests/IfMatchTest.php`).
The content retrieved will then be hashed in the same way the original ETag was created, and compared to the `If-Match` header sent. If the `If-Match` header and new etag match, the `PATCH` request will be allowed through the middleware. If there is no match, `412` will be returned.

__ifNoneMatch__ `Method: GET|HEAD`
This middleware will simply compare the submitted `If-None-Match` header to a newly created etag of the response. If there is no match, `200` is returned, with the new response in the case of a `GET` request. If the hashes are matching, `304` is returned with no content, allowing the browser to used cached content instead.

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
 Please see the [license file](license.md) for more information.

  

[ico-version]: https://img.shields.io/packagist/v/werk365/etagconditionals.svg?style=flat-square

[ico-downloads]: https://img.shields.io/packagist/dt/werk365/etagconditionals.svg?style=flat-square

[ico-styleci]: https://styleci.io/repos/338617549/shield

  

[link-packagist]: https://packagist.org/packages/werk365/etagconditionals

[link-downloads]: https://packagist.org/packages/werk365/etagconditionals

[link-styleci]: https://styleci.io/repos/338617549

[link-author]: https://github.com/HergenD

[link-contributors]: ../../contributors