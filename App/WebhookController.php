<?php

namespace App;

use App\Commands\MainMenu;
use App\Commands\Price;
use App\Services\Language\ChangeLanguageService;
use App\TgHelpers\TelegramApi;
use http\Client\Curl\User;

class WebhookController
{

    public function handle()
    {
        $update = \json_decode(file_get_contents('php://input'), TRUE);
        $isCallback = !array_key_exists('message', $update);
        $response = $isCallback ? $update['callback_query'] : $update;

        if ($isCallback) {
            $config = include('config/callback_commands.php');
            $action = \json_decode($response['data'], true)['a'];

            if (isset($config[$action])) {
                (new $config[$action]($response))->handle($response);
            }

            $tg = new TelegramApi();
            $tg->answerCallbackQuery($response['id']);
        } else {
            $handler_class_name = MainMenu::class;
            // checking commands -> keyboard commands -> mode -> exit
            if ($update['message']['text']) {
                $text = $update['message']['text'];
                if (strpos($text, '/') === 0) {
                    $handlers = include(__DIR__ . '/config/slash_commands.php');
                }

                if (isset($handlers[$text])) {
                    $handler_class_name = $handlers[$text];
                } else {
                    $key = $this->processKeyboardCommand($text);
                    $handlers = include(__DIR__ . '/config/keyboard_сommands.php');
                    if ($key && $handlers[$key]) {
                        $handler_class_name = $handlers[$key];
                    } else {
                        $handlers = include(__DIR__ . '/config/mode_сommands.php');

                        // first check if user exists, then check his status
                        $user = \App\Models\User::where('chat_id', $update['message']['chat']['id'])->first();
                        if ($user && isset($handlers[$user->status])) {
                            $handler_class_name = $handlers[$user->status];
                        }
                    }
                }
                (new $handler_class_name($update))->handle($update);
            }
        }
    }


    protected function processKeyboardCommand($text): ?string
    {
        $config = include(__DIR__ . '/config/bot.php');
        $translations = @array_flip($config);
        if (isset($translations[$text])) {
            return $translations[$text];
        }

        return null;
    }

}
