<?php

namespace Anibalealvarezs\FacebookGraphApi\Enums;

/**
 * @see https://developers.facebook.com/docs/instagram-api/reference/ig-media/insights/ (Media Insights)
 * @see https://developers.facebook.com/docs/instagram-api/reference/ig-user/insights/ (User/Account Insights)
 */
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
    case WEBSITE_CLICKS = 'website_clicks';
    case IMPRESSIONS = 'impressions';
    case CLICKS = 'clicks';
    case CTR = 'ctr';
    case CPC = 'cpc';
    case CPM = 'cpm';
    case SPEND = 'spend';
    case FREQUENCY = 'frequency';
    case ACTIONS = 'actions';
    case ACTION_VALUES = 'action_values';
    case COST_PER_ACTION_TYPE = 'cost_per_action_type';
    case OBJECTIVE = 'objective';
    case DATE_START = 'date_start';
    case DATE_STOP = 'date_stop';
    case UNIQUE_CLICKS = 'unique_clicks';
    case UNIQUE_CTR = 'unique_ctr';
    case COST_PER_UNIQUE_CLICK = 'cost_per_unique_click';
    case COST_PER_INLINE_LINK_CLICK = 'cost_per_inline_link_click';
    case COST_PER_UNIQUE_OUTBOUND_CLICK = 'cost_per_unique_outbound_click';
    case COST_PER_UNIQUE_CONVERSION = 'cost_per_unique_conversion';
    case ESTIMATED_AD_RECALLERS = 'estimated_ad_recallers';
    case ESTIMATED_AD_RECALL_RATE = 'estimated_ad_recall_rate';
    case CONVERSION_RATE_RANKING = 'conversion_rate_ranking';
    case COST_PER_ESTIMATED_AD_RECALLERS = 'cost_per_estimated_ad_recallers';
    case COST_PER_RESULT = 'cost_per_result';
    case COST_PER_INLINE_POST_ENGAGEMENT = 'cost_per_inline_post_engagement';
    case COST_PER_THRUPLAY = 'cost_per_thruplay';
    case OPTIMIZATION_GOAL = 'optimization_goal';
    case THRUPLAY = 'thruplay';
    case QUALITY_RANKING = 'quality_ranking';
    case ENGAGEMENT_RATE_RANKING = 'engagement_rate_ranking';
    case PURCHASE_ROAS = 'purchase_roas';
    case WEBSITE_PURCHASE_ROAS = 'website_purchase_roas';
    case MOBILE_APP_PURCHASE_ROAS = 'mobile_app_purchase_roas';
    case OBJECTIVE_RESULTS = 'objective_results';
    case COST_PER_OBJECTIVE_RESULT = 'cost_per_objective_result';
    case RESULTS = 'results';
    case RESULT_RATE = 'result_rate';

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
            self::WEBSITE_CLICKS,
            self::TOTAL_INTERACTIONS => [MetricType::TOTAL_VALUE],
            self::ONLINE_FOLLOWERS,
            self::THREADS_VIEWS,
            self::IMPRESSIONS,
            self::CLICKS,
            self::CTR,
            self::CPC,
            self::CPM,
            self::SPEND,
            self::FREQUENCY,
            self::ACTIONS,
            self::ACTION_VALUES,
            self::COST_PER_ACTION_TYPE,
            self::OBJECTIVE,
            self::DATE_START,
            self::DATE_STOP,
            self::UNIQUE_CLICKS,
            self::UNIQUE_CTR,
            self::COST_PER_UNIQUE_CLICK,
            self::COST_PER_INLINE_LINK_CLICK,
            self::COST_PER_UNIQUE_OUTBOUND_CLICK,
            self::COST_PER_UNIQUE_CONVERSION,
            self::ESTIMATED_AD_RECALLERS,
            self::ESTIMATED_AD_RECALL_RATE,
            self::CONVERSION_RATE_RANKING,
            self::COST_PER_ESTIMATED_AD_RECALLERS,
            self::COST_PER_RESULT,
            self::COST_PER_INLINE_POST_ENGAGEMENT,
            self::COST_PER_THRUPLAY,
            self::OPTIMIZATION_GOAL,
            self::THRUPLAY,
            self::QUALITY_RANKING,
            self::ENGAGEMENT_RATE_RANKING,
            self::PURCHASE_ROAS,
            self::WEBSITE_PURCHASE_ROAS,
            self::MOBILE_APP_PURCHASE_ROAS,
            self::OBJECTIVE_RESULTS,
            self::COST_PER_OBJECTIVE_RESULT,
            self::RESULTS,
            self::RESULT_RATE => [],
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
            self::IMPRESSIONS,
            self::CLICKS,
            self::CTR,
            self::CPC,
            self::CPM,
            self::SPEND,
            self::FREQUENCY,
            self::ACTIONS,
            self::ACTION_VALUES,
            self::COST_PER_ACTION_TYPE,
            self::OBJECTIVE,
            self::DATE_START,
            self::DATE_STOP,
            self::UNIQUE_CLICKS,
            self::UNIQUE_CTR,
            self::COST_PER_UNIQUE_CLICK,
            self::COST_PER_INLINE_LINK_CLICK,
            self::COST_PER_UNIQUE_OUTBOUND_CLICK,
            self::COST_PER_UNIQUE_CONVERSION,
            self::ESTIMATED_AD_RECALLERS,
            self::ESTIMATED_AD_RECALL_RATE,
            self::CONVERSION_RATE_RANKING,
            self::COST_PER_ESTIMATED_AD_RECALLERS,
            self::COST_PER_RESULT,
            self::COST_PER_INLINE_POST_ENGAGEMENT,
            self::COST_PER_THRUPLAY,
            self::OPTIMIZATION_GOAL,
            self::THRUPLAY,
            self::QUALITY_RANKING,
            self::ENGAGEMENT_RATE_RANKING,
            self::THREADS_VIEWS => [],
            self::FOLLOWS_AND_UNFOLLOWS,
            self::VIEWS,
            self::SAVES,
            self::SHARES,
            self::LIKES,
            self::COMMENTS,
            self::PROFILE_LINK_TAPS,
            self::WEBSITE_CLICKS,
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
            self::IMPRESSIONS,
            self::CLICKS,
            self::CTR,
            self::CPC,
            self::CPM,
            self::SPEND,
            self::FREQUENCY,
            self::ACTIONS,
            self::ACTION_VALUES,
            self::COST_PER_ACTION_TYPE,
            self::OBJECTIVE,
            self::DATE_START,
            self::DATE_STOP,
            self::UNIQUE_CLICKS,
            self::UNIQUE_CTR,
            self::COST_PER_UNIQUE_CLICK,
            self::COST_PER_INLINE_LINK_CLICK,
            self::COST_PER_UNIQUE_OUTBOUND_CLICK,
            self::COST_PER_UNIQUE_CONVERSION,
            self::ESTIMATED_AD_RECALLERS,
            self::ESTIMATED_AD_RECALL_RATE,
            self::CONVERSION_RATE_RANKING,
            self::COST_PER_ESTIMATED_AD_RECALLERS,
            self::COST_PER_RESULT,
            self::COST_PER_INLINE_POST_ENGAGEMENT,
            self::COST_PER_THRUPLAY,
            self::OPTIMIZATION_GOAL,
            self::THRUPLAY,
            self::QUALITY_RANKING,
            self::ENGAGEMENT_RATE_RANKING,
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
            self::WEBSITE_CLICKS,
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
            self::WEBSITE_CLICKS,
            self::IMPRESSIONS,
            self::CLICKS,
            self::CTR,
            self::CPC,
            self::CPM,
            self::SPEND,
            self::FREQUENCY,
            self::ACTIONS,
            self::ACTION_VALUES,
            self::COST_PER_ACTION_TYPE,
            self::OBJECTIVE,
            self::DATE_START,
            self::DATE_STOP,
            self::UNIQUE_CLICKS,
            self::UNIQUE_CTR,
            self::COST_PER_UNIQUE_CLICK,
            self::COST_PER_INLINE_LINK_CLICK,
            self::COST_PER_UNIQUE_OUTBOUND_CLICK,
            self::COST_PER_UNIQUE_CONVERSION,
            self::ESTIMATED_AD_RECALLERS,
            self::ESTIMATED_AD_RECALL_RATE,
            self::CONVERSION_RATE_RANKING,
            self::COST_PER_ESTIMATED_AD_RECALLERS,
            self::COST_PER_RESULT,
            self::COST_PER_INLINE_POST_ENGAGEMENT,
            self::COST_PER_THRUPLAY,
            self::OPTIMIZATION_GOAL,
            self::THRUPLAY,
            self::QUALITY_RANKING,
            self::ENGAGEMENT_RATE_RANKING,
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
            self::WEBSITE_CLICKS,
            self::TOTAL_INTERACTIONS => MetricGroup::MULTIPLE,
            self::THREADS_VIEWS => MetricGroup::THREADS_VIEWS,
            self::ONLINE_FOLLOWERS => MetricGroup::ONLINE_FOLLOWERS,
            self::IMPRESSIONS,
            self::CLICKS,
            self::CTR,
            self::CPC,
            self::CPM,
            self::SPEND,
            self::FREQUENCY,
            self::ACTIONS,
            self::ACTION_VALUES,
            self::COST_PER_ACTION_TYPE,
            self::OBJECTIVE,
            self::DATE_START,
            self::DATE_STOP,
            self::UNIQUE_CLICKS,
            self::UNIQUE_CTR,
            self::COST_PER_UNIQUE_CLICK,
            self::COST_PER_INLINE_LINK_CLICK,
            self::COST_PER_UNIQUE_OUTBOUND_CLICK,
            self::COST_PER_UNIQUE_CONVERSION,
            self::ESTIMATED_AD_RECALLERS,
            self::ESTIMATED_AD_RECALL_RATE,
            self::CONVERSION_RATE_RANKING,
            self::COST_PER_ESTIMATED_AD_RECALLERS,
            self::COST_PER_RESULT,
            self::COST_PER_INLINE_POST_ENGAGEMENT,
            self::COST_PER_THRUPLAY,
            self::OPTIMIZATION_GOAL,
            self::THRUPLAY,
            self::QUALITY_RANKING,
            self::ENGAGEMENT_RATE_RANKING,
            self::PURCHASE_ROAS,
            self::WEBSITE_PURCHASE_ROAS,
            self::MOBILE_APP_PURCHASE_ROAS,
            self::OBJECTIVE_RESULTS,
            self::COST_PER_OBJECTIVE_RESULT,
            self::RESULTS,
            self::RESULT_RATE => MetricGroup::OTHERS,
        };
    }
}
