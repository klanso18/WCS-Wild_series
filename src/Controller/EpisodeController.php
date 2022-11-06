<?php

namespace App\Controller;

use App\Entity\Season;
use App\Entity\Episode;
use App\Entity\Program;
use App\Service\Slugify;
use App\Form\EpisodeType;
use Symfony\Component\Mime\Email;
use App\Repository\EpisodeRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

// #[Route('/episode')]
// class EpisodeController extends AbstractController
// {
//     #[Route('/', name: 'app_episode_index', methods: ['GET'])]
//     public function index(EpisodeRepository $episodeRepository): Response
//     {
//         return $this->render('episode/index.html.twig', [
//             'episodes' => $episodeRepository->findAll(),
//         ]);
//     }

//     #[Route('/new', name: 'app_episode_new', methods: ['GET', 'POST'])]
//     public function new(Request $request, MailerInterface $mailer, EpisodeRepository $episodeRepository, Slugify $slugify): Response
//     {
//         $episode = new Episode();
//         $form = $this->createForm(EpisodeType::class, $episode);
//         $form->handleRequest($request);

//         if ($form->isSubmitted() && $form->isValid()) {
//             $slug = $slugify->generate($episode->getTitle());
//             $episode->setSlug($slug);
//             $episodeRepository->add($episode, true);

//             $this->addFlash('success', 'Le nouvel épisode a bien été créé');

//             $email = (new Email())
//                 ->from($this->getParameter('mailer_from'))
//                 ->to('your_email@example.com')
//                 ->subject('Un nouvel épisode vient d\'être publié !')
//                 ->html($this->renderView('episode/newEpisodeEmail.html.twig', ['episode' => $episode]));
            
//             $mailer->send($email);

//             return $this->redirectToRoute('app_episode_index', [], Response::HTTP_SEE_OTHER);
//         }

//         return $this->renderForm('episode/new.html.twig', [
//             'episode' => $episode,
//             'form' => $form,
//         ]);
//     }

//     #[Route('/{slug}', name: 'app_episode_show', methods: ['GET'])]
//     public function show(Episode $episode): Response
//     {
//         return $this->render('episode/show.html.twig', [
//             'episode' => $episode,
//         ]);
//     }

//     #[Route('/{slug}/edit', name: 'app_episode_edit', methods: ['GET', 'POST'])]
//     public function edit(Request $request, Episode $episode, EpisodeRepository $episodeRepository): Response
//     {
//         $form = $this->createForm(EpisodeType::class, $episode);
//         $form->handleRequest($request);

//         if ($form->isSubmitted() && $form->isValid()) {
//             $episodeRepository->add($episode, true);

//             $this->addFlash('success', 'L\'épisode a bien été modifié');

//             return $this->redirectToRoute('app_episode_index', [], Response::HTTP_SEE_OTHER);
//         }

//         return $this->renderForm('episode/edit.html.twig', [
//             'episode' => $episode,
//             'form' => $form,
//         ]);
//     }

//     #[Route('/{id}', name: 'app_episode_delete', methods: ['POST'])]
//     public function delete(Request $request, Episode $episode, EpisodeRepository $episodeRepository): Response
//     {
//         if ($this->isCsrfTokenValid('delete'.$episode->getId(), $request->request->get('_token'))) {
//             $episodeRepository->remove($episode, true);

//             $this->addFlash('danger', 'L\'épisode a bien été supprimé');
//         }

//         return $this->redirectToRoute('app_episode_index', [], Response::HTTP_SEE_OTHER);
//     }
// }

#[Route('/{program_slug}/season/{season_id<\d+>}', name: 'program_season_episode_')]
#[ParamConverter('program', options: ['mapping' =>['program_slug' => 'slug']])]
#[Entity('season', options: ['id' => 'season_id'])]
class EpisodeController extends AbstractController
{
    #[Route('/episodes', name: 'index', methods: ['GET'])]
    public function index(Program $program, Season $season): Response
    {
        return $this->render('season/index.html.twig', [
            'program' => $program,
            'season' => $season
        ]);
    }

    #[Route('/episode/new', name: 'new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_CONTRIBUTOR')]
    public function newEpisode(
        Request $request,
        EpisodeRepository $episodeRepository,
        Program $program,
        Season $season,
        Slugify $slugify,
        MailerInterface $mailer
    ): Response {
        $episode = new Episode();
        $form = $this->createForm(EpisodeType::class, $episode);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $slug = $slugify->generate($episode->getTitle());
            $episode->setSlug($slug);
            $episode->setSeason($season);
            $episodeRepository->add($episode, true);

            $this->addFlash('success', 'Le nouvel épisode a bien été créé');

            $email = (new Email())
                ->from($this->getParameter('mailer_from'))
                ->to('your_email@example.com')
                ->subject('Un nouvel épisode vient d\'être publié !')
                ->html($this->renderView('episode/newEpisodeEmail.html.twig', ['episode' => $episode]));
            
            $mailer->send($email);

            return $this->redirectToRoute('program_show', ['slug' => $program->getSlug()]);
        }

        return $this->renderForm('episode/new.html.twig', [
            'program' => $program,
            'season' => $season,
            'form' => $form
        ]);
    }

    #[Route('/{episode_slug}', methods: ['GET'], name: 'show')]
    #[ParamConverter('episode', options: ['mapping' =>['episode_slug' => 'slug']])]
    public function showEpisode(Program $program, Season $season, Episode $episode): Response
    {
        return $this->render('program/episode_show.html.twig',[
            'program' => $program,
            'season' => $season,
            'epsiode' => $episode
        ]);
    }

    #[Route('/{episode_slug}/edit', name: 'edit', methods: ['GET', 'POST'])]
    #[ParamConverter('episode', options: ['mapping' =>['episode_slug' => 'slug']])]
    public function editEpisode(
        Request $request,
        Program $program, 
        Season $season, 
        Episode $episode,
        EpisodeRepository $episodeRepository
    ): Response {
        $form = $this->createForm(EpisodeType::class, $episode);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $episodeRepository->add($episode, true);

            $this->addFlash('success', 'L\'épisode a bien été modifié');

            return $this->redirectToRoute('program_show', ['slug' => $program->getSlug()]);
        }

        return $this->renderForm('season/edit.html.twig', [
            'program' => $program,
            'season' => $season,
            'episode' => $episode,
            'form' => $form,
        ]);
    }

    #[Route('/{episode_slug}/delete', name: 'delete', methods: ['POST'])]
    #[ParamConverter('episode', options: ['mapping' =>['episode_slug' => 'slug']])]
    public function deleteEpisode(
        Request $request, 
        Program $program,
        Season $season, 
        Episode $episode,
        EpisodeRepository $episodeRepository
    ): Response {
        if ($this->isCsrfTokenValid('delete'.$episode->getId(), $request->request->get('_token'))) {
            $episodeRepository->remove($episode, true);

            $this->addFlash('success', 'L\'épisode a bien été supprimé');
        }

        return $this->redirectToRoute('program_show', ['slug' => $program->getSlug()]);
    }
}