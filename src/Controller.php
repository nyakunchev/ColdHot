<?php

namespace nyakunchev\cold_hot\Controller;

use SQLite3;

use function nyakunchev\cold_hot\View\showGame;
use function nyakunchev\cold_hot\View\help;

function key($key, $id)
{
    if ($key == "--new" || $key == "-n") {
        startGame();
    } elseif ($key == "--list" || $key == "-l") {
        showList();
    } elseif ($key == "--replay" || $key == "-r") {
        showReplay($id);
    } elseif ($key == "--help" || $key == "-h") {
        help();
    } else {
        echo "Неверный ключ.";
    }
}


function coldHot($numberArray, $currentNumber)
{
    $result = "Исходы:";
    for ($i = 0; $i < 3; $i++) {
        if ($numberArray[$i] == $currentNumber[$i]) {
            $result .= " Горячо!;";
            echo "Горячо!\n";
        } elseif (
            $numberArray[$i] == $currentNumber[0] ||
            $numberArray[$i] == $currentNumber[1] ||
            $numberArray[$i] == $currentNumber[2]
        ) {
            $result .= " Тепло!;";
            echo "Тепло!\n";
        } else {
            $result .= " Холодно!;";
            echo "Холодно!\n";
        }
    }
    return $result;
}

function restart()
{
    $restart = readline("Хотите сыграть ещё?[Y/N]\n");
    if ($restart == "Y") {
        startGame();
    } else {
        exit;
    }
}

function startGame()
{
    showGame();
    $number = 0;
    $currentNumber = random_int(100, 999);
    $db = insertDB($currentNumber);
    $turn = 0;

    $currentNumber = str_split($currentNumber);
    $id = $db->querySingle("SELECT gameId FROM games ORDER BY gameId DESC LIMIT 1");

    while ($number != $currentNumber) {
        $number = readline("Введите трехзначное число : ");
        if (is_numeric($number)) {
            if (strlen($number) != 3) {
                echo "Ошибка! Число должно быть трехзначным\n";
            } else {
                $numberArray = str_split($number);
                if ($numberArray == $currentNumber) {
                    echo "Вы выиграли!\n";
                    $result = "Победа";
                    updateDB($id, $result);
                    $turn++;
                    $turnRes = coldHot($numberArray, $currentNumber);
                    $turnResult = $turn . " | " . $number . " | " . $turnRes;
                    insertReplay($id, $turnResult);
                    restart();
                } else {
                    $turn++;
                    $turnRes = coldHot($numberArray, $currentNumber);
                    $turnResult = $turn . " | " . $number . " | " . $turnRes;
                    insertReplay($id, $turnResult);
                }
            }
        } else {
            echo "Ошибка! Введите число.\n";
        }
    }
}

function openDB()
{
    if (!file_exists("gameDB.db")) {
        $db = createDB();
    } else {
        $db = new SQLite3("gameDB.db");
    }
    return $db;
}

function createDB()
{
    $db = new SQLite3("gameDB.db");

    $game = "CREATE TABLE games(
        gameId INTEGER PRIMARY KEY,
        gameDate DATE,
        gameTime TIME,
        playerName TEXT,
        secretNumber INTEGER,
        gameResult TEXT
    )";
    $db->exec($game);

    $turns = "CREATE TABLE info(
        gameId INTEGER,
        gameResult TEXT
    )";
    $db->exec($turns);

    return $db;
}

function insertDB($currentNumber)
{
    $db = openDB();

    date_default_timezone_set("Europe/Moscow");
    $gameData = date("d") . "." . date("m") . "." . date("Y");
    $gameTime = date("H") . ":" . date("i") . ":" . date("s");
    $playerName = getenv("username");

    $db->exec("INSERT INTO games (
        gameDate, 
        gameTime,
        playerName,
        secretNumber,
        gameResult
        ) VALUES (
        '$gameData', 
        '$gameTime',
        '$playerName',
        '$currentNumber',
        'Не закончено'
        )");

    return $db;
}

function updateDB($id, $result)
{
    $db = openDB();
    $db -> exec("UPDATE games
        SET gameResult = '$result'
        WHERE gameId = '$id'");
}

function showList()
{
    $db = openDB();
    $query = $db->query('SELECT Count(*) FROM games');
    $DBcheck = $query->fetchArray();
    $query = $db->query('SELECT * FROM games');
    if ($DBcheck[0] != 0) {
        while ($row = $query->fetchArray()) {
            \cli\line("ID $row[0])\n Дата: $row[1]\n Время: $row[2] 
 Имя: $row[3]\n Загаданное число: $row[4]\n Результат: $row[5]");
        }
    } else {
        \cli\line("База данных пуста.");
    }
}

function insertReplay($id, $turnResult)
{
    $db = openDB();
    $db -> exec("INSERT INTO info (
    gameID,
    gameResult
    ) VALUES (
    '$id',
    '$turnResult')");
}

function showReplay($id)
{
    $db = openDB();
    $query = $db->query("SELECT Count(*) FROM info WHERE gameID = '$id'");
    $DBcheck = $query->fetchArray();
    if ($DBcheck[0] != 0) {
        \cli\line("Повтор игры с id = " . $id);
        $query = $db->query("SELECT gameResult FROM info WHERE gameID = '$id'");
        while ($row = $query->fetchArray()) {
            \cli\line("$row[0]");
        }
    } else {
        \cli\line("База данных пуста, либо не правильный id игры.");
    }
}
