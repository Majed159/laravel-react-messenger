<?php

namespace Database\Seeders;

use App\Models\Conversation;
use App\Models\Group;
use App\Models\Message;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Majd Admin',
            'password' => bcrypt('password'),
            'email' => 'Majd@admin.com',
            'is_admin' => true,
        ]);
        User::factory()->create([
            'name' => 'Majd User',
            'password' => bcrypt('password'),
            'email' => 'Majd@User.com',

        ]);
        User::factory(10)->create();
        for ($i = 0; $i < 5; $i++) {
            $group = Group::factory()->create([
                'owner_id' => 1,
            ]);
            $users = User::inRandomOrder()->limit(rand(1, 5))->pluck('id');
            $group->users()->attach(array_unique([1, ...$users]));
        }
        Message::factory(1000)->create();
        $messages = Message::whereNull('group_id')->orderBy('created_at')->get();
        $conversations = $messages->groupBy(function ($message) {
            return collect([$message->sender_id, $message->receiver_id])->sort()->implode('_');
        })->map(function ($groupedMessages) {
            return [
                'user_id1' => $groupedMessages->first()->sender_id,
                'user_id2' => $groupedMessages->first()->receiver_id,
                'last_message_id' => $groupedMessages->last()->id,
                'created_at' => new Carbon,
                'updated_at' => new Carbon,
            ];
        })->values();

        $conversations->each(function (array $attributes): void {
            $conversation = Conversation::create($attributes);
            Message::whereNull('group_id')
                ->where(function ($query) use ($conversation) {
                    $query->where(function ($query) use ($conversation) {
                        $query->where('sender_id', $conversation->user_id1)
                            ->where('receiver_id', $conversation->user_id2);
                    })->orWhere(function ($query) use ($conversation) {
                        $query->where('sender_id', $conversation->user_id2)
                            ->where('receiver_id', $conversation->user_id1);
                    });
                })
                ->update(['conversation_id' => $conversation->id]);
        });
    }
}
