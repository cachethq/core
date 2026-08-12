<?php

namespace Cachet\Http\Controllers\StatusPage;

use Cachet\Badger\Badger;
use Cachet\Http\BadgeResponseFactory;
use Cachet\Status;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class StatusBadgeController
{
    /**
     * Render a badge for the status page's current system status.
     */
    public function __invoke(Request $request, Status $status, Badger $badger, BadgeResponseFactory $badgeResponseFactory): Response
    {
        $systemStatus = $status->current();

        return $badgeResponseFactory->make(
            $badger->generate('status', $systemStatus->getLabel(), $systemStatus->getBadgeColor(), $badgeResponseFactory->style($request)),
        );
    }
}
