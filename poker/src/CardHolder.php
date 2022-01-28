<?php

namespace Poker;

/**
 * Кому можно раздать карты
 */
interface CardHolder
{
    public function addCard(Card $card);
}