<?php

namespace App\Commands;

use App\Models\Record;
use App\Services\RecordStatusService;
use App\Services\UserStatusService;

class RecordAddress extends BaseCommand {

    function processCommand($par = false)
    {
        if ($this->user->status == UserStatusService::ADDRESS) {
            Record::where('user_id', $this->user->id)->where('status', RecordStatusService::FILLING)->update([
                'address' => $this->parser::getMessage()
            ]);
            $this->triggerCommand(RecordCount::class);
        } else {
            $this->user->status = UserStatusService::ADDRESS;
            $this->user->save();

            $this->tg->sendMessageWithKeyboard($this->text['enter_address'], [[$this->text['cancel']]]);
        }
    }

}