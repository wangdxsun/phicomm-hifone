<?php
namespace  Hifone\Handlers\Commands\Stat;

use Carbon\Carbon;
use Hifone\Commands\Credit\DailyStatCommand;
use Hifone\Models\DailyStat;
use Hifone\Services\Dates\DateFactory;

class DailyStatCommandHandler
{
    protected $dates;

    public function __construct(DateFactory $dates)
    {
        $this->dates = $dates;
    }

    public function handle(DailyStatCommand $command)
    {
        if ($this->checkFrequency($command)){
            return false;
        }
        $data = [
            'object_id'           => $command->id,
            'object_type'         => $command->getClass(),
            'created_at'          => Carbon::now()->toDateTimeString(),
        ];
        $daily_stat = DailyStat::create($data);
        return $daily_stat;
    }

    protected function checkFrequency($command)
    {

    }

}
