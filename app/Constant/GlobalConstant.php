<?php

namespace App\Constant;

class GlobalConstant
{
    const PERMANENT = 'permanent';
    const CONTRACT = 'contract';
    const RESIDENT_STATUS = [self::PERMANENT, self::CONTRACT];

    const IN = 'in';
    const OUT = 'out';
    const PAYMENT_TYPES = [self::IN, self::OUT];
}
