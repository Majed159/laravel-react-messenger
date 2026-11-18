<?php

use App\Models\Conversation;
use App\Models\Message;
use Database\Seeders\DatabaseSeeder;

it('seeds messages with valid conversation links', function () {
    fake()->seed(7345);

    $this->seed(DatabaseSeeder::class);

    $this->assertDatabaseCount('messages', 1000);
    expect(Message::whereNull('group_id')->exists())->toBeTrue();
    expect(Message::whereNotNull('group_id')->exists())->toBeTrue();
    expect(Message::whereNull('group_id')->whereNull('conversation_id')->exists())->toBeFalse();
    expect(Message::whereNotNull('group_id')->whereNotNull('conversation_id')->exists())->toBeFalse();

    $conversation = Conversation::whereNotNull('last_message_id')->firstOrFail();
    expect(
        Message::where('id', $conversation->last_message_id)
            ->where('conversation_id', $conversation->id)
            ->exists()
    )->toBeTrue();
});
