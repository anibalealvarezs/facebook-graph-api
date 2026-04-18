<?php

namespace Anibalealvarezs\FacebookGraphApi\Enums\Metrics;

enum MetricGroup: string
{
    case REACH_FOLLOWERS = 'reach_followers';
    case DEMOGRAPHICS = 'demographics';
    case ONLINE_FOLLOWERS = 'online_followers';
    case THREADS_VIEWS = 'threads_views';
    case MULTIPLE = 'multiple';
    case OTHERS = 'others';

    public function getMetrics(): array
    {
        return match($this) {
            self::REACH_FOLLOWERS => [Metric::REACH, Metric::FOLLOWER_COUNT],
            self::DEMOGRAPHICS => [Metric::REACHED_AUDIENCE_DEMOGRAPHICS, Metric::ENGAGED_AUDIENCE_DEMOGRAPHICS, Metric::FOLLOWER_DEMOGRAPHICS],
            self::ONLINE_FOLLOWERS => [Metric::ONLINE_FOLLOWERS],
            self::THREADS_VIEWS => [Metric::THREADS_VIEWS],
            self::MULTIPLE => [Metric::FOLLOWS_AND_UNFOLLOWS, Metric::ACCOUNTS_ENGAGED, Metric::PROFILE_VIEWS, Metric::CONTENT_VIEWS, Metric::VIEWS, Metric::PROFILE_LINK_TAPS, Metric::REPLIES, Metric::SAVES, Metric::SHARES, Metric::COMMENTS, Metric::LIKES, Metric::WEBSITE_CLICKS, Metric::TOTAL_INTERACTIONS],
            self::OTHERS => [Metric::IMPRESSIONS, Metric::CLICKS, Metric::CTR, Metric::CPC, Metric::CPM, Metric::SPEND, Metric::FREQUENCY, Metric::ACTIONS, Metric::ACTION_VALUES, Metric::COST_PER_ACTION_TYPE, Metric::OBJECTIVE, Metric::DATE_START, Metric::DATE_STOP, Metric::UNIQUE_CLICKS, Metric::UNIQUE_CTR, Metric::COST_PER_UNIQUE_CLICK, Metric::COST_PER_INLINE_LINK_CLICK, Metric::COST_PER_UNIQUE_OUTBOUND_CLICK, Metric::COST_PER_UNIQUE_CONVERSION, Metric::ESTIMATED_AD_RECALLERS, Metric::ESTIMATED_AD_RECALL_RATE, Metric::CONVERSION_RATE_RANKING, Metric::COST_PER_ESTIMATED_AD_RECALLERS, Metric::COST_PER_RESULT, Metric::COST_PER_INLINE_POST_ENGAGEMENT, Metric::COST_PER_THRUPLAY, Metric::OPTIMIZATION_GOAL, Metric::THRUPLAY, Metric::QUALITY_RANKING, Metric::ENGAGEMENT_RATE_RANKING, Metric::PURCHASE_ROAS, Metric::WEBSITE_PURCHASE_ROAS, Metric::MOBILE_APP_PURCHASE_ROAS, Metric::OBJECTIVE_RESULTS, Metric::COST_PER_OBJECTIVE_RESULT, Metric::RESULTS, Metric::RESULT_RATE],
            default => [],
        };
    }
}
