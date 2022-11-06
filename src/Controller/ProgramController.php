<?php
// src/Controller/ProgramController.php
namespace App\Controller;

use App\Entity\Season;
use App\Entity\Episode;
use App\Entity\Program;
use App\Form\SeasonType;
use App\Service\Slugify;
use App\Form\ProgramType;
use Symfony\Component\Mime\Email;
use App\Form\SearchProgramFormType;
use App\Repository\SeasonRepository;
use App\Repository\ProgramRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

#[Route('/program', name: 'program_')]
class ProgramController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(Request $request, ProgramRepository $programRepository, CategoryRepository $categoryRepository): Response
    {
        // $programs = $programRepository->findAll();
        // $categories = $categoryRepository->findAll();
        $form = $this->createForm(SearchProgramFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $search = $form->getData()['search'];
            $programs = $programRepository->findLikeName($search);
        } else {
            $programs = $programRepository->findAll();
        }

        return $this->renderForm('program/index.html.twig', [
                'programs' => $programs,
                // 'categories' => $categories,
                'form' => $form,
        ]);
    }


    #[Route('/new', name: 'new')]
    public function new(
        Request $request, 
        MailerInterface $mailer, 
        ProgramRepository $programRepository, 
        Slugify $slugify,
        SluggerInterface $slugger
    ): Response
    {
        $program = new Program();
        $form = $this->createForm(ProgramType::class, $program);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $slug = $slugify->generate($program->getTitle());
            $program->setSlug($slug);
            $program->setOwner($this->getUser());
            $poster = $form->get('poster')->getData();
            if ($poster) {
                $originalFilename = pathinfo($poster->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$poster->guessExtension();
                try {
                    $poster->move(
                        $this->getParameter('programs_dir'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                $program->setPoster($newFilename);
            }
            $programRepository->add($program, true); 

            $this->addFlash('success', 'La nouvelle série a bien été créée');

            $email = (new Email())
                ->from($this->getParameter('mailer_from'))
                ->to('your_email@example.com')
                ->subject('Une nouvelle série vient d\'être publiée !')
                ->html($this->renderView('program/newProgramEmail.html.twig', ['program' => $program]));
            
            $mailer->send($email);

            return $this->redirectToRoute('program_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('program/new.html.twig', [
            'form' => $form,
        ]);
    }


    #[Route('/{slug}', name: 'show')]
    public function show(Program $program): Response
    {
        return $this->render('program/show.html.twig', [
            'program' => $program
        ]);
    }

    #[Route('/{slug}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Program $program, ProgramRepository $programRepository): Response
    {
        // Check wether the logged in user is the owner of the program
        if (!($this->getUser() == $program->getOwner())) {
            // If not the owner, throws a 403 Access Denied exception
            throw new AccessDeniedException('Vous n\'avez pas les droits pour modifier cette série!');
        }
        
        $form = $this->createForm(ProgramType::class, $program);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $programRepository->add($program, true);

            $this->addFlash('success', 'La série a bien été modifiée');

            return $this->redirectToRoute('program_show', ['slug' => $program->getSlug()]);
        }

        return $this->renderForm('program/edit.html.twig', [
            'program' => $program,
            'form' => $form,
        ]);
    }

    #[Route('/{program_slug}/season/{season_id<\d+>}/episode/{episode_slug}', methods: ['GET'], name: 'episode_show')]
    #[ParamConverter('program', options: ['mapping' =>['program_slug' => 'slug']])]
    #[ParamConverter('episode', options: ['mapping' =>['episode_slug' => 'slug']])]
    // #[Entity('program', options: ['id' => 'program_id'])]
    #[Entity('season', options: ['id' => 'season_id'])]
    // #[Entity('episode', options: ['id' => 'episode_id'])]
    public function showEpisode(Program $program, Season $season, Episode $episode): Response
    {
        return $this->render('program/episode_show.html.twig', [
            'program' => $program,
            'season' => $season,
            'episode'=> $episode,
        ]);
    }

    // #[Route('/{slug}/delete', name: 'delete', methods: ['POST'])]
    #[Route('/{id}/delete', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, Program $program, ProgramRepository $programRepository): Response
    {
        // Check wether the logged in user is the owner of the program
        if (!($this->getUser() == $program->getOwner())) {
            // If not the owner, throws a 403 Access Denied exception
            throw new AccessDeniedException('Vous n\'avez pas les droits pour modifier cette série!');
        }
        
        if ($this->isCsrfTokenValid('delete'.$program->getId(), $request->request->get('_token'))) {
            $programRepository->remove($program, true);

            $this->addFlash('danger', 'La série a bien été supprimée');
        }

        return $this->redirectToRoute('program_index', [], Response::HTTP_SEE_OTHER);
    }


}