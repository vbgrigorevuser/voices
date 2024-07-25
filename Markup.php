<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/Get.php';

use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;

class Markup {

    public function responses($hero_index, $group_index) {
        $group_responses = Get::group_responses($this->_heroes[$hero_index], $this->_groups[$group_index]);
        $this->_voices = $group_responses;
        $buttons = [];
        foreach ($group_responses as $idx => $response) {
            array_push($buttons, InlineKeyboardButton::make($response[0], callback_data: 'resp h '. strval($hero_index) . ' g ' . strval($idx) . ' r ' . strval($idx)));
        }
        $keyboard = InlineKeyboardMarkup::make();
        foreach ($buttons as $button) {
            $keyboard->addRow($button);
        }
        return $keyboard;
    }

    public function groups($hero_index) {
        $this->_groups = Get::groups($this->_heroes[$hero_index]);
        $buttons = [];
        foreach ($this->_groups as $idx => $group) {
            array_push($buttons, InlineKeyboardButton::make($group, callback_data: 'group h '. strval($hero_index) . ' g ' . strval($idx)));
        }
        $keyboard = InlineKeyboardMarkup::make();
        foreach ($buttons as $button) {
            $keyboard->addRow($button);
        }
        return $keyboard;
    }

    public function heroes() {
        $buttons = [];
        foreach ($this->_heroes as $idx => $hero) {
            array_push($buttons, InlineKeyboardButton::make($hero, callback_data: 'hero h ' . strval($idx)));
        }
        $keyboard = InlineKeyboardMarkup::make();
        foreach ($buttons as $button) {
            $keyboard->addRow($button);
        }
        return $keyboard;
    }

    public function __construct() {
        $this->_heroes = Get::heroes();
        $this->_groups = Get::groups();
    }

    public function get_voices() {
        return $this->_voices;
    }

    private array $_heroes;
    private array $_groups;
    private array $_voices;
}