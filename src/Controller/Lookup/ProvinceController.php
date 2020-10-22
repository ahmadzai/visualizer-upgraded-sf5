<?php

namespace App\Controller\Lookup;

use App\Entity\Province;
use App\Service\Settings;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Province controller.
 *
 * @Route("province")
 * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_EDITOR')")
 */
class ProvinceController extends AbstractController
{
    /**
     * Lists all province entities.
     *
     * @Route("/", name="province_index", methods={"GET"})
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $provinces = $em->getRepository('App:Province')->findAll();


        return $this->render('province/index.html.twig', array(
            'provinces' => $provinces,
            'tableSetting' => json_encode(Settings::tableSetting())
        ));
    }

    /**
     * @return Response
     * @Route("/select", name="province_select", methods={"GET"})
     */
    public function allProAction() {
        $em = $this->getDoctrine()->getManager();

        $provinces = $em->getRepository('App:District')->findAll();

        $select = "<select>";
        $options = "";
        foreach ($provinces as $province) {
            $options .= "<option>".$province->getDistrictName()."</option>";
        }

        $select = $select.$options;
        $select .= "</select>";

        return $this->render(':pages:test.html.twig', ['select' => $select]);


    }

    /**
     * Creates a new province entity.
     *
     * @Route("/new", name="province_new", methods={"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $province = new Province();
        $form = $this->createForm('App\Form\ProvinceType', $province);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($province);
            $em->flush();

            return $this->redirectToRoute('province_show', array('id' => $province->getId()));
        }

        return $this->render('province/new.html.twig', array(
            'province' => $province,
            'form' => $form->createView(),
            'change_breadcrumb' => true,
            'breadcrumb_text' => "Adding <small> new province</small>"
        ));
    }

    /**
     * Finds and displays a province entity.
     *
     * @Route("/{id}", name="province_show", methods={"GET"})
     */
    public function showAction(Province $province)
    {
        $deleteForm = $this->createDeleteForm($province);

        return $this->render('province/show.html.twig', array(
            'province' => $province,
            'delete_form' => $deleteForm->createView(),
            'change_breadcrumb' => true,
            'breadcrumb_text' => $province->getProvinceName()."<small> detail</small>"
        ));
    }

    /**
     * Displays a form to edit an existing province entity.
     *
     * @Route("/{id}/edit", name="province_edit", methods={"GET", "POST"})
     */
    public function editAction(Request $request, Province $province)
    {
        $deleteForm = $this->createDeleteForm($province);
        $editForm = $this->createForm('App\Form\ProvinceType', $province);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('province_edit', array('id' => $province->getId()));
        }

        return $this->render('province/edit.html.twig', array(
            'province' => $province,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'change_breadcrumb' => true,
            'breadcrumb_text' => $province->getProvinceName()."<small> edit</small>"
        ));
    }

    /**
     * Deletes a province entity.
     *
     * @Route("/{id}", name="province_delete", methods={"DELETE"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function deleteAction(Request $request, Province $province)
    {
        $form = $this->createDeleteForm($province);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($province);
            $em->flush();
        }

        return $this->redirectToRoute('province_index');
    }

    /**
     * Creates a form to delete a province entity.
     *
     * @param Province $province The province entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Province $province)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('province_delete', array('id' => $province->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
