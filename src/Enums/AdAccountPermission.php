<?php

namespace Anibalealvarezs\FacebookGraphApi\Enums;

/**
 * Enum for fields available per page permission for the /me and /me/accounts endpoints.
 */
enum AdAccountPermission: string
{
    case DEFAULT = 'default';

    public function fields(): string
    {
        return match ($this) {
            self::DEFAULT => AdAccountField::toCommaSeparatedList(),
        };
    }

    public function insightsFields(MetricSet $set = MetricSet::BASIC): string
    {
        $basic = 'spend,impressions,reach,clicks,ctr,cpc,frequency,cost_per_result,results,purchase_roas,actions';
        $key = $basic . ',cpm,result_rate,cost_per_objective_result,website_purchase_roas,mobile_app_purchase_roas,objective_results';
        return match ($set) {
            MetricSet::BASIC => $basic,
            MetricSet::KEY => $key,
            MetricSet::FULL => $key . ',action_values,cost_per_action_type,cost_per_unique_click,cost_per_inline_link_click,cost_per_inline_post_engagement,cost_per_unique_outbound_click,cost_per_estimated_ad_recallers,cost_per_thruplay,estimated_ad_recallers,optimization_goal,objective,date_start,date_stop,unique_clicks,unique_ctr,estimated_ad_recall_rate,conversion_rate_ranking,quality_ranking,engagement_rate_ranking,video_30_sec_watched_actions,video_avg_time_watched_actions,video_p25_watched_actions,video_p50_watched_actions,video_p75_watched_actions,video_p95_watched_actions,video_p100_watched_actions,video_play_actions,video_thruplay_watched_actions',
        };
    }
}
