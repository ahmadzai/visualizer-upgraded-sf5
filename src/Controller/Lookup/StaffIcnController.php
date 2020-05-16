<?php

namespace App\Controller\Lookup;

use App\Entity\StaffIcn;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Stafficn controller.
 *
 * @Route("staff-icn")
 */
class StaffIcnController extends AbstractController
{
    /**
     * Lists all staffIcn entities.
     *
     * @Route("/", name="staff-icn_index", methods={"GET"})
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $staffIcns = $em->getRepository('App:StaffIcn')->findAll();

        return $this->render('stafficn/index.html.twig', array(
            'staffIcns' => $staffIcns,
        ));
    }

    /**
     * Creates a new staffIcn entity.
     *
     * @Route("/new", name="staff-icn_new", methods={"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $staffIcn = new Stafficn();
        $form = $this->createForm('App\Form\StaffIcnType', $staffIcn);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($staffIcn);
            $em->flush();

            return $this->redirectToRoute('staff-icn_show', array('id' => $staffIcn->getId()));
        }

        return $this->render('stafficn/new.html.twig', array(
            'staffIcn' => $staffIcn,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a staffIcn entity.
     *
     * @Route("/{id}", name="staff-icn_show", methods={"GET"})
     */
    public function showAction(StaffIcn $staffIcn)
    {
        $deleteForm = $this->createDeleteForm($staffIcn);

        return $this->render('stafficn/show.html.twig', array(
            'staffIcn' => $staffIcn,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing staffIcn entity.
     *
     * @Route("/{id}/edit", name="staff-icn_edit", methods={"GET", "POST"})
     */
    public function editAction(Request $request, StaffIcn $staffIcn)
    {
        $deleteForm = $this->createDeleteForm($staffIcn);
        $editForm = $this->createForm('App\Form\StaffIcnType', $staffIcn);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('staff-icn_edit', array('id' => $staffIcn->getId()));
        }

        return $this->render('stafficn/edit.html.twig', array(
            'staffIcn' => $staffIcn,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a staffIcn entity.
     *
     * @Route("/{id}", name="staff-icn_delete", methods={"DELETE"})
     */
    public function deleteAction(Request $request, StaffIcn $staffIcn)
    {
        $form = $this->createDeleteForm($staffIcn);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($staffIcn);
            $em->flush();
        }

        return $this->redirectToRoute('staff-icn_index');
    }

    /**
     * Creates a form to delete a staffIcn entity.
     *
     * @param StaffIcn $staffIcn The staffIcn entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(StaffIcn $staffIcn)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('staff-icn_delete', array('id' => $staffIcn->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
