<?php

namespace App\Modules\Recipient\Constants;

final class RecipientConstants
{
    public const SUFFIXES = ['Jr', 'Sr', '2nd', '3rd', 'II', 'III', 'IV', 'V', 'VI'];

    public const TIN_TYPES = ['SSN', 'EIN', 'ITIN', 'ATIN'];

    public const FILE_TYPE_INDIVIDUAL = 'Individual';
    public const FILE_TYPE_BUSINESS = 'Business';
    public const FILE_TYPES = [self::FILE_TYPE_INDIVIDUAL, self::FILE_TYPE_BUSINESS];

    public const EMAIL_LANGUAGES = ['en', 'es', 'fr', 'de', 'zh', 'ja', 'pt', 'it'];

    public const PER_PAGE = 10;
}