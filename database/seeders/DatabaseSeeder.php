<?php

namespace Database\Seeders;

use App\Models\Testimonial;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Testimonial::insert([
            [
                'name' => 'Sophie Laurent',
                'role' => 'Designer',
                'avatar_initials' => 'SL',
                'text' => 'Le quiz de style était incroyablement précis. Il m\'a recommandé des montures que je n\'aurais jamais choisies moi-même, et elles sont devenues mes lunettes préférées !',
                'sort_order' => 0,
            ],
            [
                'name' => 'Marcus Chen',
                'role' => 'Ingénieur Logiciel',
                'avatar_initials' => 'MC',
                'text' => 'Une qualité premium qui se ressent vraiment. Les montures en titane sont incroyablement légères, et le revêtement anti-lumière bleue a fait une énorme différence pour mon travail sur écran.',
                'sort_order' => 1,
            ],
            [
                'name' => 'Aisha Patel',
                'role' => 'Médecin',
                'avatar_initials' => 'AP',
                'text' => 'En tant que personne qui porte des lunettes quotidiennement, le confort est primordial. Optique Échouate a allié style et confort toute la journée. L\'examen de la vue était complet et professionnel.',
                'sort_order' => 2,
            ],
            [
                'name' => 'James Wilson',
                'role' => 'Directeur Marketing',
                'avatar_initials' => 'JW',
                'text' => 'Je n\'ai jamais reçu autant de compliments sur mes lunettes. Le processus de sélection était fluide et l\'essayage à domicile très pratique.',
                'sort_order' => 3,
            ],
        ]);
    }
}
