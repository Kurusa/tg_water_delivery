<?php

namespace App\Commands;

use App\Models\Record;
use App\Services\RecordStatusService;
use App\Services\UserStatusService;

class RecordUserName extends BaseCommand {

    function processCommand($par = false)
    {
        if ($this->user->status == UserStatusService::USER_NAME) {
            Record::create([
                'user_id' => $this->user->id,
                'name' => $this->parser::getMessage(),
                'status' => RecordStatusService::FILLING
            ]);
            $this->triggerCommand(RecordPhone::class);
        } else {
            $this->user->status = UserStatusService::USER_NAME;
            $this->user->save();

            $this->tg->sendMessageWithKeyboard($this->text['enter_user_name'], [[$this->text['cancel']]]);
        }
    }

}