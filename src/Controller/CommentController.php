<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Episode;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

#[Route('/comment', name:'app_comment_')]
class CommentController extends AbstractController
{
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(CommentRepository $commentRepository): Response
    {
        return $this->render('comment/index.html.twig', [
            'comments' => $commentRepository->findAll(),
        ]);
    }

    #[Route('/new/{episode_slug<[^0-9]+>}', name: 'new', methods: ['GET', 'POST'])]
    #[ParamConverter('episode', options: ['mapping' =>['episode_slug' => 'slug']])]
    public function new(Request $request, CommentRepository $commentRepository, Episode $episode): Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // on récupère l'utilisateur
            $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
            $user = $this->getUser();

             /** @var \App\Entity\User $user */
            $comment->setAuthor($user);
            $comment->setEpisode($episode);

            $commentRepository->add($comment, true);

            $this->addFlash('success', 'Votre commentaire a bien été ajouté');

            $slug = $episode->getSlug();
            $seasonId = $episode->getSeason()->getId();
            $season = $episode->getSeason();
            $program = $season->getProgram()->getSlug();


            return $this->redirectToRoute('program_episode_show', 
            ["program_slug" => $program, 
            "season_id" => $seasonId, 
            "episode_slug" => $slug], 
            Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('comment/new.html.twig', [
            'comment' => $comment,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(Comment $comment): Response
    {
        return $this->render('comment/show.html.twig', [
            'comment' => $comment,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Comment $comment, CommentRepository $commentRepository): Response
    {

        if (!($this->getUser() == $comment->getAuthor()) && !($this->isGranted('ROLE_ADMIN'))) {
            // S'il ne s'agit pas du propriétaire, une exception de type 403 Access Denied est levée.
            throw new AccessDeniedException("Vous n'avez pas les droits pour modifier ce commentaire!");
        }

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $commentRepository->add($comment, true);

            
            $slug = $comment->getEpisode()->getSlug();
            $seasonId = $comment->getEpisode()->getSeason()->getId();
            $season = $comment->getEpisode()->getSeason();
            $program = $season->getProgram()->getSlug();

            return $this->redirectToRoute('program_episode_show', 
            ["program_slug" => $program, 
            "season_id" => $seasonId, 
            "episode_slug" => $slug], 
            Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('comment/edit.html.twig', [
            'comment' => $comment,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, Comment $comment, CommentRepository $commentRepository): Response
    {
        if (!($this->getUser() == $comment->getAuthor()) && !($this->isGranted('ROLE_ADMIN'))) {
            // S'il ne s'agit pas du propriétaire, une exception de type 403 Access Denied est levée.
            throw new AccessDeniedException("Vous n'avez pas les droits pour supprimer ce commentaire!");
        }

        if ($this->isCsrfTokenValid('delete'.$comment->getId(), $request->request->get('_token'))) {
            $commentRepository->remove($comment, true);
        }
        $this->addFlash('danger', 'Votre commentaire a bien été supprimé');

        $slug = $comment->getEpisode()->getSlug();
        $seasonId = $comment->getEpisode()->getSeason()->getId();
        $season = $comment->getEpisode()->getSeason();
        $program = $season->getProgram()->getSlug();

        return $this->redirectToRoute('program_episode_show', 
        ["program_slug" => $program, 
        "season_id" => $seasonId, 
        "episode_slug" => $slug], 
        Response::HTTP_SEE_OTHER);
    }
}
