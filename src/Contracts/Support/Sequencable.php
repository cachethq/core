<?php

namespace Cachet\Contracts\Support;

use Carbon\CarbonInterface;

interface Sequencable
{
    public function getSequenceTimestamp(): CarbonInterface;
}
