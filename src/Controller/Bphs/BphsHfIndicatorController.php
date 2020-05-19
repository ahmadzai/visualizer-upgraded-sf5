<?php

namespace App\Controller\Bphs;

use App\Entity\BphsHfIndicator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Bphshfindicator controller.
 *
 * @Route("bphs/hf/indicator")
 */
class BphsHfIndicatorController extends AbstractController
{
    /**
     * Lists all bphsHfIndicator entities.
     *
     * @Route("/", name="bphs_hf_indicator_index", methods={"GET"})
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $bphsHfIndicators = $em->getRepository('App:BphsHfIndicator')->findAll();

        return $this->render('bphs_plus/bphshfindicator/index.html.twig', array(
            'bphsHfIndicators' => $bphsHfIndicators,
        ));
    }

    /**
     * Creates a new bphsHfIndicator entity.
     *
     * @Route("/new", name="bphs_hf_indicator_new", methods={"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $bphsHfIndicator = new Bphshfindicator();
        $form = $this->createForm('App\Form\BphsHfIndicatorType', $bphsHfIndicator);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($bphsHfIndicator);
            $em->flush();

            return $this->redirectToRoute('bphs_hf_indicator_show', array('id' => $bphsHfIndicator->getId()));
        }

        return $this->render('bphs_plus/bphshfindicator/new.html.twig', array(
            'bphsHfIndicator' => $bphsHfIndicator,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a bphsHfIndicator entity.
     *
     * @Route("/{id}", name="bphs_hf_indicator_show", methods={"GET"})
     */
    public function showAction(BphsHfIndicator $bphsHfIndicator)
    {
        $deleteForm = $this->createDeleteForm($bphsHfIndicator);

        return $this->render('bphs_plus/bphshfindicator/show.html.twig', array(
            'bphsHfIndicator' => $bphsHfIndicator,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing bphsHfIndicator entity.
     *
     * @Route("/{id}/edit", name="bphs_hf_indicator_edit", methods={"GET", "POST"})
     */
    public function editAction(Request $request, BphsHfIndicator $bphsHfIndicator)
    {
        $deleteForm = $this->createDeleteForm($bphsHfIndicator);
        $editForm = $this->createForm('App\Form\BphsHfIndicatorType', $bphsHfIndicator);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('bphs_hf_indicator_edit', array('id' => $bphsHfIndicator->getId()));
        }

        return $this->render('bphs_plus/bphshfindicator/edit.html.twig', array(
            'bphsHfIndicator' => $bphsHfIndicator,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a bphsHfIndicator entity.
     *
     * @Route("/{id}", name="bphs_hf_indicator_delete", methods={"DELETE"})
     */
    public function deleteAction(Request $request, BphsHfIndicator $bphsHfIndicator)
    {
        $form = $this->createDeleteForm($bphsHfIndicator);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($bphsHfIndicator);
            $em->flush();
        }

        return $this->redirectToRoute('bphs_hf_indicator_index');
    }

    /**
     * Creates a form to delete a bphsHfIndicator entity.
     *
     * @param BphsHfIndicator $bphsHfIndicator The bphsHfIndicator entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(BphsHfIndicator $bphsHfIndicator)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('bphs_hf_indicator_delete', array('id' => $bphsHfIndicator->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
