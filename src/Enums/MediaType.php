<?php

namespace Anibalealvarezs\FacebookGraphApi\Enums;

/**
 * @see https://developers.facebook.com/docs/instagram-api/reference/ig-media/
 */
enum MediaType: string
{
    case IMAGE = 'IMAGE';
    case VIDEO  = 'VIDEO';
    case CAROUSEL_ALBUM = 'CAROUSEL_ALBUM';

    public function insightsFields(MetricSet $set = MetricSet::BASIC): string
    {
        $basic = match ($this) {
            self::IMAGE,
            self::CAROUSEL_ALBUM => 'comments,likes,reach,saved,shares,total_interactions,views',
            self::VIDEO => 'reach,saved',
            default => 'comments,ig_reels_avg_watch_time,ig_reels_video_view_total_time,likes,reach,saved,shares,total_interactions,views',
        };
        return match ($set) {
            MetricSet::BASIC, MetricSet::KEY, MetricSet::FULL => $basic,
            MetricSet::CUSTOM => '',
        };
    }
}
