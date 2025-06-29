<?php

namespace Anibalealvarezs\FacebookGraphApi\Enums;

/**
 * Enum for fields available per page permission for the /me and /me/accounts endpoints.
 */
enum CreativeField: string
{
    case ID = 'id';
    case NAME = 'name';
    case ACTOR_ID = 'actor_id';
    case TITLE = 'title';
    case BODY = 'body';
    case IMAGE_URL = 'image_url';
    case THUMBNAIL_URL = 'thumbnail_url';
    case OBJECT_STORY_SPEC = 'object_story_spec';
    case VIDEO_ID = 'video_id';
    case URL_TAGS = 'url_tags';
    case CALL_TO_ACTION_TYPE = 'call_to_action_type';
    case CALL_TO_ACTION_VALUE = 'call_to_action_value';

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