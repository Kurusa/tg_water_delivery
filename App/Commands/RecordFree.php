<?php

namespace App\Commands;

use App\Models\Record;
use App\Services\RecordStatusService;
use App\TgHelpers\GoogleClient;
use App\TgHelpers\TelegramKeyboard;
use Carbon\Carbon;

class RecordFree extends BaseCommand
{

    function processCommand($par = false)
    {
        if ($this->parser::getByKey('a') == 'per') {
            $this->tg->deleteMessage($this->parser::getMsgId());
            Record::where('user_id', $this->user->id)->where('status', RecordStatusService::FILLING)->update([
                'time' => $this->parser::getByKey('v')
            ]);
            $filling_record = Record::where('user_id', $this->user->id)->where('status', RecordStatusService::FILLING)->get();

            $google = new GoogleClient();
            $google->create($filling_record[0]['address'] . ' ' . $filling_record[0]['time'] . ' ' . $filling_record[0]['name'] . ' ' . $filling_record[0]['phone'] . ', бутылок: ' . $filling_record[0]['count'], $filling_record[0]['date'], $filling_record[0]['date']);

            Record::where('user_id', $this->user->id)->where('status', RecordStatusService::FILLING)->update([
                'status' => RecordStatusService::DONE
            ]);
            $this->triggerCommand(MainMenu::class, $this->text['thankyou']);

            foreach (explode(',', env('ADMIN_LIST')) as $admin) {
                $this->tg->sendMessage($filling_record[0]['address'] . ', ' . date('Y-m-d', $filling_record[0]['date']) . ' ' . $filling_record[0]['time'] . ', ' . $filling_record[0]['name']
                    . ' ' . $filling_record[0]['phone'] . ', бутылок: ' . $filling_record[0]['count'], $admin);
            }
        } else {
            foreach ($this->text['period'] as $period) {
                TelegramKeyboard::addButton($period['title'], [
                    'a' => $period['action'],
                    'v' => $period['v']
                ]);
            }
            $this->tg->sendMessageWithInlineKeyboard($this->text['select_period'], TelegramKeyboard::get());
        }
    }

}