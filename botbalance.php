<?php 
require 'vendor/autoload.php';

use TelegramBot\Api\BotApi;
use TelegramBot\Api\Types\Update;
use TelegramBot\Api\Types\Message;

$mysqli = new mysqli('localhost', 'root', '', 'telegram_bot_db');
if ($mysqli->connect_error) {
    die('Ошибка подключения (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
}

$bot = new BotApi('');

$lastUpdateId = 0;

while (true) {
    try {
        $updates = $bot->getUpdates($lastUpdateId + 1, 10, 30);

        foreach ($updates as $update) {
            $message = $update->getMessage();
            if ($message instanceof Message) {
                $chatId = $message->getChat()->getId();
                $text = $message->getText();

                $mysqli->begin_transaction();
                try {
                    $stmt = $mysqli->prepare("SELECT balance FROM users WHERE telegram_id = ?");
                    $stmt->bind_param("i", $chatId);
                    $stmt->execute();
                    $stmt->store_result();

                    if ($stmt->num_rows === 0) {
                        $stmt = $mysqli->prepare("INSERT INTO users (telegram_id, balance) VALUES (?, 0.00)");
                        $stmt->bind_param("i", $chatId);
                        $stmt->execute();
                        $balance = 0.00;
                    } else {
                        $stmt->bind_result($balance);
                        $stmt->fetch();
                    }
                    $stmt->close();

                    if (strtolower($text) === '/balance') {
                        $bot->sendMessage($chatId, "Ваш текущий баланс: $" . number_format($balance, 2));
                    } elseif (is_numeric(str_replace(',', '.', $text))) {
                        $amount = floatval(str_replace(',', '.', $text));

                        if ($amount < 0 && abs($amount) > $balance) {
                            $bot->sendMessage($chatId, "Недостаточно средств на счете.");
                        } else {
                            $newBalance = $balance + $amount;
                            $stmt = $mysqli->prepare("UPDATE users SET balance = ? WHERE telegram_id = ?");
                            $stmt->bind_param("di", $newBalance, $chatId);
                            $stmt->execute();
                            $stmt->close();

                            $bot->sendMessage($chatId, "Ваш текущий баланс: $" . number_format($newBalance, 2));
                        }
                    } else {
                        $bot->sendMessage($chatId, "Отправьте сумму в долларах для пополнения или списания со счета.");
                    }

                    $mysqli->commit();
                } catch (Exception $e) {
                    $mysqli->rollback();
                    $bot->sendMessage($chatId, "Произошла ошибка. Попробуйте еще раз.");
                }
            }

            $lastUpdateId = $update->getUpdateId();
        }
    } catch (\TelegramBot\Api\HttpException $e) {
        error_log("Telegram API Error: " . $e->getMessage());
        sleep(5);
    } catch (Exception $e) {
        error_log("Error: " . $e->getMessage());
        sleep(5);
    }

    sleep(1);
}
