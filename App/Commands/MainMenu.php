<?php

namespace App\Commands;

use App\Models\Record;
use App\Services\RecordStatusService;
use App\Services\UserStatusService;

/**
 * Class MainMenu
 * @package App\Commands
 */
class MainMenu extends BaseCommand
{

    /**
     * @param bool $par
     */
    function processCommand($par = false)
    {
        // delete possible undone record
        $filling_record = Record::where('user_id', $this->user->id)->where('status', RecordStatusService::FILLING)->first();
        if ($filling_record) {
            $filling_record->delete();
        }

        $this->user->status = UserStatusService::DONE;
        $this->user->save();
        if ($this->parser::getMessage() == '/start') {
            $this->tg->sendMessage('Приветствуем!)✋ с помощью этого бота Вы сможете заказать доставку воды 5element💧 в городе Днепр.');
        }
        $this->tg->sendMessageWithKeyboard($par ?: $this->text['main_menu'], [
            [$this->text['create_record']]
        ]);
    }

}