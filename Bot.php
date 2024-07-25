<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/Get.php';
require_once __DIR__ . '/Markup.php';
require_once __DIR__ . '/Token.php';

use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;

class Bot {

    private function _gen_heroes_handler() {
        return function (Nutgram $bot) {
            $bot->sendMessage(
                text: 'Герои',
                reply_markup: $this->_markup->heroes(),
            );
        };
    }

    private function _gen_groups_handler() {
        return function (Nutgram $bot) {
            $this->cbdata = $bot->callbackQuery()->data;
            sscanf($this->cbdata, 'hero h %d', $this->hero_index);
            $bot->sendMessage(
                text: $this->_markup->get_heroes()[$this->hero_index],
                reply_markup: $this->_markup->groups($this->hero_index),
            );
        };
    }

    private function _gen_responses_handler() {
        return function (Nutgram $bot) {
            $this->cbdata = $bot->callbackQuery()->data;
            sscanf($this->cbdata, 'group h %d g %d', $this->hero_index, $this->group_index);
            $bot->sendMessage(
                text: $this->_markup->get_heroes()[$this->hero_index],
                reply_markup: $this->_markup->responses($this->hero_index, $this->group_index),
            );
        };
    }

    private function _gen_sounds_handler() {
        return function (Nutgram $bot) {
            $this->cbdata = $bot->callbackQuery()->data;
            
            sscanf($this->cbdata, 'resp h %d g %d r %d', $this->hero_index, $this->group_index, $this->response_index);
            $bot->sendAudio(
                $this->_markup->get_voices()[$this->response_index][1],
            );
            $bot->sendMessage(
                text: 'Еще?',
                reply_markup: InlineKeyboardMarkup::make()
                ->addRow(
                    InlineKeyboardButton::make('Да!', callback_data: 'start'),
                ),
            );
        };
    }

    public function __construct() {
        global $BOT_TOKEN;
        $this->_bot = new Nutgram($BOT_TOKEN);
        $this->_markup = new Markup();
        $this->_bot->onCommand('start', $this->_gen_heroes_handler());
        $this->_bot->onCallbackQueryData('start', $this->_gen_heroes_handler());
        $this->_bot->onCallbackQueryData('hero h {hero}', $this->_gen_groups_handler());
        $this->_bot->onCallbackQueryData('group h {hero} g {group}', $this->_gen_responses_handler());
        $this->_bot->onCallbackQueryData('resp h {hero} g {group} r {resp}', $this->_gen_sounds_handler());

        $this->_bot->run();
    }

    private Nutgram $_bot;
    private Markup $_markup;
    private $cbdata;
    private $hero_index;
    private $group_index;
    private $response_index;
}

$bot = new Bot();