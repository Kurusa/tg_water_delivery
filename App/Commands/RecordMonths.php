<?php

namespace App\Commands;

use App\Models\Record;
use App\Services\RecordStatusService;
use App\TgHelpers\GoogleClient;
use App\TgHelpers\TelegramKeyboard;
use Carbon\Carbon;

class RecordMonths extends BaseCommand
{

    function processCommand($par = false)
    {
        if ($this->parser::getByKey('a') == 'month') {
            Record::where('user_id', $this->user->id)->where('status', RecordStatusService::FILLING)->update([
                'date' => $this->parser::getByKey('s')
            ]);
            $this->tg->deleteMessage($this->parser::getMsgId());
            $this->triggerCommand(RecordFree::class);
        } else {
            $array = [];
            for ($i = 0; $i <= 3; $i++) {
                $array[] = [
                    'title' => Carbon::now()->addDays($i)->toDateString(),
                    'callback' => [
                        'a' => 'month',
                        's' => Carbon::now()->addDays($i)->startOfDay()->timestamp,
                    ]
                ];
            }
            TelegramKeyboard::$list = $array;
            TelegramKeyboard::$button_title = 'title';
            TelegramKeyboard::$callback_data = 'callback';
            TelegramKeyboard::$columns = 3;
            TelegramKeyboard::build();

            $this->tg->sendMessageWithInlineKeyboard($this->text['select_month_day'], TelegramKeyboard::get());
        }
    }

}