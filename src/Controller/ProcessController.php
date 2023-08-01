<?php

namespace App\Controller;

use App\Form\CalculateFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProcessController extends AbstractController
{
    #[Route(path: '/', name: 'process', methods: ['GET'])]
    public function list(): Response
    {
        $calculateForm = $this->createForm(CalculateFormType::class, [], [
            'action' => $this->generateUrl('calculate_price')
        ]);

        return $this->render('process.html.twig', ['calculateForm' => $calculateForm->createView()]);
    }
}