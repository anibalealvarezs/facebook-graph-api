<?php

namespace Anibalealvarezs\FacebookGraphApi\Enums;

enum MediaProductType: string
{
    case FEED = 'FEED';
    case REELS = 'REES';
    case STORY = 'STORY';
    case AD = 'AD';

    public function fields(): string
    {
        return match ($this) {
            self::FEED => 'comments,follows,likes,profile_activity,profile_visits,reach,saved,shares,total_interactions,views',
            self::REELS => 'comments,ig_reels_avg_watch_time,ig_reels_video_view_total_time,likes,reach,saved,shares,total_interactions,views',
            self::STORY => 'follows,navigation,profile_activity,profile_visits,reach,replies,shares,total_interactions,views',
            self::AD => 'comments,engagement_rate,impressions,likes,reach,saved,shares,total_interactions',
        };
    }
}
