<h2 align="center">Http Message Util</h2>
<h3 align="center">This repository holds utility classes and constants to facilitate common operations of PSR-7.</h3>
<p align="center">
    <a href="https://github.com/narrowspark/http-message-util/releases"><img src="https://img.shields.io/packagist/v/narrowspark/http-message-util.svg?style=flat-square"></a>
    <a href="https://php.net/"><img src="https://img.shields.io/badge/php-%5E7.1.0-8892BF.svg?style=flat-square"></a>
    <a href="https://travis-ci.org/narrowspark/http-message-util"><img src="https://img.shields.io/travis/narrowspark/http-message-util/master.svg?style=flat-square"></a>
    <a href="https://codecov.io/gh/narrowspark/http-message-util"><img src="https://img.shields.io/codecov/c/github/narrowspark/http-message-util/master.svg?style=flat-square"></a>
    <a href="https://packagist.org/packages/narrowspark/http-message-util"><img src="https://img.shields.io/packagist/dt/narrowspark/http-message-util.svg?style=flat-square"></a>
    <a href="http://opensource.org/licenses/MIT"><img src="https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square"></a>
</p>

Each header class is named after the specific http header and has the following functions for parsing and signing.

Installation
------------

```bash
$ composer require narrowspark/http-message-util
```

Use
------------

Here's an example using the InteractsWithAcceptLanguage class:
``` php
<?php
declare(strict_types=1);

use Narrowspark\Http\Message\Util\InteractsWithAcceptLanguage;

$request = new Request();
$request = $request->withHeader('Accept-Language', 'zh, en-us; q=0.8, en; q=0.6');

return InteractsWithAcceptLanguage::getLanguages($request); // => ['zh', 'en', 'en_US']

```

Here's an example using the InteractsWithAuthorization class:

``` php
<?php
declare(strict_types=1);

use Narrowspark\Http\Message\Util\InteractsWithAuthorization;

$request = new Request();
$request = $request->withHeader('Authorization', 'Basic QWxhZGRpbjpPcGVuU2VzYW1l');

return InteractsWithAuthorization::getAuthorization($request); // => ['Basic', 'QWxhZGRpbjpPcGVuU2VzYW1l']

```

Here's an example using the InteractsWithContentTypes class:

``` php
<?php
declare(strict_types=1);

use Narrowspark\Http\Message\Util\InteractsWithContentTypes;

$request = new Request();
$request = $request->withHeader('Content-Type', 'application/json, */*');

return InteractsWithContentTypes::isJson($request); // => true

$request = $request->withHeader('X-Pjax', 'true');

return InteractsWithContentTypes::isPjax($request); // => true

$request = $request->withHeader('X-Requested-With', 'XMLHttpRequest');

return InteractsWithContentTypes::isAjax($request); // => true

```

Here's an example using the HeaderUtils class:
``` php
<?php
declare(strict_types=1);

use Narrowspark\Http\Message\Util\HeaderUtils;

return HeaderUtils::split("da, en-gb;q=0.8", ",;"); // => array(array('da'), array('en-gb', 'q=0.8'))

or

return HeaderUtils::combine(array(array("foo", "abc"), array("bar"))); // => array("foo" => "abc", "bar" => true)

or

return HeaderUtils::toString(array("foo" => "abc", "bar" => true, "baz" => "a b c"), ",");  // => 'foo=abc, bar, baz="a b c"'

or

return HeaderUtils::quote('foo bar'); // => "foo bar"

or

return HeaderUtils::unquote('"foo bar"'); // => foo bar
```

Here's an example using the InteractsWithDisposition class:
``` php
<?php
declare(strict_types=1);

use Narrowspark\Http\Message\Util\InteractsWithDisposition;

$response = InteractsWithDisposition::appendDispositionHeader(new Response(), InteractsWithDisposition::DISPOSITION_ATTACHMENT, 'foo.html');

return $response->getHeaderLine('Content-Disposition'); // => attachment; filename=foo.html

or

return InteractsWithDisposition::makeDisposition(InteractsWithDisposition::DISPOSITION_ATTACHMENT, 'foo.html'); // => attachment; filename=foo.html

```

Testing
------------

``` bash
$ vendor/bin/phpunit
```

Contributing
------------

If you would like to help take a look at the [list of issues](http://github.com/narrowspark/http-emitter/issues) and check our [Contributing](CONTRIBUTING.md) guild.

> **Note:** Please note that this project is released with a Contributor Code of Conduct. By participating in this project you agree to abide by its terms.


License
---------------

The Narrowspark http-emitter is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)
