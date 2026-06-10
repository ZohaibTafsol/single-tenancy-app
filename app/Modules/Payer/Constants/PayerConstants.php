<?php

namespace App\Modules\Payer\Constants;

final class PayerConstants
{
    // File types
    const FILE_TYPE_INDIVIDUAL = 'Individual';
    const FILE_TYPE_BUSINESS   = 'Business';
    const FILE_TYPES           = [self::FILE_TYPE_INDIVIDUAL, self::FILE_TYPE_BUSINESS];

    // ID types
    const ID_TYPE_SSN = 'SSN';
    const ID_TYPE_EIN = 'EIN';
    const ID_TYPES    = [self::ID_TYPE_SSN, self::ID_TYPE_EIN];

    // Suffixes
    const SUFFIXES = ['Jr', 'Sr', '2nd', 'C3rd', 'II', 'III', 'IV', 'V', 'VI'];

    // Pagination
    const PER_PAGE = 10;
}
