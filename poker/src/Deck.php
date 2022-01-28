<?php

namespace Poker;

class Deck extends CardSet
{
    public function __construct()
    {
        foreach (Card::SUITS as $suit) {
            foreach (Card::VALUES as $value) {
                $this->addCard(new Card($suit, $value));
            }
        }
    }

    /**
     * Сдать карту
     */
    public function handOver(CardHolder $holder)
    {
        if (empty($this->cards)) {
            throw new PokerException('Колода пуста');
        }

        $card = $this->cards[array_rand($this->cards)];
        $this->removeCard($card);
        $holder->addCard($card);
    }
}