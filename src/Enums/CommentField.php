<?php

namespace Anibalealvarezs\FacebookGraphApi\Enums;

/**
 * Enum for fields available per page permission for the /me and /me/accounts endpoints.
 */
enum CommentField: string
{
    case FROM = 'from';
    case HIDDEN = 'hidden';
    case ID = 'id';
    case LEGACY_INSTAGRAM_COMMENT_ID = 'legacy_instagram_comment_id';
    case MEDIA = 'media';
    case PARENT_ID = 'parent_id';
    case REPLIES = 'replies';
    case TEXT = 'text';
    case TIMESTAMP = 'timestamp';
    case USER = 'user';

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