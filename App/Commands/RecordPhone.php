<?php

namespace App\Commands;

use App\Models\Record;
use App\Services\RecordStatusService;
use App\Services\UserStatusService;

class RecordPhone extends BaseCommand {

    function processCommand($par = false)
    {
        if ($this->user->status == UserStatusService::PHONE) {
            Record::where('user_id', $this->user->id)->where('status', RecordStatusService::FILLING)->update([
                'phone' => $this->parser::getMessage()
            ]);
            $this->triggerCommand(RecordAddress::class);
        } else {
            $this->user->status = UserStatusService::PHONE;
            $this->user->save();

            $this->tg->sendMessageWithKeyboard($this->text['enter_phone'], [[$this->text['cancel']]]);
        }
    }

}