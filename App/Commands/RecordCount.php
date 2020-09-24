<?php

namespace App\Commands;

use App\Models\Record;
use App\Services\RecordStatusService;
use App\Services\UserStatusService;

class RecordCount extends BaseCommand {

    function processCommand($par = false)
    {
        if ($this->user->status == UserStatusService::COUNT) {
            Record::where('user_id', $this->user->id)->where('status', RecordStatusService::FILLING)->update([
                'count' => $this->parser::getMessage()
            ]);
            $this->triggerCommand(RecordMonths::class);
        } else {
            $this->user->status = UserStatusService::COUNT;
            $this->user->save();

            $this->tg->sendMessageWithKeyboard('Укажите количество бутылей в заказе?
1 бутыль = 18,9 литров', [[$this->text['cancel']]]);
        }
    }

}