<?php



require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/Get.php';
require_once __DIR__ . '/Markup.php';
require_once __DIR__ . '/Token.php';

use SergiX44\Nutgram\Conversations\Conversation;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;


class Dialog extends Conversation {
    public function heroes(Nutgram $bot) {
        $bot->sendMessage(
            text: 'Герои',
            reply_markup: $this->markup->heroes(),
        );
        $this->next('groups');
    }

    public function groups(Nutgram $bot) {
        $this->cbdata = $bot->callbackQuery()->data;
        sscanf($this->cbdata, 'h %d', $this->hero_index);
        $bot->sendMessage(
            text: 'Группы',
            reply_markup: $this->markup->groups($this->hero_index),
        );
        $this->next('responses');
    }

    public function responses(Nutgram $bot) {
        $this->cbdata = $bot->callbackQuery()->data;
        sscanf($this->cbdata, 'h %d g %d', $this->hero_index, $this->group_index);
        $bot->sendMessage(
            text: 'Реплики',
            reply_markup: $this->markup->responses($this->hero_index, $this->group_index),
        );
        $this->next('sound');
    }

    public function sound(Nutgram $bot) {
        $this->cbdata = $bot->callbackQuery()->data;
        
        sscanf($this->cbdata, 'h %d g %d r %d', $this->hero_index, $this->group_index, $this->response_index);
        $bot->sendAudio(
            $this->markup->get_voices()[$this->response_index][1],
        );
        $bot->sendMessage(
            text: 'Еще?',
            reply_markup: InlineKeyboardMarkup::make()
            ->addRow(
                InlineKeyboardButton::make('Да!', callback_data: 'yes'),
            ),
        );
        $this->next('heroes');
    }

    public function __construct() {
        $this->markup = new Markup();        
    }

    public Markup $markup;
    protected ?string $step = 'heroes';
    public $cbdata;
    public $hero_index;
    public $group_index;
    public $response_index;
}

class Bot {

    public function __construct() {
        global $BOT_TOKEN;
        $this->_bot = new Nutgram($BOT_TOKEN);
        $this->_bot->onCommand('start', Dialog::class);

        $this->_bot->run();
    }

    private Nutgram $_bot;
}

$bot = new Bot();