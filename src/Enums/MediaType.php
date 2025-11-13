<?php

namespace Anibalealvarezs\FacebookGraphApi\Enums;

enum MediaType : string
{
    case IMAGE = 'IMAGE';
    case VIDEO  = 'VIDEO';
    case CAROUSEL_ALBUM = 'CAROUSEL_ALBUM';

    public function insightsFields(): string
    {
        return match ($this) {
            self::IMAGE,
            self::CAROUSEL_ALBUM => 'comments,likes,reach,saved,shares,total_interactions,views',
            self::VIDEO => 'reach,saved',
            default => 'comments,ig_reels_avg_watch_time,ig_reels_video_view_total_time,likes,reach,saved,shares,total_interactions,views',
        };
    }
}
