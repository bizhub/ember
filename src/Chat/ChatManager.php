<?php

namespace Bizhub\Ember\Chat;

use Bizhub\Ember\Embedding\EmbeddingManager;
use Bizhub\Ember\Search\SearchManager;

class ChatManager
{
    protected string $prompt;

    public function __construct(
        protected EmbeddingManager $embedding,
        protected SearchManager $search,
    ) {}
    
    public function ask(string $question)
    {
        // Check if conversation exists
        // Create new conversation

        // Get messages for embedding with question
        $questionEmbedding = $this->embedding->create($conversationForEmbedding);

        // Similarity search
        $docs = $this->search->similar($questionEmbedding);

        // Create context
        // Create system prompt
        // Add conversation history to system prompt
        // Add question to conversation
        // AI Request
        // Add response to conversation
    }

    public function findOrCreateConversation()
    {
        //
    }
}
