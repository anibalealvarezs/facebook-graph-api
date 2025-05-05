<?php

namespace Anibalealvarezs\FacebookGraphApi\Enums;

/**
 * Enum for fields available per page permission for the /me and /me/accounts endpoints.
 */
enum MediaField: string
{
    case ALT_TEXT = 'alt_text';
    case BOOST_ADS_LIST = 'boost_ads_list';
    case BOOST_ELIGIBILITY_INFO = 'boost_eligibility_info';
    case CAPTION = 'caption';
    case COMMENTS_COUNT = 'comments_count';
    case ID = 'id';
    case IS_COMMENT_ENABLED = 'is_comment_enabled';
    case IS_SHARED_TO_FEED = 'is_shared_to_feed';
    case LEGACY_INSTAGRAM_MEDIA_ID = 'legacy_instagram_media_id';
    case LIKE_COUNT = 'like_count';
    case MEDIA_PRODUCT_TYPE = 'media_product_type';
    case MEDIA_TYPE = 'media_type';
    case MEDIA_URL = 'media_url';
    case OWNER = 'owner';
    case PERMALINK = 'permalink';
    case SHORTCODE = 'shortcode';
    case THUMBNAIL_URL = 'thumbnail_url';
    case TIMESTAMP = 'timestamp';
    case USERNAME = 'username';

    /**
     * Returns a comma-separated list of all enum values.
     *
     * @return string
     */
    public static function toCommaSeparatedList(): string
    {
        $enumValues = array_map(fn (self $field) => $field->value, self::cases());
        return implode(',', $enumValues);
    }
}