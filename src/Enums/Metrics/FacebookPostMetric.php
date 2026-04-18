<?php

namespace Anibalealvarezs\FacebookGraphApi\Enums\Metrics;

use Anibalealvarezs\FacebookGraphApi\Enums\MetricBreakdown;
use Anibalealvarezs\FacebookGraphApi\Enums\MetricPeriod;
use Anibalealvarezs\FacebookGraphApi\Enums\MetricTimeframe;
use Anibalealvarezs\FacebookGraphApi\Enums\MetricType;

/**
 * @see https://developers.facebook.com/docs/graph-api/reference/v22.0/post/insights
 */
enum FacebookPostMetric: string
{
    case POST_IMPRESSIONS = 'post_impressions';
    case POST_IMPRESSIONS_UNIQUE = 'post_impressions_unique';
    case POST_ENGAGEMENTS = 'post_engagements';
    case POST_CLICKS = 'post_clicks';
    case POST_REACTIONS_BY_TYPE_TOTAL = 'post_reactions_by_type_total';
    case POST_MEDIA_VIEW = 'post_media_view';
    case POST_VIDEO_VIEWS = 'post_video_views';
    case POST_VIDEO_AVG_TIME_WATCHED = 'post_video_avg_time_watched';

    public function allowedMetricTypes(): array
    {
        return [];
    }

    public function allowedPeriods(): array
    {
        return [MetricPeriod::DAY, MetricPeriod::LIFETIME];
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
