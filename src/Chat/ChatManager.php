<?php

namespace Bizhub\Ember\Chat;

use Bizhub\Ember\Embedding\EmbeddingManager;
use Bizhub\Ember\Enums\ConversationRole;
use Bizhub\Ember\Models\ConversationMessage;
use Bizhub\Ember\Search\SearchManager;
use Domain\Ember\Data\ChatMessage;
use Illuminate\Support\Facades\View;
use Prism\Prism\Enums\Provider;
use Prism\Prism\Prism;
use Ramsey\Uuid\Uuid;

class ChatManager
{
    protected $systemPrompt;
    protected ?int $userId = null;
    protected array $tools = [];
    protected ?string $conversationId = null;

    public function __construct(
        protected EmbeddingManager $embedding,
        protected SearchManager $search,
    ) {}

    public function withSystemPrompt(string|callable $prompt): self
    {
        $this->systemPrompt = $prompt;

        return $this;
    }

    public function withSystemPromptView(string $view, array $data = []): self
    {
        return $this->withSystemPrompt(function (string $context) use ($view, $data) {
            $payload = array_merge($data, [
                'context' => $context ?: 'No relevant knowledge found — answer from general knowledge and conversation so far.',
            ]);

            return View::make($view, $payload)->render();
        });
    }

    protected function resolveSystemPrompt(string $context, array $history = []): string
    {
        if (is_callable($this->systemPrompt)) {
            $systemPrompt = (string) call_user_func($this->systemPrompt, $context);
        } elseif ($this->systemPrompt) {
            $systemPrompt = str_replace(
                '{context}',
                $context ?: 'No relevant knowledge found — answer from general knowledge and conversation so far.',
                $this->systemPrompt,
            );
        } else {
            $systemPrompt = $context ?: 'No relevant knowledge found.';
        }

        if ($history) {
            $systemPrompt .= "\nUse the conversation history:\n<conversation>"
                . implode("\n", $history)
                . "</conversation>";
        }

        return $systemPrompt;
    }

    public function forUser($user): self
    {
        $this->userId = $user->id ?? $user;

        return $this;
    }

    public function withTools(array $tools): self
    {
        $this->tools = $tools;

        return $this;
    }

    public function forConversation(?string $conversationId): self
    {
        $this->conversationId = $conversationId;

        return $this;
    }
    
    public function ask(string $question): ChatMessage
    {
        $conversationId = $this->conversationId ?? Uuid::uuid4()->toString();

        $conversationMessages = ConversationMessage::query()
            ->where('conversation_id', $conversationId)
            ->when($this->userId, fn($query) => $query->where('user_id', $this->userId), fn($query) => $query->whereNull('user_id'))
            ->latest()
            ->limit(10)
            ->get()
            ->reverse();

        $conversationForEmbedding = $conversationMessages->isNotEmpty()
            ? $conversationMessages->pluck('content')->implode("\n")
            : $question;

        $questionEmbedding = $this->embedding->create($conversationForEmbedding);
        $searchResult = $this->search->similar($questionEmbedding);
        $contextText = '';

        foreach ($searchResult as $doc) {
            $score = $doc['score'] ?? 0;
            $summary = $doc['payload']['summary'] ?? '';

            if ($score < 0.5) continue;

            $contextText .= $summary . "\n\n";
        }

        $history = $conversationMessages
            ->take(-6)
            ->map(fn($msg) => ($msg->role === ConversationRole::User ? 'User: ' : 'Assistant: ') . $msg->content)
            ->toArray();

        $output = Prism::text()
            ->using(Provider::Gemini, 'gemini-2.0-flash')
            ->withTools($this->tools)
            ->withMaxSteps(2)
            ->usingTemperature(0)
            ->withSystemPrompt(
                $this->resolveSystemPrompt($contextText, $history),
            )
            ->withPrompt($question)
            ->asText();

        ConversationMessage::create([
            'user_id' => $this->userId,
            'conversation_id' => $conversationId,
            'role' => ConversationRole::User,
            'content' => $question,
        ]);

        ConversationMessage::create([
            'user_id' => $this->userId,
            'conversation_id' => $conversationId,
            'role' => ConversationRole::Assistant,
            'content' => $output->text,
        ]);

        return new ChatMessage(
            text: $output->text,
            conversationId: $conversationId,
        );
    }
}
