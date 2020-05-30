<?php

namespace App\Controller\Bphs;

use App\Entity\BphsHfIndicator;
use App\Form\BphsHfIndicatorType;
use App\Repository\BphsHfIndicatorRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/bphs/hf/indicator")
 */
class BphsHfIndicatorController extends AbstractController
{
    /**
     * @Route("/", name="bphs_hf_indicator_index", methods={"GET"})
     */
    public function index(BphsHfIndicatorRepository $bphsHfIndicatorRepository): Response
    {
        $bphs_hf_indicators = $bphsHfIndicatorRepository->findAll();
        return $this->render('bphs_plus/bphshfindicator/index.html.twig', [
            'bphs_hf_indicators' => $bphs_hf_indicators,
        ]);
    }

    /**
     * @Route("/new", name="bphs_hf_indicator_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $bphsHfIndicator = new BphsHfIndicator();
        $form = $this->createForm(BphsHfIndicatorType::class, $bphsHfIndicator);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($bphsHfIndicator);
            $entityManager->flush();

            return $this->redirectToRoute('bphs_hf_indicator_index');
        }

        return $this->render('bphs_plus/bphshfindicator/new.html.twig', [
            'bphs_hf_indicator' => $bphsHfIndicator,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="bphs_hf_indicator_show", methods={"GET"})
     */
    public function show(BphsHfIndicator $bphsHfIndicator): Response
    {
        return $this->render('bphs_plus/bphshfindicator/show.html.twig', [
            'bphs_hf_indicator' => $bphsHfIndicator,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="bphs_hf_indicator_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, BphsHfIndicator $bphsHfIndicator): Response
    {
        $form = $this->createForm(BphsHfIndicatorType::class, $bphsHfIndicator);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('bphs_hf_indicator_index');
        }

        return $this->render('bphs_plus/bphshfindicator/edit.html.twig', [
            'bphs_hf_indicator' => $bphsHfIndicator,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="bphs_hf_indicator_delete", methods={"DELETE"})
     */
    public function delete(Request $request, BphsHfIndicator $bphsHfIndicator): Response
    {
        if ($this->isCsrfTokenValid('delete'.$bphsHfIndicator->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($bphsHfIndicator);
            $entityManager->flush();
        }

        return $this->redirectToRoute('bphs_hf_indicator_index');
    }
}
