<?php

namespace App\Controller\Bphs;

use App\Entity\BphsIndicatorReach;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Bphsindicatorreach controller.
 *
 * @Route("bphs/reach/indicator")
 */
class BphsIndicatorReachController extends AbstractController
{
    /**
     * Lists all bphsIndicatorReach entities.
     *
     * @Route("/", name="bphs_indicator_reach_index", methods={"GET"})
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $bphsIndicatorReaches = $em->getRepository('App:BphsIndicatorReach')->findAll();

        return $this->render('bphs_plus/bphsindicatorreach/index.html.twig', array(
            'bphsIndicatorReaches' => $bphsIndicatorReaches,
        ));
    }

    /**
     * Creates a new bphsIndicatorReach entity.
     *
     * @Route("/new", name="bphs_indicator_reach_new", methods={"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $bphsIndicatorReach = new Bphsindicatorreach();
        $form = $this->createForm('App\Form\BphsIndicatorReachType', $bphsIndicatorReach);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($bphsIndicatorReach);
            $em->flush();

            return $this->redirectToRoute('bphs_indicator_reach_show', array('id' => $bphsIndicatorReach->getId()));
        }

        return $this->render('bphs_plus/bphsindicatorreach/new.html.twig', array(
            'bphsIndicatorReach' => $bphsIndicatorReach,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a bphsIndicatorReach entity.
     *
     * @Route("/{id}", name="bphs_indicator_reach_show", methods={"GET"})
     */
    public function showAction(BphsIndicatorReach $bphsIndicatorReach)
    {
        $deleteForm = $this->createDeleteForm($bphsIndicatorReach);

        return $this->render('bphs_plus/bphsindicatorreach/show.html.twig', array(
            'bphsIndicatorReach' => $bphsIndicatorReach,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing bphsIndicatorReach entity.
     *
     * @Route("/{id}/edit", name="bphs_indicator_reach_edit", methods={"GET", "POST"})
     */
    public function editAction(Request $request, BphsIndicatorReach $bphsIndicatorReach)
    {
        $deleteForm = $this->createDeleteForm($bphsIndicatorReach);
        $editForm = $this->createForm('App\Form\BphsIndicatorReachType', $bphsIndicatorReach);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('bphs_indicator_reach_edit', array('id' => $bphsIndicatorReach->getId()));
        }

        return $this->render('bphs_plus/bphsindicatorreach/edit.html.twig', array(
            'bphsIndicatorReach' => $bphsIndicatorReach,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a bphsIndicatorReach entity.
     *
     * @Route("/{id}", name="bphs_indicator_reach_delete", methods={"DELETE"})
     */
    public function deleteAction(Request $request, BphsIndicatorReach $bphsIndicatorReach)
    {
        $form = $this->createDeleteForm($bphsIndicatorReach);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($bphsIndicatorReach);
            $em->flush();
        }

        return $this->redirectToRoute('bphs_indicator_reach_index');
    }

    /**
     * Creates a form to delete a bphsIndicatorReach entity.
     *
     * @param BphsIndicatorReach $bphsIndicatorReach The bphsIndicatorReach entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(BphsIndicatorReach $bphsIndicatorReach)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('bphs_indicator_reach_delete', array('id' => $bphsIndicatorReach->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
