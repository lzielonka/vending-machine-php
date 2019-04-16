<?php

declare(strict_types=1);

namespace VendingMachine;

use PHPUnit\Runner\Exception;
use SebastianBergmann\Timer\RuntimeException;

final class VendingMachine {
    public const INITIALIZED_ITEM_COUNT = 10;
    public const INITIALIZED_MONEY_COUNT = 20;

    public const REFILLED_ITEM_COUNT = 20;
    public const REFILLED_MONEY_COUNT = 100;

    private $validMoney = [
        'N' => 0.05,
        'DI' => 0.1,
        'Q' => 0.25,
        'D' => 1
    ];

    public $items = [
        'A' => ['count' => self::INITIALIZED_ITEM_COUNT, 'price' => 0.65],
        'B' => ['count' => self::INITIALIZED_ITEM_COUNT, 'price' => 1],
        'C' => ['count' => self::INITIALIZED_ITEM_COUNT, 'price' => 1.5],
     ];

    public $availableChange = [
        'N' => self::INITIALIZED_MONEY_COUNT,
        'DI' => self::INITIALIZED_MONEY_COUNT,
        'Q' => self::INITIALIZED_MONEY_COUNT,
        'D' => self::INITIALIZED_MONEY_COUNT
    ];

    public $money = [];
    public $change = [];

    public function service()
    {
        foreach ($this->availableChange as $money => $count) {
            $this->availableChange[$money] = self::REFILLED_MONEY_COUNT;
        }

        foreach ($this->items as $item => $data) {
            $this->items[$item]['count'] = self::REFILLED_ITEM_COUNT;
        }
    }

    public function insert(string $money)
    {
        if (!array_key_exists($money, $this->validMoney)) {
            throw new RuntimeException('invalid money inserted');
        }

        $this->money[$money] = ($this->money[$money] ?? 0) + 1;
    }

    public function returnMoney()
    {
        $toReturn = $this->money;
        $this->money = [];

        return $toReturn;
    }

    public function get(string $item)
    {
        if ($this->items[$item]['count'] < 1) {
            throw new Exception('item out of stock');
        }
        $this->items[$item]['count'] -= 1;

        $change = $this->getInsertedAmount() - $this->items[$item]['price'];
        if ($change < 0) {
            throw new Exception('insufficient funds');
        }

        if ($change === 0) {
            return $item;
        }

        $this->calculateChange($change);

        return $item;
    }

    private function getInsertedAmount()
    {
        $sum = 0;
        foreach ($this->money as $money => $count) {
            $sum += $this->validMoney[$money] * $count;
        }
        return $sum;
    }

    /**
     * @param $change
     */
    public function calculateChange($change): void
    {
        foreach (['D', 'Q', 'N', 'DI'] as $money) {
            if (!isset($this->money[$money])) {
                continue;
            }
            $this->change[$money] = min($this->money[$money] % $this->validMoney[$money], $this->availableChange[$money]);
            $change -= $this->change[$money] * $this->validMoney[$money];
        }
    }
}
