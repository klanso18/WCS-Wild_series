<?php

namespace App\DataFixtures;

use App\Entity\Program;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ProgramFixtures extends Fixture implements DependentFixtureInterface
{
    public const PROGRAMS = [
        ['title' => 'Walking dead', 'synopsis' => 'Des zombies envahissent la terre', 'category' => 'category_Horreur'],
        ['title' => 'All of us are dead', 'synopsis' => 'Où sont les vivants?', 'category' => 'category_Horreur'],
        ['title' => 'American Horror Story', 'synopsis' => "Une histoire de film d'horreur", 'category' => 'category_Horreur'],
        ['title' => 'Slasher', 'synopsis' => "D'ignobles tueurs en série sèment l'effroi", 'category' => 'category_Horreur'],
        ['title' => 'Stranger things', 'synopsis' => "Comme c'est étrange", 'category' => 'category_Fantastique'],
        ['title' => 'Breaking bad', 'synopsis' => "Un peu de chimie", 'category' => 'category_Action'],
        ['title' => 'The Witcher', 'synopsis' => "Des bonbons ou un sort !", 'category' => 'category_Aventure'],
        ['title' => 'Arcane', 'synopsis' => "Entre technologies magiques et convictions incompatibles", 'category' => 'category_Animation']
    ];

    public function load(ObjectManager $manager)
    {
        foreach (self::PROGRAMS as $programName) {
            $program = new Program();
            $program->setTitle($programName['title']);
            $program->setSynopsis($programName['synopsis']);
            $program->setCategory($this->getReference($programName['category']));
            $manager->persist($program);
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        // Tu retournes ici toutes les classes de fixtures dont ProgramFixtures dépend
        return [
        CategoryFixtures::class,
        ];
    }

    
}
