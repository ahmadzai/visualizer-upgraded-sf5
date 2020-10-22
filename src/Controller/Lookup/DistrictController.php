<?php

namespace App\Controller\Lookup;

use App\Entity\District;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use App\Service\Settings;

/**
 * District controller.
 *
 * @Route("district")
 * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_EDITOR')")
 */
class DistrictController extends AbstractController
{
    /**
     * Lists all district entities.
     *
     * @Route("/", name="district_index", methods={"GET"})
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $districts = $em->getRepository('App:District')->findAll();

        return $this->render('district/index.html.twig', array(
            'districts' => $districts,
            'tableSetting' => json_encode(Settings::tableSetting())
        ));
    }

    /**
     * Creates a new district entity.
     *
     * @Route("/new", name="district_new", methods={"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $district = new District();
        $form = $this->createForm('App\Form\DistrictType', $district);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($district);
            $em->flush();

            return $this->redirectToRoute('district_show', array('id' => $district->getId()));
        }

        return $this->render('district/new.html.twig', array(
            'district' => $district,
            'form' => $form->createView(),
            'change_breadcrumb' => true,
            'breadcrumb_text' => "Adding <small> a new district</small>"
        ));
    }

    /**
     * Finds and displays a district entity.
     *
     * @Route("/{id}", name="district_show", methods={"GET"})
     */
    public function showAction(District $district)
    {
        $deleteForm = $this->createDeleteForm($district);

        return $this->render('district/show.html.twig', array(
            'district' => $district,
            'delete_form' => $deleteForm->createView(),
            'change_breadcrumb' => true,
            'breadcrumb_text' => $district->getDistrictName()."<small> detail</small>"
        ));
    }

    /**
     * Displays a form to edit an existing district entity.
     *
     * @Route("/{id}/edit", name="district_edit", methods={"GET", "POST"})
     */
    public function editAction(Request $request, District $district)
    {
        $deleteForm = $this->createDeleteForm($district);
        $editForm = $this->createForm('App\Form\DistrictType', $district);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('district_edit', array('id' => $district->getId()));
        }

        return $this->render('district/edit.html.twig', array(
            'district' => $district,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'change_breadcrumb' => true,
            'breadcrumb_text' => $district->getDistrictName()."<small> edit</small>"
        ));
    }

    /**
     * Deletes a district entity.
     *
     * @Route("/{id}", name="district_delete", methods={"DELETE"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function deleteAction(Request $request, District $district)
    {
        $form = $this->createDeleteForm($district);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($district);
            $em->flush();
        }

        return $this->redirectToRoute('district_index');
    }

    /**
     * Creates a form to delete a district entity.
     *
     * @param District $district The district entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(District $district)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('district_delete', array('id' => $district->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
