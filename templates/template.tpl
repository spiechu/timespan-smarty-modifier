{foreach $dates as $date}
    {$date|date_format:'%H:%M:%S'}
    <br>
    {$date|date_format}
    <br>
    {$date|timespan:$lang}
    <br><br>
{/foreach}