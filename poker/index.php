<?php

include './vendor/autoload.php';

$config = [
    'players' => [
        ['name' => 'Petr', 'account' => 1000],
        ['name' => 'Olya', 'account' => 2000],
        ['name' => 'Slava', 'account' => 5000],
    ],
    'bank' => 0,
    // входная ставка
    'ante' => 300,
    // минимальная ставка, устанавливается первым игроком на раунде
    'min' => 100,
    // тип холдема
    // безлимитный — максимальная ставка ограничена размером стека игрока.
    // лимитированный — ставки лимитированы.
    //с пот-лимитом — максимальная ставка ограничена размерами банка.
    'max' => 'nolimit',
];
//$game = new \Poker\Game($config);
//
//$game->flop();
//$game->turn();
//$game->river();
//$game->finish();
//dd($game);

$desk = new \Poker\Deck();
$desk->combo();