<?php

namespace Anibalealvarezs\FacebookGraphApi\Enums;

enum Metric: string
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
    case ENGAGED_AUDIENCE_DEMOGRAPHICS = 'engaged_audience_demographics';
    case REACHED_AUDIENCE_DEMOGRAPHICS = 'reached_audience_demographics';
    case FOLLOWER_DEMOGRAPHICS = 'follower_demographics';
    case ONLINE_FOLLOWERS = 'online_followers';
    case THREADS_VIEWS = 'threads_views';
    case FOLLOWS_AND_UNFOLLOWS = 'follows_and_unfollows';

    public function allowedMetricTypes(): array
    {
        return match($this) {
            self::REACH => [MetricType::TIME_SERIES, MetricType::TOTAL_VALUE],
            self::FOLLOWER_COUNT => [MetricType::TIME_SERIES],
            self::ACCOUNTS_ENGAGED,
            self::REACHED_AUDIENCE_DEMOGRAPHICS,
            self::ENGAGED_AUDIENCE_DEMOGRAPHICS,
            self::FOLLOWER_DEMOGRAPHICS,
            self::PROFILE_VIEWS,
            self::CONTENT_VIEWS,
            self::VIEWS,
            self::PROFILE_LINK_TAPS,
            self::REPLIES,
            self::SAVES,
            self::SHARES,
            self::COMMENTS,
            self::LIKES,
            self::FOLLOWS_AND_UNFOLLOWS,
            self::TOTAL_INTERACTIONS => [MetricType::TOTAL_VALUE],
            self::ONLINE_FOLLOWERS,
            self::THREADS_VIEWS => [],
        };
    }

    public function allowedPeriods(): array
    {
        return match($this) {
            self::REACH,
            self::FOLLOWER_COUNT => [MetricPeriod::DAY, MetricPeriod::WEEK, MetricPeriod::DAYS_28],
            self::ACCOUNTS_ENGAGED,
            self::PROFILE_VIEWS,
            self::CONTENT_VIEWS,
            self::REPLIES,
            self::THREADS_VIEWS => [],
            self::FOLLOWS_AND_UNFOLLOWS,
            self::VIEWS,
            self::SAVES,
            self::SHARES,
            self::LIKES,
            self::COMMENTS,
            self::PROFILE_LINK_TAPS,
            self::TOTAL_INTERACTIONS => [MetricPeriod::DAY],
            self::REACHED_AUDIENCE_DEMOGRAPHICS,
            self::ENGAGED_AUDIENCE_DEMOGRAPHICS,
            self::FOLLOWER_DEMOGRAPHICS,
            self::ONLINE_FOLLOWERS => [MetricPeriod::LIFETIME],
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
            self::FOLLOWER_COUNT,
            self::ACCOUNTS_ENGAGED,
            self::PROFILE_VIEWS,
            self::CONTENT_VIEWS,
            self::REPLIES,
            self::THREADS_VIEWS,
            self::ONLINE_FOLLOWERS => [],
            self::FOLLOWS_AND_UNFOLLOWS => [
                [MetricBreakdown::FOLLOW_TYPE]
            ],
            self::COMMENTS,
            self::LIKES,
            self::SAVES,
            self::SHARES,
            self::TOTAL_INTERACTIONS => [
                [MetricBreakdown::MEDIA_PRODUCT_TYPE]
            ],
            self::VIEWS,
            self::REACH => [
                [MetricBreakdown::MEDIA_PRODUCT_TYPE, MetricBreakdown::FOLLOW_TYPE],
                [MetricBreakdown::MEDIA_PRODUCT_TYPE],
                [MetricBreakdown::FOLLOW_TYPE]
            ],
            self::PROFILE_LINK_TAPS => [
                [MetricBreakdown::CONTACT_BUTTON_TYPE]
            ],
        };
    }

    public function allowedTimeframes(): array
    {
        return match($this) {
            self::REACHED_AUDIENCE_DEMOGRAPHICS,
            self::ENGAGED_AUDIENCE_DEMOGRAPHICS,
            self::FOLLOWER_DEMOGRAPHICS => [MetricTimeframe::THIS_WEEK, MetricTimeframe::THIS_MONTH],
            self::REACH,
            self::FOLLOWER_COUNT,
            self::ACCOUNTS_ENGAGED,
            self::PROFILE_VIEWS,
            self::CONTENT_VIEWS,
            self::VIEWS,
            self::PROFILE_LINK_TAPS,
            self::REPLIES,
            self::SAVES,
            self::SHARES,
            self::COMMENTS,
            self::LIKES,
            self::THREADS_VIEWS,
            self::TOTAL_INTERACTIONS,
            self::FOLLOWS_AND_UNFOLLOWS,
            self::ONLINE_FOLLOWERS => [],
        };
    }

    public function group(): MetricGroup
    {
        return match($this) {
            self::REACH,
            self::FOLLOWS_AND_UNFOLLOWS,
            self::FOLLOWER_COUNT => MetricGroup::REACH_FOLLOWERS,
            self::REACHED_AUDIENCE_DEMOGRAPHICS,
            self::ENGAGED_AUDIENCE_DEMOGRAPHICS,
            self::FOLLOWER_DEMOGRAPHICS => MetricGroup::DEMOGRAPHICS,
            self::ACCOUNTS_ENGAGED,
            self::PROFILE_VIEWS,
            self::CONTENT_VIEWS,
            self::VIEWS,
            self::PROFILE_LINK_TAPS,
            self::REPLIES,
            self::SAVES,
            self::SHARES,
            self::COMMENTS,
            self::LIKES,
            self::TOTAL_INTERACTIONS => MetricGroup::MULTIPLE,
            self::THREADS_VIEWS => MetricGroup::THREADS_VIEWS,
            self::ONLINE_FOLLOWERS => MetricGroup::ONLINE_FOLLOWERS
        };
    }
}
