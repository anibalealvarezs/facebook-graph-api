<?php

namespace Anibalealvarezs\FacebookGraphApi\Enums;

/**
 * Enum for fields available per page permission for the /me and /me/accounts endpoints.
 */
enum MediaEdge: string
{
    case CHILDREN = 'children';
    case COLLABORATORS = 'collaborators';
    case COMMENTS = 'comments';
    case INSIGHTS = 'insights';

    public function fields(MediaProductType $mediaProductTypes = MediaProductType::FEED): string
    {
        return match ($this) {
            self::CHILDREN => InstagramMediaField::toCommaSeparatedList(),
            self::COLLABORATORS => UserPermission::PUBLIC_PROFILE->fields(),
            self::COMMENTS => CommentField::toCommaSeparatedList(),
            self::INSIGHTS => $mediaProductTypes->insightsFields(),
        };
    }
}