<?php

declare(strict_types=1);

namespace App\Main\Domain\Enum;

enum UpdateHandleStatusEnum: int
{
    case PENDING = 0; // Update not handled. Update will be handled in next time.
    case IN_PROGRESS = 1; // Update handling in progress. Update partially handled.
    case FAILED = 3; // Update handle failed, it will be retried in next time. Max retries 5.
    case REJECTED = 4; // Update exhausted limit on retries. Update will not be handled in next time.
    case FULFILLED = 5; // Update successfully handled. Update stay in system with current status.
}
