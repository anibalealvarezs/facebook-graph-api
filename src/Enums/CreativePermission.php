<?php

namespace Anibalealvarezs\FacebookGraphApi\Enums;

/**
 * Enum for fields available per page permission for the /me and /me/accounts endpoints.
 */
enum CreativePermission: string
{
    case DEFAULT = 'default';

    public function fields(): string
    {
        return match ($this) {
            self::DEFAULT => CreativeField::toCommaSeparatedList(),
        };
    }

    public function insightsFields(): string
    {
        return 'campaign_id,adset_id,ad_id,ad_name,impressions,reach,frequency,clicks,spend,ctr,cpc,cpm,actions,action_values,cost_per_action_type,date_start,date_stop';
    }
}