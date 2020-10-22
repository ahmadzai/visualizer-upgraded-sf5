<?php

namespace App\Controller\Bphs;

use App\Entity\BphsHfIndicator;
use App\EventListener\HfIndicatorCopyListener;
use App\EventSubscriber\CopyHfIndicatorEvent;
use App\Form\BphsHfIndicatorType;
use App\Form\CopyIndicatorToHfType;
use App\Repository\BphsHfIndicatorRepository;
use App\Service\HfIndicatorCopyService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcher;
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
     * @Security("is_granted('ROLE_RESTRICTED_EDITOR')")
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
     * @Route("/{id}", name="bphs_hf_indicator_show", methods={"GET"}, requirements={"id"="\d+"})
     */
    public function show(BphsHfIndicator $bphsHfIndicator): Response
    {
        return $this->render('bphs_plus/bphshfindicator/show.html.twig', [
            'bphs_hf_indicator' => $bphsHfIndicator,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="bphs_hf_indicator_edit", methods={"GET","POST"})
     * @Security("is_granted('ROLE_RESTRICTED_EDITOR')")
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
     * @Security("is_granted('ROLE_RESTRICTED_EDITOR')")
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

    /**
     * @param Request $request
     * @param HfIndicatorCopyService $copyService
     * @return Response
     * @Route("/copy", name="bphs_hf_indicator_copy")
     * @Security("is_granted('ROLE_RESTRICTED_EDITOR')")
     */
    public function copyHfIndicators(Request $request, HfIndicatorCopyService $copyService) : Response
    {
        $form = $this->createForm(CopyIndicatorToHfType::class, []);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $data = $form->getData();
            $result = $copyService->doCopy($data);
            foreach($result as $flash=>$message)
                $this->addFlash($flash, $message);

            return $this->redirectToRoute('bphs_hf_indicator_index');
        }

        return $this->render('bphs_plus/bphshfindicator/copy.html.twig', [
            'form' => $form->createView(),
        ]);

    }
}
