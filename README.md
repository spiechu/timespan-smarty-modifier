# Timespan Smarty Modifier

Smarty modifier intended to convert given date in Smarty template into human-readable string such as `2 minutes ago` in many languages.
Currently only english and polish languages are supported.

Polish numeral inflection is well known for its variety. It has 3 numeral inflection forms. This library tries to cope with this task.

## Requirements

Modifier is being written on Smarty 3.1.8, so I can't guarantee it will work on version < 3.0.

## Installation

All You need to do to use modifier is to put `modifier.timespan.php` into Your Smarty plugin folder.
For the sake of readability, modifier file has been thrown into separate plugin folder instead of Smarty's `Smarty/libs/plugins` one. If You want to do that as well, use `$smarty->addPluginsDir('yourpluginsfolder');`.

## Usage

In Your PHP script assign DateTime object or timestamp integer for example like:

    $smarty->assign('date', new DateTime());
  
and then in template:

    {$date|timespan}

output will be:

    just now
    
Default value for `just now` and all other foreign language equivalents is <= 10 seconds.

You can modify language and suffix 'ago' displaying for example:

    {$date|timespan:'PL'}
    {$date|timespan:'EN':false}
    
If there is only one certain unit, numeric value is omitted. For example `a minute ago`, `an hour ago`...

