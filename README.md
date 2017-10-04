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
        "jamesiarmes/php-ntlm": "~1.0"
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

#### Available options
The basic options available on the constructor can be found at
http://php.net/manual/en/soapclient.soapclient.php. The trace option is not
necessary, as the last request and response methods will always be available. In
addition to these options, the following additional options are available:

- user (string, required): The user to authenticate with.
- password (string, required): The password to use when authenticating the user.
- curlopts (array): Array of options to set on the curl handler when making the
request. This can be used to override any cURL options with the exception of the
following: CURLOPT_HEADER, CURLOPT_POST, CURLOPT_POSTFIELDS.
- strip_bad_chars (boolean, default: true): Whether or not to strip invalid
characters from the XML response. This can lead to content being returned
differently than it actually is on the host service, but can also prevent the
"looks like we got no XML document" SoapFault when the response includes invalid
characters.
- warn_on_bad_chars (boolean, default: false): Trigger a warning if bad
characters are stripped. This has no affect unless strip_bad_chars is true.

## Projects that use php-ntlm
The following is a list of known projects that use this library. If you would
like to add your project to the list, please open a pull request to update this
document.

- [php-ews](https://github.com/jamesiarmes/php-ews)
