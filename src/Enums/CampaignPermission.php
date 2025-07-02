<?php

namespace Anibalealvarezs\FacebookGraphApi\Enums;

/**
 * Enum for fields available per page permission for the /me and /me/accounts endpoints.
 */
enum CampaignPermission: string
{
    case DEFAULT = 'default';

    public function fields(): string
    {
        return match ($this) {
            self::DEFAULT => CampaignField::toCommaSeparatedList(),
        };
    }

    public function insightsFields(): string
    {
        return 'impressions,clicks,ctr,cpc,cpm,spend,reach,frequency,actions,action_values,cost_per_action_type,objective,date_start,date_stop';
    }
}