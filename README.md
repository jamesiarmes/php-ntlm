PHP NTLM
===================================

The PHP NTLM library (php-ntlm) is intended to provide various methods to aid in
communicating with Microsoft services that utilize NTLM authentication from
within PHP.

[![Scrutinizer](https://img.shields.io/scrutinizer/g/jamesiarmes/php-ntlm.svg?style=flat-square)](https://scrutinizer-ci.com/g/jamesiarmes/php-ntlm)
[![Total Downloads](https://img.shields.io/packagist/dt/jamesiarmes/php-ntlm.svg?style=flat-square)](https://packagist.org/packages/jamesiarmes/php-ntlm)

Dependencies
------------

 * Composer
 * PHP 5.4 or greater
 * cURL with NTLM support (7.23.0+ recommended)

Installation
------------

The preferred installation method is via Composer, which will automatically
handle autoloading of classes.

```json
{
    "require": {
        "jamesiarmes/php-ntlm": "dev-master"
    }
}
```

## Usage

### SoapClient
The `\jamesiarmes\PhpNtlm\SoapClient` class extends PHP's built in `SoapClient`
class and can be used in the same manner with a few minor changes.

1. The constructor accepts a required 'user' and 'password' index in the
`$options` array.
2. The constructor accepts an optional 'curlopts' index in the `$options` array
that can be used to set or override the default curl options.

Basic example:

```php
$client = new SoapClient(
    $wsdl,
    array('user' => 'username', 'password' => '12345')
);
```

Example that skips SSL certificate validation:

```php
$client = new SoapClient(
    $wsdl,
    array(
        'user' => 'username',
        'password' => '12345',
        'curlopts' => array(CURLOPT_SSL_VERIFYPEER => false),
    )
};
```
