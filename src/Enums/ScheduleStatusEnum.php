<?php

namespace Cachet\Enums;

enum ScheduleStatusEnum: int
{
    case upcoming = 0;
    case in_progress = 1;
    case complete = 2;
}
