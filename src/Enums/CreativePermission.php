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

    public function insightsFields(MetricSet $set = MetricSet::BASIC): string
    {
        $basic = 'spend,impressions,reach,clicks,ctr,cpc,frequency,actions'; // Basic for creatives
        $key = $basic . ',cpm,date_start,date_stop';
        return match ($set) {
            MetricSet::BASIC => $basic,
            MetricSet::KEY => $key,
            MetricSet::FULL => $key . ',action_values,cost_per_action_type',
        };
    }
}
