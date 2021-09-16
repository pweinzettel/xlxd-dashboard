<?php
include_once './secured/functions.php';

//https://core.telegram.org/bots/api#markdownv2-style

function tg_send($chat, $msg) {
    $apiToken = get_opt('TGtoken');
    $data = [
        'chat_id' => $chat,
        'text' => $msg,
        'parse_mode' => 'HTML',
    ];
    return @file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data), false, stream_context_create(['http' => ['ignore_errors' => true]]));
}

?>
