<?php

namespace App\Controller\Covid19;

use App\Entity\Covid19Cases;
use App\Service\Maps;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("covid-19")
 * @Security("is_granted('ROLE_PARTNER') or is_granted('ROLE_NORMAL_USER') or is_granted('ROLE_COVID19_VIEWER')")
 */
class CasesController extends AbstractController
{

    /**
     * Lists all covid19Case entities.
     *
     * @Route("/", name="covid19_cases", methods={"GET"})
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $totalCases = $em->getRepository('App:Covid19Cases')->totalCases();
        $casesByRegion = $em->getRepository('App:Covid19Cases')->casesByRegion();
        $covid19Cases = $em->getRepository('App:Covid19Cases')->findAll();

        $lastUpdated = $em->getRepository("App:Covid19Cases")->getLastUpdated();

        $tableSettings = array(
            "order" => [[ 2, "desc" ]],
            'pageLength'=> -1,
            'lengthMenu' => [
                [15, 25, -1],
                [15, 25, 'All']
                ]
        );

        return $this->render('covid19/cases/index.html.twig', array(
            'covid19Cases' => $covid19Cases,
            'totalCases' => $totalCases,
            'casesByRegion' => $casesByRegion,
            'source'=>'Covid19Cases',
            'url' => 'covid19_cases',
            'lastUpdated' => $lastUpdated,
            'tableSetting' => json_encode($tableSettings),
            'province' => 'all',
        ));
    }

    /**
     * Creates a new covid19Case entity.
     *
     * @Route("/new", name="covid19_cases_new", methods={"GET", "POST"})
     * @Security("is_granted('ROLE_COVID19_EDITOR')")
     */
    public function newAction(Request $request)
    {
        $covid19Case = new Covid19Cases();
        $form = $this->createForm('App\Form\Covid19CasesType', $covid19Case);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($covid19Case);
            $em->flush();

            return $this->redirectToRoute('covid19_cases_show', array('id' => $covid19Case->getId()));
        }

        return $this->render('covid19/cases/new.html.twig', array(
            'covid19Case' => $covid19Case,
            'form' => $form->createView(),
            'change_breadcrumb' => true,
            'breadcrumb_text' => "Cases <small> Management Page</small>"
        ));
    }

    /**
     * Finds and displays a covid19Case entity.
     *
     * @Route("/{id}", name="covid19_cases_show", methods={"GET"})
     */
    public function showAction(Covid19Cases $covid19Case)
    {
        $deleteForm = $this->createDeleteForm($covid19Case);
        $province = $covid19Case->getProvince();

        $em = $this->getDoctrine()->getManager();
        $totalCases = $em->getRepository('App:Covid19Cases')->totalCases($province);

        return $this->render('covid19/cases/show.html.twig', array(
            'covid19Case' => $covid19Case,
            'delete_form' => $deleteForm->createView(),
            'change_breadcrumb' => true,
            'breadcrumb_text' => $covid19Case->getProvince()->getProvinceName()."<small> COVID-19 Status</small>",
            'source'=>'Covid19Cases',
            'url' => 'covid19_cases',
            'totalCases' => $totalCases,
            'province' => $province->getId()
        ));
    }

    /**
     * Displays a form to edit an existing covid19Case entity.
     *
     * @Route("/{id}/edit", name="covid19_cases_edit", methods={"GET", "POST"})
     * @Security("is_granted('ROLE_COVID19_EDITOR')")
     */
    public function editAction(Request $request, Covid19Cases $covid19Case)
    {
        $deleteForm = $this->createDeleteForm($covid19Case);
        $editForm = $this->createForm('App\Form\Covid19CasesType', $covid19Case);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('covid19_cases_edit', array('id' => $covid19Case->getId()));
        }

        return $this->render('covid19/cases/edit.html.twig', array(
            'covid19Case' => $covid19Case,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'change_breadcrumb' => true,
            'breadcrumb_text' => "Cases <small> Management Page</small>"
        ));
    }

    /**
     * Deletes a covid19Case entity.
     *
     * @Route("/{id}", name="covid19_cases_delete", methods={"DELETE"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function deleteAction(Request $request, Covid19Cases $covid19Case)
    {
        $form = $this->createDeleteForm($covid19Case);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($covid19Case);
            $em->flush();
        }

        return $this->redirectToRoute('covid19_cases');
    }

    /**
     * Lists all covid19Case entities.
     *
     * @Route("/ajax/map", name="ajax_covid19_cases", options={"expose"=true}, methods={"GET", "POST"})
     */
    public function getCasesForMap(Request $request)
    {
        $province = $request->get('province');
        $province = $province === 'all' ? null : (int) $province;
        $em = $this->getDoctrine()->getManager();
        $cases = $em->getRepository('App:Covid19Cases')->selectForMap($province);
        //dump($cases); die;
        return new JsonResponse(['map_data' => json_encode($cases)]);
    }


    /**
     * Creates a form to delete a covid19Case entity.
     *
     * @param Covid19Cases $covid19Case The covid19Case entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Covid19Cases $covid19Case)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('covid19_cases_delete', array('id' => $covid19Case->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

}
