<?php

namespace App\Services;

use App\Models\Badge;
use App\Models\BingeSession;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class BadgeService
{
    /**
     * Retro-compat helper used by older components that passed full badge data.
     */
    public function grantBadge(User $user, string $name, string $description, string $icon): bool
    {
        $code = Str::slug($name, '_');

        Badge::firstOrCreate(
            ['code' => $code],
            [
                'name' => $name,
                'description' => $description,
                'icon' => $icon,
                'category' => 'special',
            ]
        );

        return $this->unlockBadge($user, $code);
    }

    /**
     * Tenta di sbloccare un badge per l'utente dato il suo codice.
     */
    public function unlockBadge(User $user, string $badgeCode): bool
    {
        $badge = Badge::where('code', $badgeCode)->first();

        if (!$badge) {
            return false;
        }
        // Verifica se l'utente possiede già questo badge
        if (!$user->badges()->where('badge_id', $badge->id)->exists()) {
            $user->badges()->attach($badge->id, ['unlocked_at' => now()]);
            // Possiamo loggare lo sblocco o emettere un evento
            Log::info("🏆 Badge Sbloccato! [{$badge->name}] per l'utente {$user->id}");
            return true; // Sbloccato con successo!
        }

        return false; // Già posseduto
    }
    /**
     * Controlla i badge dell'Area Social (basato sul numero di commenti pubblicati)
     */
    public function checkSocialBadges(User $user, int $commentsCount): array
    {
        $unlocked = [];

        if ($commentsCount >= 1) {
            if ($this->unlockBadge($user, 'social_first_comment')) {
                $unlocked[] = '💬 Primo Salotto';
            }
        }
        if ($commentsCount >= 10) {
            if ($this->unlockBadge($user, 'social_talkative')) {
                $unlocked[] = '🗣️ Chiacchierone';
            }
        }
        if ($commentsCount >= 50) {
            if ($this->unlockBadge($user, 'social_guru')) {
                $unlocked[] = '🔥 Pillar of Community';
            }
        }

        return $unlocked;
    }
    /**
     * Controlla i badge dell'Area Hype (visti entro 24h dal rilascio)
     */
    public function checkHypeBadge(User $user, $airingTimestamp = null): bool
    {
        // Se non viene fornito un orario di trasmissione, usiamo il timestamp corrente per il test
        $releaseTime = $airingTimestamp ? \Carbon\Carbon::parse($airingTimestamp) : now();
        $hoursDiff = now()->diffInHours($releaseTime);
        // Se l'episodio viene spuntato entro 24 ore dal rilascio
        if ($hoursDiff <= 24) {
            $unlocked = $this->unlockBadge($user, 'hype_first_checkin');
            // Possiamo anche tracciare il totale dei check-in veloci per sbloccare 'hype_master' (10 check-in)
            // (Aggiungeremo un contatore dedicato se necessario)
            return $unlocked;
        }

        return false;
    }

    /**
     * Traccia l'avanzamento degli episodi per verificare le sessioni di binge-watching.
     *
     * @param  \App\Models\User  $user
     * @param  string  $malId
     * @param  int  $addedEpisodes Numero di episodi aggiunti in questa azione (default 1)
     * @return array Nomi dei nuovi badge sbloccati
     */
    public function trackBingeSession($user, string $malId, int $addedEpisodes = 1): array
    {
        $now = Carbon::now();
        $maxTimeGapMinutes = 120; // 2 ore di tolleranza tra un episodio e l'altro

        // Cerca una sessione attiva negli ultimi 120 minuti per questo utente e questo anime
        $session = BingeSession::where('user_id', $user->id)
            ->where('mal_id', $malId)
            ->where('last_watched_at', '>=', $now->copy()->subMinutes($maxTimeGapMinutes))
            ->first();

        if ($session) {
            // Incrementa la sessione esistente
            $session->episodes_watched += $addedEpisodes;
            $session->last_watched_at = $now;
            $session->save();
        } else {
            // Avvia una nuova sessione
            $session = BingeSession::create([
                'user_id'          => $user->id,
                'mal_id'           => $malId,
                'episodes_watched' => $addedEpisodes,
                'last_watched_at'  => $now,
            ]);
        }

        $unlockedBadges = [];

        if ($session->episodes_watched >= 5) {
            if ($this->grantBadge($user, 'Binge Watcher', 'Hai guardato 5+ episodi nella stessa sessione!', '🍿')) {
                $unlockedBadges[] = 'Binge Watcher 🍿';
            }
        }

        if ($session->episodes_watched >= 10) {
            if ($this->grantBadge($user, 'Maratoneta Inarrestabile', 'Hai guardato 10+ episodi nella stessa sessione!', '🔥')) {
                $unlockedBadges[] = 'Maratoneta Inarrestabile 🔥';
            }
        }

        Cache::forget("user_{$user->id}_max_binge");

        return $unlockedBadges;
    }
}
