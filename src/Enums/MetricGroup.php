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
    case OTHERS = 'OTHERS';

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
            self::OTHERS => [
                Metric::IMPRESSIONS,
                Metric::CLICKS,
                Metric::CTR,
                Metric::CPC,
                Metric::CPM,
                Metric::SPEND,
                Metric::FREQUENCY,
                Metric::ACTIONS,
                Metric::ACTION_VALUES,
                Metric::COST_PER_ACTION_TYPE,
                Metric::OBJECTIVE,
                Metric::DATE_START,
                Metric::DATE_STOP,
                Metric::UNIQUE_CLICKS,
                Metric::UNIQUE_CTR,
                Metric::COST_PER_UNIQUE_CLICK,
                Metric::COST_PER_INLINE_LINK_CLICK,
                Metric::COST_PER_UNIQUE_OUTBOUND_CLICK,
                Metric::COST_PER_UNIQUE_CONVERSION,
                Metric::ESTIMATED_AD_RECALLERS,
                Metric::ESTIMATED_AD_RECALL_RATE,
                Metric::CONVERSION_RATE_RANKING,
                Metric::COST_PER_ESTIMATED_AD_RECALLERS,
                Metric::QUALITY_RANKING,
                Metric::ENGAGEMENT_RATE_RANKING,
            ],
        };
    }
}
