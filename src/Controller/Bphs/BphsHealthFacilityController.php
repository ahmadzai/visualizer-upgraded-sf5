<?php

namespace App\Controller\Bphs;

use App\Entity\BphsHealthFacility;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Bphshealthfacility controller.
 *
 * @Route("bphs/hf")
 */
class BphsHealthFacilityController extends AbstractController
{
    /**
     * Lists all bphsHealthFacility entities.
     *
     * @Route("/", name="bphs_hf_index", methods={"GET"})
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $bphsHealthFacilities = $em->getRepository('App:BphsHealthFacility')->findAll();

        return $this->render('bphs_plus/bphshealthfacility/index.html.twig', array(
            'bphsHealthFacilities' => $bphsHealthFacilities,
        ));
    }

    /**
     * Creates a new bphsHealthFacility entity.
     * @Security("is_granted('ROLE_RESTRICTED_EDITOR')")
     * @Route("/new", name="bphs_hf_new", methods={"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $bphsHealthFacility = new Bphshealthfacility();
        $form = $this->createForm('App\Form\BphsHealthFacilityType', $bphsHealthFacility);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($bphsHealthFacility);
            $em->flush();

            return $this->redirectToRoute('bphs_hf_show', array('id' => $bphsHealthFacility->getId()));
        }

        return $this->render('bphs_plus/bphshealthfacility/new.html.twig', array(
            'bphsHealthFacility' => $bphsHealthFacility,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a bphsHealthFacility entity.
     *
     * @Route("/{id}", name="bphs_hf_show", methods={"GET"}, requirements={"id"="\d+"})
     */
    public function showAction(BphsHealthFacility $bphsHealthFacility)
    {
        $deleteForm = $this->createDeleteForm($bphsHealthFacility);

        return $this->render('bphs_plus/bphshealthfacility/show.html.twig', array(
            'bphsHealthFacility' => $bphsHealthFacility,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing bphsHealthFacility entity.
     * @Security("is_granted('ROLE_RESTRICTED_EDITOR')")
     * @Route("/{id}/edit", name="bphs_hf_edit", methods={"GET", "POST"})
     */
    public function editAction(Request $request, BphsHealthFacility $bphsHealthFacility)
    {
        $deleteForm = $this->createDeleteForm($bphsHealthFacility);
        $editForm = $this->createForm('App\Form\BphsHealthFacilityType', $bphsHealthFacility);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('bphs_hf_edit', array('id' => $bphsHealthFacility->getId()));
        }

        return $this->render('bphs_plus/bphshealthfacility/edit.html.twig', array(
            'bphsHealthFacility' => $bphsHealthFacility,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a bphsHealthFacility entity.
     * @Security("is_granted('ROLE_RESTRICTED_EDITOR')")
     * @Route("/{id}", name="bphs_hf_delete", methods={"DELETE"})
     */
    public function deleteAction(Request $request, BphsHealthFacility $bphsHealthFacility)
    {
        $form = $this->createDeleteForm($bphsHealthFacility);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($bphsHealthFacility);
            $em->flush();
        }

        return $this->redirectToRoute('bphs_hf_index');
    }

    /**
     * Creates a form to delete a bphsHealthFacility entity.
     *
     * @param BphsHealthFacility $bphsHealthFacility The bphsHealthFacility entity
     *
     * @return \Symfony\Component\Form\FormInterface The form
     */
    private function createDeleteForm(BphsHealthFacility $bphsHealthFacility)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('bphs_hf_delete', array('id' => $bphsHealthFacility->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
