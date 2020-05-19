<?php

namespace App\Controller\Bphs;

use App\Entity\BphsIndicator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Bphsindicator controller.
 *
 * @Route("bphs/indicator")
 */
class BphsIndicatorController extends AbstractController
{
    /**
     * Lists all bphsIndicator entities.
     *
     * @Route("/", name="bphs_indicator_index", methods={"GET"})
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $bphsIndicators = $em->getRepository('App:BphsIndicator')->findAll();

        return $this->render('bphs_plus/bphsindicator/index.html.twig', array(
            'bphsIndicators' => $bphsIndicators,
        ));
    }

    /**
     * Creates a new bphsIndicator entity.
     *
     * @Route("/new", name="bphs_indicator_new", methods={"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $bphsIndicator = new Bphsindicator();
        $form = $this->createForm('App\Form\BphsIndicatorType', $bphsIndicator);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($bphsIndicator);
            $em->flush();

            return $this->redirectToRoute('bphs_indicator_show', array('id' => $bphsIndicator->getId()));
        }

        return $this->render('bphs_plus/bphsindicator/new.html.twig', array(
            'bphsIndicator' => $bphsIndicator,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a bphsIndicator entity.
     *
     * @Route("/{id}", name="bphs_indicator_show", methods={"GET"})
     */
    public function showAction(BphsIndicator $bphsIndicator)
    {
        $deleteForm = $this->createDeleteForm($bphsIndicator);

        return $this->render('bphs_plus/bphsindicator/show.html.twig', array(
            'bphsIndicator' => $bphsIndicator,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing bphsIndicator entity.
     *
     * @Route("/{id}/edit", name="bphs_indicator_edit", methods={"GET", "POST"})
     */
    public function editAction(Request $request, BphsIndicator $bphsIndicator)
    {
        $deleteForm = $this->createDeleteForm($bphsIndicator);
        $editForm = $this->createForm('App\Form\BphsIndicatorType', $bphsIndicator);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('bphs_indicator_edit', array('id' => $bphsIndicator->getId()));
        }

        return $this->render('bphs_plus/bphsindicator/edit.html.twig', array(
            'bphsIndicator' => $bphsIndicator,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a bphsIndicator entity.
     *
     * @Route("/{id}", name="bphs_indicator_delete", methods={"DELETE"})
     */
    public function deleteAction(Request $request, BphsIndicator $bphsIndicator)
    {
        $form = $this->createDeleteForm($bphsIndicator);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($bphsIndicator);
            $em->flush();
        }

        return $this->redirectToRoute('bphs_indicator_index');
    }

    /**
     * Creates a form to delete a bphsIndicator entity.
     *
     * @param BphsIndicator $bphsIndicator The bphsIndicator entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(BphsIndicator $bphsIndicator)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('bphs_indicator_delete', array('id' => $bphsIndicator->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
