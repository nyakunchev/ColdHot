<?php

namespace nyakunchev\cold_hot\Controller;

use function nyakunchev\cold_hot\View\showGame;
use function nyakunchev\cold_hot\View\showList;
use function nyakunchev\cold_hot\View\showReplay;
use function nyakunchev\cold_hot\View\help;

function key($key)
{
    if ($key == "--new" || $key == "-n") {
        startGame();
    } elseif ($key == "--list" || $key == "-l") {
        showList();
    } elseif ($key == "--replay" || $key == "-r") {
        showReplay();
    } elseif ($key == "--help" || $key == "-h") {
        help();
    } else {
        echo "Неверный ключ.";
    }
}

function coldHot($numberArray, $currentNumber)
{
    for ($i = 0; $i < 3; $i++) {
        if ($numberArray[$i] == $currentNumber[$i]) {
            echo "Горячо!\n";
        } elseif (
            $numberArray[$i] == $currentNumber[0] ||
            $numberArray[$i] == $currentNumber[1] ||
            $numberArray[$i] == $currentNumber[2]
        ) {
            echo "Тепло!\n";
        } else {
            echo "Холодно!\n";
        }
    }
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
    $currentNumber = str_split($currentNumber);
    while ($number != $currentNumber) {
        $number = readline("Введите трехзначное число : ");
        if (is_numeric($number)) {
            if (strlen($number) != 3) {
                echo "Ошибка! Число должно быть трехзначным\n";
            } else {
                $numberArray = str_split($number);
                if ($numberArray == $currentNumber) {
                    echo "Вы выиграли!\n";
                    restart();
                } else {
                    coldHot($numberArray, $currentNumber);
                }
            }
        } else {
            echo "Ошибка! Введите число.\n";
        }
    }
}
