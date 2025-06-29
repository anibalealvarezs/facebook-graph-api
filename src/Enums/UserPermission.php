<?php

namespace Anibalealvarezs\FacebookGraphApi\Enums;

/**
 * Enum for fields available per user permission for the /me endpoint.
 */
enum UserPermission: string
{
    case PUBLIC_PROFILE = 'public_profile';
    case EMAIL = 'email';
    case USER_BIRTHDAY = 'user_birthday';
    case USER_GENDER = 'user_gender';
    case USER_HOMETOWN = 'user_hometown';
    case USER_LOCATION = 'user_location';
    case USER_CURRENCY = 'user_currency';
    case USER_EDUCATION_HISTORY = 'user_education_history';
    case USER_WORK_HISTORY = 'user_work_history';
    case USER_FRIENDS = 'user_friends';
    case USER_LIKES = 'user_likes';
    case USER_RELATIONSHIP_DETAILS = 'user_relationship_details';
    case USER_RELATIONSHIPS = 'user_relationships';
    case USER_POSTS = 'user_posts';
    case USER_RELIGION_POLITICS = 'user_religion_politics';
    case USER_AGE_RANGE = 'user_age_range';

    public function fields(): string
    {
        return match ($this) {
            self::PUBLIC_PROFILE => 'id,name,first_name,last_name,middle_name,picture,link,name_format,third_party_id,updated_time,verified',
            self::EMAIL => 'email',
            self::USER_BIRTHDAY => 'birthday',
            self::USER_GENDER => 'gender',
            self::USER_HOMETOWN => 'hometown',
            self::USER_LOCATION => 'location,locale,timezone',
            self::USER_CURRENCY => 'currency',
            self::USER_EDUCATION_HISTORY => 'education',
            self::USER_WORK_HISTORY => 'work',
            self::USER_FRIENDS => 'friends',
            self::USER_LIKES => 'languages,favorite_teams,favorite_athletes,inspirational_people,sports',
            self::USER_RELATIONSHIP_DETAILS => 'interested_in,meeting_for',
            self::USER_RELATIONSHIPS => 'relationship_status,significant_other',
            self::USER_POSTS => 'political,quotes,public_key',
            self::USER_RELIGION_POLITICS => 'religion',
            self::USER_AGE_RANGE => 'age_range',
        };
    }
}