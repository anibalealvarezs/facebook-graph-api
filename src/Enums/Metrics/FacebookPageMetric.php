<?php

namespace Anibalealvarezs\FacebookGraphApi\Enums\Metrics;

use Anibalealvarezs\FacebookGraphApi\Enums\MetricBreakdown;
use Anibalealvarezs\FacebookGraphApi\Enums\MetricPeriod;
use Anibalealvarezs\FacebookGraphApi\Enums\MetricTimeframe;
use Anibalealvarezs\FacebookGraphApi\Enums\MetricType;

/**
 * @see https://developers.facebook.com/docs/graph-api/reference/page/insights/
 */
enum FacebookPageMetric: string
{
    case PAGE_IMPRESSIONS = 'page_impressions';
    case PAGE_IMPRESSIONS_UNIQUE = 'page_impressions_unique';
    case PAGE_POST_ENGAGEMENTS = 'page_post_engagements';
    case PAGE_VIEWS_TOTAL = 'page_views_total';
    case PAGE_FOLLOWS = 'page_follows';
    case PAGE_VIDEO_VIEWS = 'page_video_views';
    case PAGE_ACTIONS_POST_REACTIONS_TOTAL = 'page_actions_post_reactions_total';
    case PAGE_FAN_ADDS = 'page_fan_adds';
    case PAGE_FANS = 'page_fans';

    public function allowedMetricTypes(): array
    {
        return [];
    }

    public function allowedPeriods(): array
    {
        return [MetricPeriod::DAY, MetricPeriod::WEEK, MetricPeriod::DAYS_28];
    }

    public function allowedBreakdowns(): array
    {
        return [];
    }

    public function allowedTimeframes(): array
    {
        return [];
    }

    public function group(): MetricGroup
    {
        return MetricGroup::OTHERS;
    }
}
