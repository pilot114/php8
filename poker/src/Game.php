<?php

namespace Poker;

/**
 * Контроль правил игры в покер
 */
class Game
{
    protected array $players = [];
    protected Deck  $deck;
    protected Table $table;

    protected int $min = 0;

    public function __construct(array $config)
    {
        $this->deck = new \Poker\Deck();

        // рассаживаем игроков
        foreach ($config['players'] as $player) {
            $this->players[] = new \Poker\Player(new \Poker\CardSet(), $player['account']);
        }

        $this->table = new \Poker\Table(new \Poker\CardSet(), $config['bank']);

        // собираем начальную ставку
        foreach ($this->players as $player) {
            $player->pay($config['ante']);
            $this->table->addToBank($config['ante']);
        }

        // первые 2 игрока делают доп.ставку (малый и большой блайнд)
        $min = $config['min'];
        $this->players[0]->pay($min / 2);
        $this->table->addToBank($min / 2);
        $this->players[1]->pay($min);
        $this->table->addToBank($min);
        $this->min = $config['min'];

        // сдаем по 2 карты
        foreach ($this->players as $player) {
            $this->deck->handOver($player);
            $this->deck->handOver($player);
        }

    }

    public function flop()
    {
        $this->deck->handOver($this->table);
        $this->deck->handOver($this->table);
        $this->deck->handOver($this->table);
    }

    public function turn()
    {
        $this->round();
        $this->deck->handOver($this->table);

        // если лимитированный холдем
        //$min *= 2;
    }

    public function river()
    {
        $this->round();
        $this->deck->handOver($this->table);
        $this->round();
    }

    /**
     * Считаем кто выиграл, распределяем банк
     */
    public function finish()
    {
    }

    /**
     * Раунд торгов
     */
    protected function round(array $playerMoves = [])
    {
        foreach ($playerMoves as $i => $playerMove) {
            // ставка
            if ($playerMove['type'] === 'bet') {
                $this->min = $playerMove['value'];
                $this->players[$i]->pay($this->min);
                $this->table->addToBank($this->min);
            }
            // повышение
            if ($playerMove['type'] === 'raise') {
                $this->min = $playerMove['value'];
                $this->players[$i]->pay($this->min);
                $this->table->addToBank($this->min);
            }
            // уравнять
            if ($playerMove['type'] === 'call') {
                $this->players[$i]->pay($this->min);
                $this->table->addToBank($this->min);
            }
            // Передать ход
            if ($playerMove['type'] === 'check') {
                // идем дальше (если еще не было ставок)
            }
            // Выйти из игры
            if ($playerMove['type'] === 'fold') {
                // Сбросить карты и потерять уже поставленные деньги
            }
        }
    }
}