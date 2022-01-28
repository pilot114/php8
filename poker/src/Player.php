<?php

namespace Poker;

class Player implements CardHolder
{
    public function __construct(
        protected CardSet $cards,
        protected int $account,
    ){}

    public function addCard(Card $card)
    {
        $this->cards->addCard($card);
    }

    /**
     * начальный взнос (анте)
     */
    public function pay(int $value): bool
    {
        if ($this->account > $value) {
            $this->account -= $value;
            return true;
        }
        return false;
    }
}