<?php

namespace App\Controller\Bphs;

use App\Entity\BphsIndicatorReach;
use App\Form\BphsIndicatorReachType;
use App\Repository\BphsIndicatorReachRepository;
use mysql_xdevapi\Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/bphs/reach/indicator")
 */
class BphsIndicatorReachController extends AbstractController
{
    /**
     * @Route("/", name="bphs_indicator_reach_index", methods={"GET"})
     * @param BphsIndicatorReachRepository $bphsIndicatorReachRepository
     * @return Response
     */
    public function index(BphsIndicatorReachRepository $bphsIndicatorReachRepository): Response
    {
        return $this->render('bphs_plus/indicator_reach/index.html.twig', [
            'bphs_indicator_reaches' => $bphsIndicatorReachRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="bphs_indicator_reach_new", methods={"GET","POST"})
     * @Security("is_granted('ROLE_RESTRICTED_EDITOR')")
     */
    public function new(Request $request, SessionInterface $session): Response
    {

        $bphsIndicatorReach = new BphsIndicatorReach();
        $form = $this->createForm(BphsIndicatorReachType::class, $bphsIndicatorReach);
        //dd($request);
        $form->handleRequest($request);

        if(!$form->getErrors(true, true)->count())
            $session->remove('facilityYear');
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            /**
             * @TODO
             * The form is not working the way i designed it (it's not mapping indicator)
             * because of too many dependent dropdowns
             * Tried a lot to fix it, but couldn't therefore made this small hack
             * A small Hack to update indicator in case someone has changed it
             */
            if($bphsIndicatorReach->getIndicator() === null) {
                $indicator = $bphsIndicatorReach->getBphsHfIndicator()->getBphsIndicator();
                $bphsIndicatorReach->setIndicator($indicator);
            }
            $entityManager->persist($bphsIndicatorReach);
            $entityManager->flush();
            return $this->redirectToRoute('bphs_indicator_reach_index');
        }

        return $this->render('bphs_plus/indicator_reach/new.html.twig', [
            'bphs_indicator_reach' => $bphsIndicatorReach,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="bphs_indicator_reach_show", methods={"GET"})
     * @param BphsIndicatorReach $bphsIndicatorReach
     * @return Response
     */
    public function show(BphsIndicatorReach $bphsIndicatorReach): Response
    {
        return $this->render('bphs_plus/indicator_reach/show.html.twig', [
            'bphs_indicator_reach' => $bphsIndicatorReach,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="bphs_indicator_reach_edit", methods={"GET","POST"})
     * @param Request $request
     * @param BphsIndicatorReach $bphsIndicatorReach
     * @return Response
     * @Security("is_granted('ROLE_RESTRICTED_EDITOR')")
     */
    public function edit(Request $request, BphsIndicatorReach $bphsIndicatorReach, SessionInterface $session): Response
    {
        $form = $this->createForm(BphsIndicatorReachType::class, $bphsIndicatorReach);
        $form->handleRequest($request);
        if(!$form->getErrors(true, true)->count())
            $session->remove('facilityYear');
        if ($form->isSubmitted() && $form->isValid()) {
            // A small Hack to update indicator in case someone has changed it, though it will be called at any time
            // where there's an update request
            $bphsIndicatorReach->setIndicator($bphsIndicatorReach->getBphsHfIndicator()->getBphsIndicator());
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('bphs_indicator_reach_index');
        }

        return $this->render('bphs_plus/indicator_reach/edit.html.twig', [
            'bphs_indicator_reach' => $bphsIndicatorReach,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="bphs_indicator_reach_delete", methods={"DELETE"})
     * @param Request $request
     * @param BphsIndicatorReach $bphsIndicatorReach
     * @return Response
     * @Security("is_granted('ROLE_RESTRICTED_EDITOR')")
     */
    public function delete(Request $request, BphsIndicatorReach $bphsIndicatorReach): Response
    {
        if ($this->isCsrfTokenValid('delete'.$bphsIndicatorReach->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($bphsIndicatorReach);
            $entityManager->flush();
        }

        return $this->redirectToRoute('bphs_indicator_reach_index');
    }
}
