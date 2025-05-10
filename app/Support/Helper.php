<?php

use Carbon\Carbon;

function app_date($date): string
{
    $d = DateTime::createFromFormat("Y-m-d H:i:s", $date);
    return $d->format(app_datetime());
}

function app_datetime(): string
{
    return 'd/m/Y H:i:s';
}
