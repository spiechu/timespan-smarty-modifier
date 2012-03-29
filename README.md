# Timespan Smarty Modifier

Smarty modifier intended to convert given date in Smarty template into human-readable string such as `2 minutes ago` in many languages.
Currently only english and polish languages are supported.

Polish numeral inflection is well known for its variety. It has 3 numeral inflection forms. This library tries to cope with this task.

## Features

* `just now` when time interval is <= 10 default seconds
* `about a half minute ago` when within 30 seconds +/- 10% default tolerance
* `about a minute ago` when near full minute (default -10% tolerance)
* `a minute ago` when at least 60 seconds and less than 90 seconds - 10% tolerance
* `about a minute and a half ago` when 90 seconds +/- 10% tolerance
* `2 minutes ago`
* `about 2 and half minutes ago` when 120 seconds + (30 +/- 10% tolerance)
* `an hour and 25 minutes ago` picks two greatest units and concatenates them
* `2 hours and 5 minutes ago`

Notice that `about` shows up when some time is approximated.

`ago` can be turned off.

There is also a special polish expression for 1.5 unit called `półtora`/`półtorej`. These special cases are also included in library.                                                                                 

## Requirements

Modifier is being written on Smarty 3.1.8, so I can't guarantee it will work on version < 3.0.
You also need at least PHP 5.3 since library uses namespaces. No external libraries needed.
If You want to run unit tests, PHPUnit 3.6 is needed.

## Installation

All You need to do to use modifier is to put `modifier.timespan.php` into Your Smarty plugin folder.
For the sake of readability, modifier file has been thrown into separate plugin folder instead of Smarty's `Smarty/libs/plugins` one. If You want to do that as well, use `$smarty->addPluginsDir('yourpluginsfolder');` in Your Smarty configuration section.

Library also needs to be registered for autoload. It uses standard SplClassLoader, for example:

```php
<?php
require_once 'SplClassLoader.php';
$classLoader = new SplClassLoader('Spiechu\TimeSpan' , 'library');
$classLoader->register();
```

## Usage

In Your PHP script assign DateTime object or timestamp integer for example like:

```php
<?php
$smarty->assign('date', new DateTime('1 second ago'));
// or
$smarty->assign('date', time() - 1);
```

and then in template:

```smarty
{$date|timespan}
```

output will be:

```smarty
just now
```

You can also use library without Smarty:

```php
<?php
$timeSpan = new TimeSpan();
$timeSpan
         ->setStartDate($startDateTime) // DateTime or timestamp
         ->setLanguage($lang) // 'EN' or 'PL'
         ->showSuffix($suffix); // true or false
return $timeSpan->getTimeSpanString();
```
    
Default value for `just now` and all other foreign language equivalents is <= 10 seconds.

You can modify language and suffix 'ago' displaying for example:

```smarty
{$date|timespan:'PL'}
{$date|timespan:'EN':false}
```
    
If there is only one certain unit, numeric value is omitted. For example `a minute ago`, `an hour ago`...
