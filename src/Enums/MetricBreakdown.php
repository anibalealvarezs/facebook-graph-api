<?php

namespace Anibalealvarezs\FacebookGraphApi\Enums;

enum MetricBreakdown: string
{
    case AGE = 'age';
    case GENDER = 'gender';
    case COUNTRY = 'country';
    case CITY = 'city';
    case FOLLOW_TYPE = 'follow_type';
    case MEDIA_PRODUCT_TYPE = 'media_product_type';
    case CONTACT_BUTTON_TYPE = 'contact_button_type';
}
