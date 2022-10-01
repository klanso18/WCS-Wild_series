<?php
// src/Controller/ProgramController.php
namespace App\Controller;

use App\Entity\User;
use App\Form\ProfileType;
use App\Repository\UserRepository;
use App\Repository\ProgramRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/myspace', name: 'myspace_')]
class MySpaceController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(ProgramRepository $programRepository, CategoryRepository $categoryRepository, Request $request): Response
    {
        $programs = $programRepository->findAll();
        $categories = $categoryRepository->findAll();
        return $this->render('myspace/index.html.twig', [
                'programs' => $programs,
                'categories' => $categories
        ]);
    }

    #[Route('/edit-profile', name: 'edit-profile')]
    public function editProfile(Request $request, UserRepository $userRepository):Response
    {
        $user = $this->getUser(); 
        $form = $this->createForm(ProfileType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $userRepository->add($user, true);
            $this->addFlash('success', 'Profil mis à jour');

            return $this->redirectToRoute('myspace_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('myspace/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }
}