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

    public function insightsFields(): string
    {
        return 'impressions,clicks,ctr,cpc,cpm,spend,reach,frequency,actions,action_values,cost_per_action_type,objective,
                date_start,date_stop,unique_clicks,unique_ctr,cost_per_unique_click,cost_per_inline_link_click,
                cost_per_unique_outbound_click,cost_per_unique_conversion,estimated_ad_recallers,estimated_ad_recall_rate,
                conversion_rate_ranking,cost_per_estimated_ad_recallers,quality_ranking,engagement_rate_ranking';
    }
}