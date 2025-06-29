<?php

namespace Anibalealvarezs\FacebookGraphApi\Enums;

/**
 * Enum for fields available per page permission for the /me and /me/accounts endpoints.
 */
enum AdPermission: string
{
    case DEFAULT = 'default';

    public function fields(): string
    {
        return match ($this) {
            self::DEFAULT => AdField::toCommaSeparatedList(),
        };
    }

    public function insightsFields(): string
    {
        return 'ad_name,ad_id,campaign_id,adset_id,impressions,reach,frequency,clicks,spend,ctr,cpc,cpm,actions,action_values,cost_per_action_type,date_start,date_stop';
    }
}