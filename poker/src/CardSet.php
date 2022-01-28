<?php

namespace Poker;

class CardSet
{
    protected array $cards = [];

    public function addCard(Card $card)
    {
        if (isset($this->cards[$card->getId()])) {
            throw new PokerException(sprintf('В наборе уже есть карта %s', $card->getId()));
        }
        $this->cards[$card->getId()] = $card;
    }

    public function removeCard(Card $card)
    {
        if (!isset($this->cards[$card->getId()])) {
            throw new PokerException(sprintf('В наборе нет карты %s', $card->getId()));
        }
        unset($this->cards[$card->getId()]);
    }

    /**
     * Получение максимальной комбинации
     * Старшинство комбинаций по возрастанию:
    Кикер. Старшая карта.
    Пара. Две карты одного достоинства.
    Две пары. Две карты одного достоинства, две карты другого достоинства.
    Сет. Три карты одного достоинства.
    Стрит. Пять карт, которые выстроились по старшинству.
    Флеш. Пять карт одной масти.
    Фулл-хаус. Три плюс два.
    Каре. Четыре карты одного достоинства.
    Стрит-флеш. Пять карт одной масти, которые выстроились по старшинству.
    Роял-флеш. Пять карт от 10 до туза одной масти.
     */
    public function combo()
    {
        foreach ($this->cards as $card) {
            dump($card->getSuit());
            dump($card->getValue());
        }
    }
}