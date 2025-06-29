<?php

namespace Anibalealvarezs\FacebookGraphApi\Enums;

enum MetricGroup: string
{
    case REACH_FOLLOWERS = 'REACH_FOLLOWERS';
    case REACH_FOLLOWS_AND_UNFOLLOWS = 'REACH_FOLLOWS_AND_UNFOLLOWS';
    case MULTIPLE = 'MULTIPLE';
    case DEMOGRAPHICS = 'DEMOGRAPHICS';
    case ONLINE_FOLLOWERS = 'ONLINE_FOLLOWERS';
    case THREADS_VIEWS = 'THREADS_VIEWS';

    public function getMetrics(): array
    {
        return match ($this) {
            self::REACH_FOLLOWERS => [
                Metric::REACH,
                Metric::FOLLOWER_COUNT,
            ],
            self::REACH_FOLLOWS_AND_UNFOLLOWS => [
                Metric::REACH,
                Metric::FOLLOWS_AND_UNFOLLOWS,
            ],
            self::MULTIPLE => [
                Metric::ACCOUNTS_ENGAGED,
                Metric::TOTAL_INTERACTIONS,
                Metric::LIKES,
                Metric::SHARES,
                Metric::COMMENTS,
                Metric::REPLIES,
                Metric::SAVES,
                Metric::PROFILE_LINK_TAPS,
                Metric::PROFILE_VIEWS,
                Metric::VIEWS,
                Metric::CONTENT_VIEWS,
            ],
            self::DEMOGRAPHICS => [
                Metric::ENGAGED_AUDIENCE_DEMOGRAPHICS,
                Metric::REACHED_AUDIENCE_DEMOGRAPHICS,
                Metric::FOLLOWER_DEMOGRAPHICS,
            ],
            self::ONLINE_FOLLOWERS => [
                Metric::ONLINE_FOLLOWERS
            ],
            self::THREADS_VIEWS => [
                Metric::THREADS_VIEWS
            ],
        };
    }
}
