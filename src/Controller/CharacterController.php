<?php

namespace App\Controller;

use App\Entity\Character;
use App\Form\CharacterType;
use App\Repository\CharacterRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class CharacterController extends AbstractController
{
    #[Route('/', name: 'character_index', methods: ['GET'])]
    public function index(Request $request, CharacterRepository $characterRepository): Response
    {
        $search = $request->query->get('search');
        if ($search) {
            $characters = $characterRepository->findByName($search);
        } else {
            $characters = $characterRepository->findAll();
        }

        return $this->render('character/index.html.twig', [
            'characters' => $characters,
        ]);
    }

    #[Route('/character/edit/{id}', name: 'character_edit', methods: ['GET', 'POST'])]
    public function edit(Character $character, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CharacterType::class, $character);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $pictureFile */
            $pictureFile = $form->get('picture')->getData();
            if ($pictureFile) {
                $newFilename = uniqid().'.'.$pictureFile->guessExtension();
                try {
                    $pictureFile->move(
                        $this->getParameter('pictures_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Handle exception if something happens during file upload
                }
                $character->setPicture($newFilename);
            }

            $entityManager->flush();

            return $this->redirectToRoute('character_index');
        }

        return $this->render('character/edit.html.twig', [
            'character' => $character,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/character/remove-picture/{id}', name: 'character_remove_picture', methods: ['POST'])]
    public function removePicture(Character $character, EntityManagerInterface $entityManager): Response
    {
        $character->setPicture(null);
        $entityManager->flush();

        return $this->redirectToRoute('character_edit', ['id' => $character->getId()]);
    }

    #[Route('/character/delete/{id}', name: 'character_delete', methods: ['POST'])]
    public function delete(Character $character, Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$character->getId(), $request->request->get('_token'))) {
            $entityManager->remove($character);
            $entityManager->flush();
        }

        return $this->redirectToRoute('character_index');
    }
}
