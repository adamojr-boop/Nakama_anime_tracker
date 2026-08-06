<?php

namespace App\Livewire\Episodes;

use App\Models\CommentAttachment;
use App\Models\EpisodeComment;
use App\Models\EpisodeRating;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class RateEpisode extends Component
{
    use WithFileUploads;

    public int $animeMalId;
    public int $episodeNumber;

    public ?int $rating = null;
    public ?string $favorite_character = null;
    public ?string $emotion = null;

    public string $body = '';
    public $image = null;
    public ?string $gifUrl = null;
    public ?string $timestampInput = null;
    public ?int $parentId = null;

    public array $emotions = [
        'euphoria' => ['label' => 'Euforia', 'emoji' => '🔥'],
        'sadness' => ['label' => 'Tristezza', 'emoji' => '😭'],
        'shock' => ['label' => 'Shock', 'emoji' => '😱'],
        'anger' => ['label' => 'Rabbia', 'emoji' => '🤬'],
        'hype' => ['label' => 'Hype', 'emoji' => '⚡'],
    ];

    protected array $commentRules = [
        'body' => 'required|string|max:1000',
        'image' => 'nullable|image|max:2048',
        'gifUrl' => 'nullable|url',
    ];

    public function mount(int $animeMalId, int $episodeNumber): void
    {
        $this->animeMalId = $animeMalId;
        $this->episodeNumber = $episodeNumber;

        $existing = EpisodeRating::query()
            ->where('user_id', Auth::id())
            ->where('anime_mal_id', $animeMalId)
            ->where('episode_number', $episodeNumber)
            ->first();

        if ($existing) {
            $this->rating = $existing->rating;
            $this->favorite_character = $existing->favorite_character;
            $this->emotion = $existing->emotion;
        }
    }

    public function save(): void
    {
        $this->validate([
            'rating' => 'required|integer|min:1|max:10',
            'favorite_character' => 'nullable|string|max:255',
            'emotion' => 'nullable|string|in:' . implode(',', array_keys($this->emotions)),
        ]);

        EpisodeRating::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'anime_mal_id' => $this->animeMalId,
                'episode_number' => $this->episodeNumber,
            ],
            [
                'rating' => $this->rating,
                'favorite_character' => $this->favorite_character,
                'emotion' => $this->emotion,
            ]
        );

        session()->flash('success', 'Valutazione e reazione salvate con successo!');
    }

    public function setReplyTo(int $commentId): void
    {
        $this->parentId = $commentId;
    }

    public function updatedImage(): void
    {
        if ($this->image) {
            $this->gifUrl = null;
        }
    }

    public function updatedGifUrl(): void
    {
        if (!empty($this->gifUrl)) {
            $this->image = null;
        }
    }

    public function cancelReply(): void
    {
        $this->parentId = null;
    }

    public function postComment(): void
    {
        $this->validate($this->commentRules);

        $comment = EpisodeComment::create([
            'user_id' => Auth::id(),
            'anime_mal_id' => $this->animeMalId,
            'episode_number' => $this->episodeNumber,
            'body' => $this->body,
            'parent_id' => $this->parentId,
        ]);

        if ($this->image) {
            $path = $this->image->store('comments-attachments', 'public');
            CommentAttachment::create([
                'episode_comment_id' => $comment->id,
                'file_path' => $path,
                'type' => 'image',
            ]);
        } elseif ($this->gifUrl) {
            CommentAttachment::create([
                'episode_comment_id' => $comment->id,
                'file_path' => $this->gifUrl,
                'type' => 'external_gif',
            ]);
        }

        $this->reset(['body', 'image', 'gifUrl', 'timestampInput', 'parentId']);
        session()->flash('comment_success', 'Commento pubblicato!');
    }

    public function insertMention(int $userId): void
    {
        $user = User::query()->find($userId);

        if (!$user) {
            return;
        }

        $name = trim($user->name);
        $tag = '@' . str_replace(' ', '_', $name);

        $this->body = trim($this->body) === ''
            ? $tag . ' '
            : rtrim($this->body) . ' ' . $tag . ' ';
    }

    public function addTimestamp(): void
    {
        $value = trim((string) $this->timestampInput);

        if ($value === '') {
            return;
        }

        if (!preg_match('/^(?:\d{1,2}:)?\d{1,2}:\d{2}$/', $value)) {
            $this->addError('timestampInput', 'Formato timestamp non valido. Usa mm:ss o hh:mm:ss.');
            return;
        }

        $this->resetValidation('timestampInput');

        $stamp = '[' . $value . ']';
        $this->body = trim($this->body) === ''
            ? $stamp . ' '
            : rtrim($this->body) . ' ' . $stamp . ' ';

        $this->timestampInput = null;
    }

    public function getMentionSuggestionsProperty(): array
    {
        if (!preg_match('/(?:^|\s)@([a-zA-Z0-9_]{1,30})$/', $this->body, $matches)) {
            return [];
        }

        $query = $matches[1];

        return User::query()
            ->where('name', 'like', $query . '%')
            ->orderBy('name')
            ->limit(6)
            ->get(['id', 'name'])
            ->map(fn (User $user) => ['id' => $user->id, 'name' => $user->name])
            ->all();
    }

    public function deleteComment(int $commentId): void
    {
        $comment = EpisodeComment::findOrFail($commentId);

        if ($comment->user_id === Auth::id()) {
            foreach ($comment->attachments as $attachment) {
                if ($attachment->type === 'image') {
                    Storage::disk('public')->delete($attachment->file_path);
                }
            }

            $comment->delete();
        }
    }

    public function formatBody(string $text): string
    {
        $escaped = e($text);

        return preg_replace(
            '/@([a-zA-Z0-9_]+)/',
            '<span class="badge bg-primary text-wrap">@$1</span>',
            $escaped
        );
    }

    public function render()
    {
        $comments = EpisodeComment::with(['user', 'attachments', 'replies.user', 'replies.attachments'])
            ->where('anime_mal_id', $this->animeMalId)
            ->where('episode_number', $this->episodeNumber)
            ->whereNull('parent_id')
            ->latest()
            ->get();

        return view('livewire.episodes-rate-episode', [
            'comments' => $comments,
        ]);
    }
}
