<?php

namespace App\Controller;

use App\Entity\Season;
use App\Entity\Program;
use App\Form\SeasonType;
use App\Service\Slugify;
use App\Repository\SeasonRepository;
use App\Repository\ProgramRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

// #[Route('/season')]
// class SeasonController extends AbstractController
// {
//     #[Route('/', name: 'app_season_index', methods: ['GET'])]
//     public function index(SeasonRepository $seasonRepository): Response
//     {
//         return $this->render('season/index.html.twig', [
//             'seasons' => $seasonRepository->findAll(),
//         ]);
//     }

//     #[Route('/new', name: 'app_season_new', methods: ['GET', 'POST'])]
//     #[IsGranted('ROLE_CONTRIBUTOR')]
//     public function new(Request $request, SeasonRepository $seasonRepository): Response
//     {
//         $season = new Season();
//         $form = $this->createForm(SeasonType::class, $season);
//         $form->handleRequest($request);

//         if ($form->isSubmitted() && $form->isValid()) {
//             $seasonRepository->add($season, true);

//             $this->addFlash('success', 'La nouvelle saison a bien été créée');

//             return $this->redirectToRoute('app_season_index', [], Response::HTTP_SEE_OTHER);
//         }

//         return $this->renderForm('season/new.html.twig', [
//             'season' => $season,
//             'form' => $form,
//         ]);
//     }

//     #[Route('/{id}', name: 'app_season_show', methods: ['GET'])]
//     public function show(Season $season): Response
//     {
//         return $this->render('season/show.html.twig', [
//             'season' => $season,
//         ]);
//     }

//     #[Route('/{id}/edit', name: 'app_season_edit', methods: ['GET', 'POST'])]
//     public function edit(Request $request, Season $season, SeasonRepository $seasonRepository): Response
//     {
//         $form = $this->createForm(SeasonType::class, $season);
//         $form->handleRequest($request);

//         if ($form->isSubmitted() && $form->isValid()) {
//             $seasonRepository->add($season, true);

//             $this->addFlash('success', 'La saison a bien été modifiée');

//             return $this->redirectToRoute('app_season_index', [], Response::HTTP_SEE_OTHER);
//         }

//         return $this->renderForm('season/edit.html.twig', [
//             'season' => $season,
//             'form' => $form,
//         ]);
//     }

//     #[Route('/{id}', name: 'app_season_delete', methods: ['POST'])]
//     public function delete(Request $request, Season $season, SeasonRepository $seasonRepository): Response
//     {
//         if ($this->isCsrfTokenValid('delete'.$season->getId(), $request->request->get('_token'))) {
//             $seasonRepository->remove($season, true);

//             $this->addFlash('success', 'La saison a bien été supprimée');
//         }

//         return $this->redirectToRoute('app_season_index', [], Response::HTTP_SEE_OTHER);
//     }
// }

#[Route('/program', name: 'program_season_')]
class SeasonController extends AbstractController
{
    #[Route('/{slug}/seasons', name: 'index', methods: ['GET'])]
    public function index(Program $program): Response
    {
        return $this->render('season/index.html.twig', [
            'program' => $program,
        ]);
    }

    #[Route('/{slug}/season/new', name: 'new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_CONTRIBUTOR')]
    public function newSeason(
        Request $request,
        SeasonRepository $seasonRepository,
        Program $program,
    ): Response {
        $season = new Season();
        $form = $this->createForm(SeasonType::class, $season);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $season->setProgram($program);
            $seasonRepository->add($season, true);

            $this->addFlash('success', 'La nouvelle saison a bien été créée');

            return $this->redirectToRoute('program_show', ['slug' => $program->getSlug()]);
        }

        return $this->renderForm('season/new.html.twig', [
            'program' => $program,
            'form' => $form,
        ]);
    }

    // #[Route('/{id}', name: 'app_season_show', methods: ['GET'])]
    // public function show(Season $season): Response
    // {
    //     return $this->render('season/show.html.twig', [
    //         'season' => $season,
    //     ]);
    // }

    #[Route('/{program_slug}/season/{season_id<\d+>}', methods: ['GET'], name: 'show')]
    #[ParamConverter('program', options: ['mapping' =>['program_slug' => 'slug']])]
    #[Entity('season', options: ['id' => 'season_id'])]
    public function showSeason(Program $program, Season $season): Response
    {
        return $this->render('program/season_show.html.twig',[
            'program' => $program,
            'season' => $season
        ]);
    }

    #[Route('/{program_slug}/seasons/{season_id<\d+>}/edit', name: 'edit', methods: ['GET', 'POST'])]
    #[ParamConverter('program', options: ['mapping' =>['program_slug' => 'slug']])]
    #[Entity('season', options: ['id' => 'season_id'])]
    public function editSeason(
        Request $request,
        Program $program, 
        Season $season, 
        SeasonRepository $seasonRepository
    ): Response {
        $form = $this->createForm(SeasonType::class, $season);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $seasonRepository->add($season, true);

            $this->addFlash('success', 'La saison a bien été modifiée');

            return $this->redirectToRoute('program_show', ['slug' => $program->getSlug()]);
        }

        return $this->renderForm('season/edit.html.twig', [
            'program' => $program,
            'season' => $season,
            'form' => $form,
        ]);
    }

    #[Route('/{program_slug}/seasons/{season_id<\d+>}/delete', name: 'delete', methods: ['POST'])]
    #[ParamConverter('program', options: ['mapping' =>['program_slug' => 'slug']])]
    #[Entity('season', options: ['id' => 'season_id'])]
    public function deleteSeason(
        Request $request, 
        Program $program,
        Season $season, 
        SeasonRepository $seasonRepository
    ): Response {
        if ($this->isCsrfTokenValid('delete'.$season->getId(), $request->request->get('_token'))) {
            $seasonRepository->remove($season, true);

            $this->addFlash('success', 'La saison a bien été supprimée');
        }

        return $this->redirectToRoute('program_show', ['slug' => $program->getSlug()]);
    }
}
