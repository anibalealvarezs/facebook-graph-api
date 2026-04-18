<?php

namespace Anibalealvarezs\FacebookGraphApi\Enums\Metrics;

use Anibalealvarezs\FacebookGraphApi\Enums\MetricBreakdown;
use Anibalealvarezs\FacebookGraphApi\Enums\MetricPeriod;
use Anibalealvarezs\FacebookGraphApi\Enums\MetricTimeframe;
use Anibalealvarezs\FacebookGraphApi\Enums\MetricType;

/**
 * @see https://developers.facebook.com/docs/instagram-api/reference/ig-media/insights/
 */
enum InstagramMediaMetric: string
{
    case REACH = 'reach';
    case TOTAL_INTERACTIONS = 'total_interactions';
    case LIKES = 'likes';
    case COMMENTS = 'comments';
    case SHARES = 'shares';
    case SAVED = 'saved';
    case REPLIES = 'replies';
    case VIEWS = 'views';
    case PLAYS = 'plays';
    case REPOSTS = 'reposts';
    case PROFILE_VISITS = 'profile_visits';
    case PROFILE_ACTIVITY = 'profile_activity';
    case IG_REELS_AVG_WATCH_TIME = 'ig_reels_avg_watch_time';
    case IG_REELS_VIDEO_VIEW_TOTAL_TIME = 'ig_reels_video_view_total_time';
    case FACEBOOK_VIEWS = 'facebook_views';

    public function allowedMetricTypes(): array
    {
        return [MetricType::TOTAL_VALUE];
    }

    public function allowedPeriods(): array
    {
        return [MetricPeriod::LIFETIME, MetricPeriod::DAY];
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
        return MetricGroup::MULTIPLE;
    }
}
