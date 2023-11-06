<?php

namespace nyakunchev\cold_hot\Model;

use RedBeanPHP\R;

R::setup("sqlite:gameDB.db");

function insertDB($currentNumber)
{
    date_default_timezone_set("Europe/Moscow");

    $db = R::dispense('games');
    $db->dateTime = R::isoDateTime();
    $db->playerName = getenv("username");
    $db->secretNumber = $currentNumber;
    $db->gameResult = "Не закончено";
    return R::store($db);
}

function updateDB($id, $result)
{
    $db = R::load('games', $id);
    $db->game_result = $result;
    R::store($db);
}

function showList()
{
    $db = R::getAll('SELECT * FROM games');
    if (sizeof($db) !== 0) {
        foreach ($db as $row) {
            \cli\line("ID: $row[id]");
            \cli\line("Дата и время: $row[date_time]");
            \cli\line("Имя: $row[player_name]");
            \cli\line("Загаданное число: $row[secret_number]");
            \cli\line("Результат: $row[game_result]");
        }
    } else {
        \cli\line("Баа данных пуста.");
    }
}

function insertReplay($id, $turnResult)
{
    $db = R::dispense('turns');
    $db->gameID = $id;
    $db->turnResult = $turnResult;
    R::store($db);
}

function showReplay($id)
{
    $db = R::getAll("SELECT * FROM turns WHERE game_id = '$id'");
    if (sizeof($db) !== 0) {
        foreach ($db as $row) {
            \cli\line("$row[turn_result]");
        }
    } else {
        \cli\line("Отсутствуют данные по игре, либо ходы не совершались.");
    }
}

R::close();
