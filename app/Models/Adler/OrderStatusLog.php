<?php

namespace App\Models\Adler;

use App\Models\Common\OrderStatusLogBase;

class OrderStatusLog extends OrderStatusLogBase
{
    protected $connection = 'adler';
}
