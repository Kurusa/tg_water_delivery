<?php

namespace App\Commands;

class CancelRecord extends BaseCommand {

    function processCommand($par = false)
    {
        $this->tg->sendMessage($this->text['sorry']);
        $this->triggerCommand(MainMenu::class);
    }

}