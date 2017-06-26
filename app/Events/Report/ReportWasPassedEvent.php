<?php

/*
 * This file is part of Hifone.
 *
 * (c) Hifone.com <hifone@hifone.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hifone\Events\Report;

use Hifone\Models\Report;

final class ReportWasPassedEvent implements ReportEventInterface
{
    /**
     * The report of thread or reply that has been reported by someone.
     *
     * @var \Hifone\Models\Thread
     */
    public $report;

    public function __construct(Report $report)
    {
        $this->report = $report;
    }
}
