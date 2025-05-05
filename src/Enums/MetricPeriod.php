<?php

namespace Anibalealvarezs\FacebookGraphApi\Enums;

enum MetricPeriod: string
{
    case DAY = 'day';
    case WEEK = 'week';
    case MONTH = 'month';
    case DAYS_28 = 'days_28';
    case LIFETIME = 'lifetime';
    case TOTAL_OVER_RANGE = 'total_over_range';
}
