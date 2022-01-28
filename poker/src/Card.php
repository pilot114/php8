<?php

namespace Poker;

class Card
{
    const SUIT_DIAMONDS = 1; // ♦
    const SUIT_CLUBS    = 2; // ♣
    const SUIT_HEARTS   = 3; // ♥
    const SUIT_SPADES   = 4; // ♠

    const SUITS = [self::SUIT_DIAMONDS, self::SUIT_CLUBS, self::SUIT_HEARTS, self::SUIT_SPADES];

    const VALUE_ACE   = 1;
    const VALUE_TWO   = 2;
    const VALUE_THREE = 3;
    const VALUE_FOUR  = 4;
    const VALUE_FIVE  = 5;
    const VALUE_SIX   = 6;
    const VALUE_SEVEN = 7;
    const VALUE_EIGHT = 8;
    const VALUE_NINE  = 9;
    const VALUE_TEN   = 10;
    const VALUE_JACK  = 11;
    const VALUE_QUEEN = 12;
    const VALUE_KING  = 13;

    const VALUES = [
        self::VALUE_ACE, self::VALUE_TWO, self::VALUE_THREE, self::VALUE_FOUR, self::VALUE_FIVE, self::VALUE_SIX,
        self::VALUE_SEVEN, self::VALUE_EIGHT, self::VALUE_NINE, self::VALUE_TEN, self::VALUE_JACK, self::VALUE_QUEEN,
        self::VALUE_KING,
    ];

    public function __construct(protected int $suit, protected int $value)
    {
        if (!in_array($suit, self::SUITS) || !in_array($value, self::VALUES)) {
            throw new PokerException(sprintf('Неверные значения для карты: %s', $this->getId()));
        }
    }

    public function getId(): string
    {
        return $this->suit . ':' . $this->value;
    }

    public function getSuit(): int
    {
        return $this->suit;
    }

    public function getValue(): int
    {
        return $this->value;
    }
}