<?php

declare(strict_types=1);

namespace VendingMachine;

use PHPUnit\Framework\TestCase;

final class VendingMachineTest extends TestCase
{
    /**
     * @var VendingMachine
     */
    private $vendingMachine;

    public function setUp(): void
    {
        $vm = new VendingMachine();
        $this->vendingMachine = $vm;
    }

    public function testServiceReturnsNoService(): void
    {
        foreach ($this->vendingMachine->availableChange as $available) {
            $this->assertEquals(VendingMachine::INITIALIZED_MONEY_COUNT, $available);
        }
        foreach ($this->vendingMachine->items as $item => $data) {
            $this->assertEquals(VendingMachine::INITIALIZED_ITEM_COUNT, $this->vendingMachine->items[$item]['count']);
        }

        $this->vendingMachine->service();

        foreach ($this->vendingMachine->availableChange as $available) {
            $this->assertEquals(VendingMachine::REFILLED_MONEY_COUNT, $available);
        }
        foreach ($this->vendingMachine->items as $item => $data) {
            $this->assertEquals(VendingMachine::REFILLED_ITEM_COUNT, $this->vendingMachine->items[$item]['count']);
        }

    }

    public function testReturnMoney()
    {
       $expected =  [
            'N' => 1,
            'DI' => 1,
            'Q' => 1,
            'D' => 1
        ];

       $this->vendingMachine->insert('N');
       $this->vendingMachine->insert('DI');
       $this->vendingMachine->insert('Q');
       $this->vendingMachine->insert('D');
       $result = $this->vendingMachine->returnMoney();

       $this->assertEquals($expected, $result);
    }

    public function testInsert()
    {
        $this->vendingMachine->insert('Q');
        $this->vendingMachine->insert('Q');
        $this->vendingMachine->insert('Q');

        $this->assertEquals(3, $this->vendingMachine->money['Q']);
    }

    public function testGet()
    {
        $this->vendingMachine->insert('D');
        $this->vendingMachine->insert('D');
        $this->vendingMachine->insert('D');
        $result = $this->vendingMachine->get('B');

        $this->assertEquals('B', $result);
    }
}
