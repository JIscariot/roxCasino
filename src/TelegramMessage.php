<?php

namespace App;

class TelegramMessage
{
    public function __construct(
        public ?int $id = null,
        public ?string $text = null,
        public ?string $viewsCount = null,
        public ?string $previewLink = null,
        public ?array $keyboards = null,
        public ?\DateTimeImmutable $publishedAt = null,
    )
    {
    }
}