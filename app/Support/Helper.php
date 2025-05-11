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

function app_money($number): string
{
    return number_format($number, 2, '.', ' ');
}

function years(): array
{
    $years = [];

    for ($i = 2025; $i <= now()->year; $i++) {
        $years[$i] = $i;
    }

    return $years;
}

function months(): array
{
    return [
        '01' => 'Enero',
        '02' => 'Febrero',
        '03' => 'Marzo',
        '04' => 'Abril',
        '05' => 'Mayo',
        '06' => 'Junio',
        '07' => 'Julio',
        '08' => 'Agosto',
        '09' => 'Setiembre',
        '10' => 'Octubre',
        '11' => 'Noviembre',
        '12' => 'Diciembre',
    ];
}

function toCarbon($year, $month = '01', $day = '01'): Carbon
{
    return Carbon::parse($year . '-' . $month . '-' . $day);
}
