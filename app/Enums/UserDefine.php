<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

final class UserDefine extends Enum
{
    const MIN_PASSWORD = 6;
    const MAX_PASSWORD = 20;
}
