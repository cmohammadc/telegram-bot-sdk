<?php

namespace Telegram\Bot\Answers;

use Telegram\Bot\Objects\Update;
use Telegram\Bot\Traits\Telegram;

/**
 * Class Answerable
 *
 * @method mixed replyWithMessage($use_sendMessage_parameters)       Reply Chat with a message. You can use all the sendMessage() parameters except chat_id.
 * @method mixed replyWithPhoto($use_sendPhoto_parameters)           Reply Chat with a Photo. You can use all the sendPhoto() parameters except chat_id.
 * @method mixed replyWithAudio($use_sendAudio_parameters)           Reply Chat with an Audio message. You can use all the sendAudio() parameters except chat_id.
 * @method mixed replyWithVideo($use_sendVideo_parameters)           Reply Chat with a Video. You can use all the sendVideo() parameters except chat_id.
 * @method mixed replyWithVoice($use_sendVoice_parameters)           Reply Chat with a Voice message. You can use all the sendVoice() parameters except chat_id.
 * @method mixed replyWithDocument($use_sendDocument_parameters)     Reply Chat with a Document. You can use all the sendDocument() parameters except chat_id.
 * @method mixed replyWithSticker($use_sendSticker_parameters)       Reply Chat with a Sticker. You can use all the sendSticker() parameters except chat_id.
 * @method mixed replyWithLocation($use_sendLocation_parameters)     Reply Chat with a Location. You can use all the sendLocation() parameters except chat_id.
 * @method mixed replyWithChatAction($use_sendChatAction_parameters) Reply Chat with a Chat Action. You can use all the sendChatAction() parameters except chat_id.
 */
trait Answerable
{
    use Telegram;

    /**
     * @var Update Holds an Update object.
     */
    protected $update;

    /**
     * Magic Method to handle all ReplyWith Methods.
     *
     * @param $method
     * @param $arguments
     *
     * @return mixed|string
     */
    public function __call($method, $arguments)
    {
        if (starts_with($method, 'replyWith')) {
            $reply_name = studly_case(substr($method, 9));
            $methodName = 'send' . $reply_name;

            $class = 'Telegram\Bot\Methods\\'.studly_case($methodName);

            if (!class_exists($class)) {
                throw new \BadMethodCallException("Method [$method] does not exist.");
            }

            if (null === $chat = $this->update->getChat())
            {
                throw new \BadMethodCallException("No chat available for reply with [$method].");
            }

            $chat_id = $chat->getId();

            $params = array_merge(compact('chat_id'), $arguments[0]);

            return call_user_func_array([$this->telegram, $methodName], [$params]);
        }

        throw new \BadMethodCallException("Method [$method] does not exist.");
    }

    /**
     * Returns Update object.
     *
     * @return Update
     */
    public function getUpdate()
    {
        return $this->update;
    }
}
