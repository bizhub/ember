<?php

namespace Bizhub\Ember;

use Bizhub\Ember\Chat\ChatManager;
use Bizhub\Ember\Embedding\EmbeddingManager;
use Bizhub\Ember\Memory\MemoryManager;
use Bizhub\Ember\Memory\QdrantClient;
use Bizhub\Ember\Search\SearchManager;

class Ember
{
    public static function embedding(): EmbeddingManager
    {
        return new EmbeddingManager;
    }

    public static function memory(): MemoryManager
    {
        return new MemoryManager(
            client: new QdrantClient,
        );
    }

    public static function search(): SearchManager
    {
        return new SearchManager(
            client: new QdrantClient,
        );
    }

    public static function chat(): ChatManager
    {
        return new ChatManager(
            embedding: self::embedding(),
            search: self::search(),
        );
    }
}
