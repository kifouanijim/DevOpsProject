<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class GestionController extends AbstractController
{
    private array $albums = [
        ['id' => 1, 'nom' => 'Album 1', 'auteur' => 'Auteur 1', 'date' => '2023-01-01'],
        ['id' => 2, 'nom' => 'Album 2', 'auteur' => 'Auteur 2', 'date' => '2023-02-01'],
        ['id' => 3, 'nom' => 'Album 3', 'auteur' => 'Auteur 3', 'date' => '2023-03-01'],
    ];

    #[Route('/gestion', name: 'app_gestion')]
    public function index(): Response
    {
        return $this->render('gestion/index.html.twig', [
            'albums' => $this->albums,
        ]);
    }

    #[Route('/gestion/add', name: 'app_gestion_add', methods: ['GET', 'POST'])]
    public function add(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            // Récupérer les données du formulaire
            $nom = $request->request->get('nom');
            $auteur = $request->request->get('auteur');
            $date = $request->request->get('date');

            // Ajouter l'album aux données en dur
            $newAlbum = [
                'id' => count($this->albums) + 1,
                'nom' => $nom,
                'auteur' => $auteur,
                'date' => $date,
            ];
            $this->albums[] = $newAlbum;

            // Redirection vers la liste
            return $this->redirectToRoute('app_gestion');
        }

        return $this->render('gestion/add.html.twig');
    }

    #[Route('/gestion/edit/{id}', name: 'app_gestion_edit', methods: ['GET', 'POST'])]
    public function edit(int $id, Request $request): Response
    {
        $album = array_filter($this->albums, fn($a) => $a['id'] === $id);
        $album = reset($album); // Récupère le premier élément correspondant

        if (!$album) {
            throw $this->createNotFoundException('Album non trouvé');
        }

        if ($request->isMethod('POST')) {
            $nom = $request->request->get('nom');
            $auteur = $request->request->get('auteur');
            $date = $request->request->get('date');

            // Modifier l'album
            foreach ($this->albums as &$a) {
                if ($a['id'] === $id) {
                    $a['nom'] = $nom;
                    $a['auteur'] = $auteur;
                    $a['date'] = $date;
                    break;
                }
            }

            return $this->redirectToRoute('app_gestion');
        }

        return $this->render('gestion/edit.html.twig', [
            'album' => $album,
        ]);
    }

    /*#[Route('/gestion/delete/{id}', name: 'app_gestion_delete', methods: ['POST'])]
    public function delete(int $id): Response
    {
        $this->albums = array_filter($this->albums, fn($a) => $a['id'] !== $id);

        return $this->redirectToRoute('app_gestion');
    }*/
}
