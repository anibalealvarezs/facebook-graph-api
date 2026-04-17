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
    case REEL = 'REEL';

    public function insightsFields(MetricSet $set = MetricSet::BASIC): string
    {
        $metrics = match ($this) {
            self::REEL => 'comments,likes,plays,reach,saved,shares,total_interactions,views,ig_reels_avg_watch_time,ig_reels_video_view_total_time',
            self::VIDEO => 'comments,likes,reach,saved,shares,total_interactions,views,impressions',
            self::IMAGE,
            self::CAROUSEL_ALBUM => 'comments,likes,reach,saved,shares,total_interactions,impressions',
            default => 'comments,likes,reach,saved,shares,total_interactions,impressions',
        };

        return match ($set) {
            MetricSet::BASIC, MetricSet::KEY, MetricSet::FULL => $metrics,
            MetricSet::CUSTOM => '',
        };
    }
}
