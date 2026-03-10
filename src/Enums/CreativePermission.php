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

    public function insightsFields(bool $full = false): string
    {
        $basic = 'spend,impressions,reach,clicks,ctr,cpc,frequency,actions'; // Basic for creatives
        if (!$full) {
            return $basic;
        }
        return $basic . ',cpm,action_values,cost_per_action_type,date_start,date_stop';
    }
}
