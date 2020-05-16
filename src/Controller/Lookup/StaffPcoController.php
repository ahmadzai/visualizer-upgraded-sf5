<?php

namespace App\Controller\Lookup;

use App\Entity\StaffPco;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Staffpco controller.
 *
 * @Route("staff-pco")
 */
class StaffPcoController extends AbstractController
{
    /**
     * Lists all staffPco entities.
     *
     * @Route("/", name="staff-pco_index", methods={"GET"})
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $staffPcos = $em->getRepository('App:StaffPco')->findAll();

        return $this->render('staffpco/index.html.twig', array(
            'staffPcos' => $staffPcos,
        ));
    }

    /**
     * Creates a new staffPco entity.
     *
     * @Route("/new", name="staff-pco_new", methods={"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $staffPco = new Staffpco();
        $form = $this->createForm('App\Form\StaffPcoType', $staffPco);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($staffPco);
            $em->flush();

            return $this->redirectToRoute('staff-pco_show', array('id' => $staffPco->getId()));
        }

        return $this->render('staffpco/new.html.twig', array(
            'staffPco' => $staffPco,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a staffPco entity.
     *
     * @Route("/{id}", name="staff-pco_show", methods={"GET"})
     */
    public function showAction(StaffPco $staffPco)
    {
        $deleteForm = $this->createDeleteForm($staffPco);

        return $this->render('staffpco/show.html.twig', array(
            'staffPco' => $staffPco,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing staffPco entity.
     *
     * @Route("/{id}/edit", name="staff-pco_edit", methods={"GET", "POST"})
     */
    public function editAction(Request $request, StaffPco $staffPco)
    {
        $deleteForm = $this->createDeleteForm($staffPco);
        $editForm = $this->createForm('App\Form\StaffPcoType', $staffPco);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('staff-pco_edit', array('id' => $staffPco->getId()));
        }

        return $this->render('staffpco/edit.html.twig', array(
            'staffPco' => $staffPco,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a staffPco entity.
     *
     * @Route("/{id}", name="staff-pco_delete", methods={"DELETE"})
     */
    public function deleteAction(Request $request, StaffPco $staffPco)
    {
        $form = $this->createDeleteForm($staffPco);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($staffPco);
            $em->flush();
        }

        return $this->redirectToRoute('staff-pco_index');
    }

    /**
     * Creates a form to delete a staffPco entity.
     *
     * @param StaffPco $staffPco The staffPco entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(StaffPco $staffPco)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('staff-pco_delete', array('id' => $staffPco->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    /**
     * @param Request $request
     * @Route("/icn/import", name="import_staff_icn")
     * @return Response
     */
    public function importSmCcsDataAction(Request $request) {
        return $this->render("staffpco/import.html.twig", []);
    }
}
