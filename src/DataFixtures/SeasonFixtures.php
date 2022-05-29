<?php

namespace App\DataFixtures;

use App\Entity\Season;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class SeasonFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for($i = 0; $i < 5; $i++) {
            $season = new Season();
            $season->setNumber($faker->numberBetween(1, 10));
            $season->setYear($faker->year());
            $season->setDescription($faker->paragraphs(3, true));

            $season->setProgram($this->getReference('program_' . $faker->numberBetween(0, 5)));
            $manager->persist($season);
            $this->addReference('season_'. $i, $season);
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        // Tu retournes ici toutes les classes de fixtures dont SeasonFixtures dépend
        return [
        ProgramFixtures::class,
        ];
    }
    
}

// <?php

// namespace App\DataFixtures;

// use App\Entity\Season;
// use Doctrine\Bundle\FixturesBundle\Fixture;
// use Doctrine\Common\DataFixtures\DependentFixtureInterface;
// use Doctrine\Persistence\ObjectManager;

// class SeasonFixtures extends Fixture implements DependentFixtureInterface
// {
//     public const SEASONS = [
//         1 => ['number' => '1', 'year' => '2022', 'description' => 'la saison 1 est top', 'program' => 'program_Walking dead'],
//         2 => ['number' => '2', 'year' => '2022', 'description' => 'la saison 2 est top', 'program' => 'program_All of us are dead'],
//         3 => ['number' => '1', 'year' => '2022', 'description' => 'la saison 1 est top', 'program' => 'program_American Horror Story'],
//         4 => ['number' => '1', 'year' => '2022', 'description' => 'la saison 1 est top', 'program' => 'program_Breaking bad'],
//         5 => ['number' => '1', 'year' => '2022', 'description' => 'la saison 1 est top', 'program' => 'program_Slasher'],
//         6 => ['number' => '1', 'year' => '2022', 'description' => 'la saison 1 est top', 'program' => 'program_Stranger things'],
//         7 => ['number' => '1', 'year' => '2022', 'description' => 'la saison 1 est top', 'program' => 'program_The Witcher'],
//         8 => ['number' => '1', 'year' => '2022', 'description' => 'la saison 1 est top', 'program' => 'program_Arcane']
//     ];

//     public function load(ObjectManager $manager)
//     {
//         foreach (self::SEASONS as $key => $seasonNumber) {
//             $season = new Season();
//             $season->setNumber($seasonNumber['number']);
//             $season->setYear($seasonNumber['year']);
//             $season->setDescription($seasonNumber['description']);
//             $season->setProgram($this->getReference('program_' . $key));
//             $this->addReference('season_' . $key, $season);
//             $manager->persist($season);
//         }
//         $manager->flush();
//     }

//     public function getDependencies()
//     {
//         // Tu retournes ici toutes les classes de fixtures dont SeasonFixtures dépend
//         return [
//         ProgramFixtures::class,
//         ];
//     }

    
// }
