<?php

namespace Anibalealvarezs\FacebookGraphApi\Enums\Metrics;

use Anibalealvarezs\FacebookGraphApi\Enums\MetricBreakdown;
use Anibalealvarezs\FacebookGraphApi\Enums\MetricPeriod;
use Anibalealvarezs\FacebookGraphApi\Enums\MetricTimeframe;
use Anibalealvarezs\FacebookGraphApi\Enums\MetricType;

/**
 * @see https://developers.facebook.com/docs/instagram-api/reference/ig-user/insights/
 */
enum InstagramAccountMetric: string
{
    case REACH = 'reach';
    case FOLLOWER_COUNT = 'follower_count';
    case ACCOUNTS_ENGAGED = 'accounts_engaged';
    case TOTAL_INTERACTIONS = 'total_interactions';
    case LIKES = 'likes';
    case COMMENTS = 'comments';
    case SHARES = 'shares';
    case SAVES = 'saves';
    case REPLIES = 'replies';
    case PROFILE_LINK_TAPS = 'profile_links_taps';
    case VIEWS = 'views';
    case CONTENT_VIEWS = 'content_views';
    case PROFILE_VIEWS = 'profile_views';
    case WEBSITE_CLICKS = 'website_clicks';
    case FOLLOWS_AND_UNFOLLOWS = 'follows_and_unfollows';
    case ONLINE_FOLLOWERS = 'online_followers';
    case ENGAGED_AUDIENCE_DEMOGRAPHICS = 'engaged_audience_demographics';
    case REACHED_AUDIENCE_DEMOGRAPHICS = 'reached_audience_demographics';
    case FOLLOWER_DEMOGRAPHICS = 'follower_demographics';

    public function allowedMetricTypes(): array
    {
        return match($this) {
            self::REACH => [MetricType::TIME_SERIES, MetricType::TOTAL_VALUE],
            self::FOLLOWER_COUNT => [MetricType::TIME_SERIES],
            default => [MetricType::TOTAL_VALUE],
        };
    }

    public function allowedPeriods(): array
    {
        return match($this) {
            self::REACH,
            self::FOLLOWER_COUNT => [MetricPeriod::DAY, MetricPeriod::WEEK, MetricPeriod::DAYS_28],
            self::REACHED_AUDIENCE_DEMOGRAPHICS,
            self::ENGAGED_AUDIENCE_DEMOGRAPHICS,
            self::FOLLOWER_DEMOGRAPHICS,
            self::ONLINE_FOLLOWERS => [MetricPeriod::LIFETIME],
            default => [MetricPeriod::DAY],
        };
    }

    public function allowedBreakdowns(): array
    {
        return match($this) {
            self::REACHED_AUDIENCE_DEMOGRAPHICS,
            self::ENGAGED_AUDIENCE_DEMOGRAPHICS,
            self::FOLLOWER_DEMOGRAPHICS => [
                [MetricBreakdown::AGE, MetricBreakdown::GENDER],
                [MetricBreakdown::AGE],
                [MetricBreakdown::GENDER],
                [MetricBreakdown::COUNTRY],
                [MetricBreakdown::CITY]
            ],
            self::FOLLOWS_AND_UNFOLLOWS => [
                [MetricBreakdown::FOLLOW_TYPE]
            ],
            self::TOTAL_INTERACTIONS,
            self::LIKES,
            self::COMMENTS,
            self::SHARES,
            self::SAVES => [
                [MetricBreakdown::MEDIA_PRODUCT_TYPE]
            ],
            self::REACH => [
                [MetricBreakdown::MEDIA_PRODUCT_TYPE, MetricBreakdown::FOLLOW_TYPE],
                [MetricBreakdown::MEDIA_PRODUCT_TYPE],
                [MetricBreakdown::FOLLOW_TYPE]
            ],
            self::PROFILE_LINK_TAPS => [
                [MetricBreakdown::CONTACT_BUTTON_TYPE]
            ],
            default => [],
        };
    }

    public function allowedTimeframes(): array
    {
        return match($this) {
            self::REACHED_AUDIENCE_DEMOGRAPHICS,
            self::ENGAGED_AUDIENCE_DEMOGRAPHICS,
            self::FOLLOWER_DEMOGRAPHICS => [MetricTimeframe::THIS_WEEK, MetricTimeframe::THIS_MONTH],
            default => []
        };
    }

    public function group(): MetricGroup
    {
        return match($this) {
            self::REACH,
            self::FOLLOWER_COUNT => MetricGroup::REACH_FOLLOWERS,
            self::REACHED_AUDIENCE_DEMOGRAPHICS,
            self::ENGAGED_AUDIENCE_DEMOGRAPHICS,
            self::FOLLOWER_DEMOGRAPHICS => MetricGroup::DEMOGRAPHICS,
            self::ONLINE_FOLLOWERS => MetricGroup::ONLINE_FOLLOWERS,
            default => MetricGroup::MULTIPLE,
        };
    }
}
