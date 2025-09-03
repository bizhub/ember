<?php

namespace Domain\Ember\Data;

class ChatMessage
{
    public function __construct(
        public string $text,
        public string $conversationId,
    ) {}
}