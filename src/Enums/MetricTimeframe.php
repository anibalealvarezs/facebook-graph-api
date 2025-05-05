<?php

namespace Anibalealvarezs\FacebookGraphApi\Enums;

enum MetricTimeframe: string
{
    case THIS_WEEK = 'this_week';
    case THIS_MONTH = 'this_month';
    case PREV_MONTH = 'prev_month';
    case LAST_14_DAYS = 'last_14_days';
    case LAST_30_DAYS = 'last_30_days';
    case LAST_90_DAYS = 'last_90_days';
}
