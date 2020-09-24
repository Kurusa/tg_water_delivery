<?php
return [
    \App\Services\UserStatusService::USER_NAME => \App\Commands\RecordUserName::class,
    \App\Services\UserStatusService::PHONE => \App\Commands\RecordPhone::class,
    \App\Services\UserStatusService::ADDRESS => \App\Commands\RecordAddress::class,
    \App\Services\UserStatusService::COUNT => \App\Commands\RecordCount::class,
];