<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../bootstrap.php';

class TelegramServiceTest extends TestCase
{
    public function testGetMessages()
    {
        $messages = (new \App\TelegramService())
            ->getMessages();

        $this->assertInstanceOf(
            Generator::class, $messages
        );
        $this->assertContainsOnlyInstancesOf(
            \App\TelegramMessage::class, $messages
        );
    }

    public function testGetMessagesPagination()
    {
        $expectedCount = 100;
        $messages = (new \App\TelegramService())
            ->getMessages($expectedCount);

        $ids = array_map(
            fn($v) => $v->id, iterator_to_array($messages)
        );
        $ids = array_unique($ids);

        $this->assertCount(
            $expectedCount, $ids
        );
    }
}