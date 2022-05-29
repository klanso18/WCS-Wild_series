<?php

namespace App\DataFixtures;

use App\Entity\Episode;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class EpisodeFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for($i = 0; $i < 10; $i++) {
            $episode = new Episode();
            $episode->setTitle($faker->title());
            $episode->setNumber($faker->numberBetween(1, 10));
            $episode->setSynopsis($faker->paragraphs(3, true));

            $episode->setSeason($this->getReference('season_' . $faker->numberBetween(0, 4)));
            $manager->persist($episode);
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        // Tu retournes ici toutes les classes de fixtures dont SeasonFixtures dépend
        return [
        SeasonFixtures::class,
        ];
    }
    
}

// <?php

// namespace App\DataFixtures;

// use App\Entity\Episode;
// use Doctrine\Bundle\FixturesBundle\Fixture;
// use Doctrine\Common\DataFixtures\DependentFixtureInterface;
// use Doctrine\Persistence\ObjectManager;

// class EpisodeFixtures extends Fixture implements DependentFixtureInterface
// {
//     public const EPISODES = [
//         1 => ['title' => 'le premier', 'number' => '1', 'synopsis' => "l'episode 1 est top", 'season' => 'season_1'],
//         2 => ['title' => 'le deuxième', 'number' => '2', 'synopsis' => "l'episode 2 est top", 'season' => 'season_1'],
//         3 => ['title' => 'le troisième', 'number' => '3', 'synopsis' => "l'episode 3 est top", 'season' => 'season_1'],
//         4 => ['title' => 'le quatrième', 'number' => '4', 'synopsis' => "l'episode 4 est top", 'season' => 'season_1'],
//         5 => ['title' => 'le cinquième', 'number' => '5', 'synopsis' => "l'episode 5 est top", 'season' => 'season_1'],
//         6 => ['title' => 'le sixième', 'number' => '6', 'synopsis' => "l'episode 6 est top", 'season' => 'season_1'],
//         7 => ['title' => 'le septième', 'number' => '7', 'synopsis' => "l'episode 7 est top", 'season' => 'season_1'],
//     ];

//     public function load(ObjectManager $manager)
//     {
//         for ($i = 1; $i <= count(SeasonFixtures::SEASONS); $i++) {
//             foreach (self::EPISODES as $episodeTitle) {
//                 $episode = new Episode();
//                 $episode->setTitle($episodeTitle['title']);
//                 $episode->setNumber($episodeTitle['number']);
//                 $episode->setSynopsis($episodeTitle['synopsis']);
//                 $episode->setSeason($this->getReference('season_' . $i));
//                 $manager->persist($episode);
//             }
//         }
        
//         $manager->flush();
//     }

//     public function getDependencies()
//     {
//         // Tu retournes ici toutes les classes de fixtures dont EpisodeFixtures dépend
//         return [
//         SeasonFixtures::class,
//         ];
//     }
// }
