<?php

namespace Poker;

class Table implements CardHolder
{
    public function __construct(
        protected CardSet $cards,
        protected int $bank,
    ){}

    public function addCard(Card $card)
    {
        $this->cards->addCard($card);
    }

    public function addToBank(int $value)
    {
        $this->bank += $value;
    }
}