<?php

namespace Bizhub\Ember\Models;

use Bizhub\Ember\Enums\ConversationRole;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ConversationMessage extends Model
{
    use HasUuids;
    use HasFactory;

    protected $table = 'ember_conversation_messages';

    protected $fillable = [
        'user_id',
        'conversation_id',
        'role',
        'content',
    ];

    protected $casts = [
        'role' => ConversationRole::class,
    ];
}
