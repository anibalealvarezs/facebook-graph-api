<?php

namespace Anibalealvarezs\FacebookGraphApi\Enums;

/**
 * Enum for fields available per page permission for the /me and /me/accounts endpoints.
 */
enum FacebookPostField: string
{
    case ID = 'id';
    case ACTIONS = 'actions';
    case ADMIN_CREATOR = 'admin_creator';
    case ALLOWED_ADVERTISING_OBJECTIVES = 'allowed_advertising_objectives';
    case APPLICATION = 'application';
    case BACKDATED_TIME = 'backdated_time';
    case CALL_TO_ACTION = 'call_to_action';
    case CAN_REPLY_PRIVATELY = 'can_reply_privately';
    case CHILD_ATTACHMENTS = 'child_attachments';
    case COORDINATES = 'coordinates';
    case CREATED_TIME = 'created_time';
    case EVENT = 'event';
    case EXPANDED_HEIGHT = 'expanded_height';
    case EXPANDED_WIDTH = 'expanded_width';
    case FEED_TARGETING = 'feed_targeting';
    case FROM = 'from';
    case FULL_PICTURE = 'full_picture';
    case HEIGHT = 'height';
    case ICON = 'icon';
    case IS_APP_SHARE = 'is_app_share';
    case IS_ELIGIBLE_FOR_PROMOTION = 'is_eligible_for_promotion';
    case IS_EXPIRED = 'is_expired';
    case IS_HIDDEN = 'is_hidden';
    case IS_INLINE_CREATED = 'is_inline_created';
    case IS_POPULAR = 'is_popular';
    case IS_PUBLISHED = 'is_published';
    case IS_SPHERICAL = 'is_spherical';
    case MESSAGE = 'message';
    case MESSAGE_TAGS = 'message_tags';
    case MULTI_SHARE_END_CARD = 'multi_share_end_card';
    case MULTI_SHARE_OPTIMIZED = 'multi_share_optimized';
    case PARENT_ID = 'parent_id';
    case PERMALINK_URL = 'permalink_url';
    case PLACE = 'place';
    case PRIVACY = 'privacy';
    case PROMOTABLE_ID = 'promotable_id';
    case PROPERTIES = 'properties';
    case SCHEDULED_PUBLISH_TIME = 'scheduled_publish_time';
    case SHARES = 'shares';
    case STATUS_TYPE = 'status_type';
    case STORY = 'story';
    case STORY_TAGS = 'story_tags';
    case SUBSCRIBED = 'subscribed';
    case TARGET = 'target';
    case TARGETING = 'targeting';
    case TIMELINE_VISIBILITY = 'timeline_visibility';
    case UPDATED_TIME = 'updated_time';
    case VIA = 'via';
    case WIDTH = 'width';
    case ATTACHMENTS = 'attachments';
    case COMMENTS = 'comments';
    case REACTIONS = 'reactions';
    case DYNAMIC_POSTS = 'dynamic_posts';
    case SHAREDPOSTS = 'sharedposts';
    case SPONSOR_TAGS = 'sponsor_tags';
    case TO = 'to';

    /**
     * Returns a comma-separated list of all enum values.
     *
     * @param bool $ignoreOptional
     * @return string
     */
    public static function toCommaSeparatedList(bool $ignoreOptional = true): string
    {
        $enumValues = array_map(
            fn (self $field) => $field->value,
            array_diff(
                self::cases(),
                $ignoreOptional ?
                    [
                        self::ATTACHMENTS,
                        self::COMMENTS,
                        self::REACTIONS,
                        self::DYNAMIC_POSTS,
                        self::SHAREDPOSTS,
                        self::SPONSOR_TAGS,
                        self::TO,
                    ] :
                    [])
        );
        return implode(',', $enumValues);
    }
}