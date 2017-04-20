<?php

/*
 * This file is part of Hifone.
 *
 * (c) Hifone.com <hifone@hifone.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hifone\Handlers\Commands\Credit;

use Carbon\Carbon;
use Hifone\Commands\Credit\AddCreditCommand;
use Hifone\Models\Credit;
use Hifone\Models\CreditRule;
use Hifone\Services\Dates\DateFactory;

class AddCreditCommandHandler
{
    /**
     * The date factory instance.
     *
     * @var \Hifone\Services\Dates\DateFactory
     */
    protected $dates;

    /**
     * Create a new report issue command handler instance.
     *
     * @param \Hifone\Services\Dates\DateFactory $dates
     */
    public function __construct(DateFactory $dates)
    {
        $this->dates = $dates;
    }

    /**
     * Handle the report credit command.
     *
     * @param \Hifone\Commands\Credit\AddCreditCommand $command
     *
     * @return \Hifone\Models\Credit
     */
    public function handle(AddCreditCommand $command)
    {
        $creditRule = CreditRule::whereSlug($command->action)->first();

        if (!$creditRule || !$this->checkFrequency($creditRule, $command->user)) {
            return false;
        }
        $data = [
            'user_id'           => $command->user->id,
            'rule_id'           => $creditRule->id,
            'balance'           => $command->user->score + $creditRule->reward,
            'body'              => $creditRule->reward,
            'created_at'        => Carbon::now()->toDateTimeString(),
        ];
        // Create the credit
        $credit = Credit::create($data);

        $command->user->update(['score' => $credit->balance]);

        return $credit;
    }

    protected function checkFrequency(CreditRule $creditRule, $user)
    {
        if ($creditRule->type == CreditRule::NO_LIMIT) {
            return true;
        }
        if ($creditRule->type == CreditRule::ONCE) {
            $count = Credit::forUser($user->id)->where('rule_id', $creditRule->id)->count();
            return $count < 1;
        }
        if ($creditRule->type == CreditRule::DAILY) {
            $frequencyTag = Credit::generateFrequencyTag();
            $count = Credit::forUser($user->id)->where('rule_id', $creditRule->id)->where('frequency_tag', $frequencyTag)->count();
            return $count < $creditRule->times;
        }
        return false;
    }
}
