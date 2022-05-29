<?php
// src/Controller/ProgramController.php
namespace App\Controller;

use App\Entity\Episode;
use App\Entity\Season;
use App\Entity\Program;
use App\Repository\ProgramRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/program', name: 'program_')]
class ProgramController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(ProgramRepository $programRepository): Response
    {
        $programs = $programRepository->findAll();
        return $this->render(
            'program/index.html.twig',
            ['programs' => $programs]
        );
    }

    #[Route('/{id<^[0-9]+$>}', name: 'show')]
    public function show(Program $program): Response
    {
        return $this->render('program/show.html.twig', [
            'program' => $program
        ]);
    }

    #[Route('/{program_id<\d+>}/season/{season_id<\d+>}', methods: ['GET'], name: 'season_show')]
    #[Entity('program', options: ['id' => 'program_id'])]
    #[Entity('season', options: ['id' => 'season_id'])]
    public function showSeason(Program $program, Season $season): Response
    {
        return $this->render('program/season_show.html.twig',[
            'program' => $program,
            'season' => $season
        ]);
    }

    #[Route('/{program_id<\d+>}/season/{season_id<\d+>}/episode/{episode_id<\d+>}', methods: ['GET'], name: 'episode_show')]
    #[Entity('program', options: ['id' => 'program_id'])]
    #[Entity('season', options: ['id' => 'season_id'])]
    #[Entity('episode', options: ['id' => 'episode_id'])]
    public function showEpisode(Program $program, Season $season, Episode $episode): Response
    {
        return $this->render('program/episode_show.html.twig', [
            'program' => $program,
            'season' => $season,
            'episode'=> $episode,
        ]);
    }


}