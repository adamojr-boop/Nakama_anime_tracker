<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Badge;

class BadgeSeeder extends Seeder
{
    public function run(): void
    {
        $badges = [
            // AREA HYPE
            [
                'code' => 'hype_first_checkin',
                'name' => '⚡ Simulcast Warrior',
                'description' => 'Hai spuntato un episodio entro 24 ore dal rilascio ufficiale!',
                'icon' => '⚡',
                'category' => 'hype'
            ],
            [
                'code' => 'hype_master',
                'name' => '🚀 Hypeman',
                'description' => 'Hai fatto 10 check-in lampo entro 24 ore dalla trasmissione.',
                'icon' => '🚀',
                'category' => 'hype'
            ],
            // AREA SOCIAL
            [
                'code' => 'social_first_comment',
                'name' => '💬 Primo Salotto',
                'description' => 'Hai pubblicato il tuo primo commento su Nakama.',
                'icon' => '💬',
                'category' => 'social'
            ],
            [
                'code' => 'social_talkative',
                'name' => '🗣️ Chiacchierone',
                'description' => 'Hai pubblicato 10 commenti nella community.',
                'icon' => '🗣️',
                'category' => 'social'
            ],
            [
                'code' => 'social_guru',
                'name' => '🔥 Pillar of Community',
                'description' => 'Hai raggiunto la soglia critica di 50 commenti!',
                'icon' => '🔥',
                'category' => 'social'
            ],
        ];

        foreach ($badges as $badge) {
            Badge::firstOrCreate(['code' => $badge['code']], $badge);
        }
    }
}
