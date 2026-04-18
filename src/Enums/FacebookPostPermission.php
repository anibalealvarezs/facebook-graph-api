<?php

namespace Anibalealvarezs\FacebookGraphApi\Enums;

/**
 * Enum for fields available per page permission for the /me and /me/accounts endpoints.
 */
enum FacebookPostPermission: string
{
    case DEFAULT = 'default';

    public function fields(): string
    {
        return match ($this) {
            self::DEFAULT => FacebookPostField::toCommaSeparatedList(),
        };
    }

    public function insightsFields(MetricSet $set = MetricSet::BASIC): string
    {
        $basic = 'post_impressions_unique,post_engagements,post_clicks,post_reactions_by_type_total,post_media_view,post_video_views,post_video_avg_time_watched';
        return match ($set) {
            MetricSet::BASIC, MetricSet::KEY, MetricSet::FULL => $basic,
            MetricSet::CUSTOM => '',
        };
    }
}
