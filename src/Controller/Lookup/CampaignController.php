<?php

namespace App\Controller\Lookup;

use App\Entity\Campaign;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use App\Service\Settings;

/**
 * Campaign controller.
 *
 * @Route("campaign")
 * @Security("is_granted('ROLE_EDITOR')")
 */
class CampaignController extends AbstractController
{
    /**
     * Lists all campaign entities.
     *
     * @Route("/", name="campaign_index", methods={"GET"})
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $campaigns = $em->getRepository('App:Campaign')->findBy([], ['id'=>'DESC']);

        $tableSetting = Settings::tableSetting();
        $tableSetting['order'] = [[ 0, "desc" ]];

        return $this->render('campaign/index.html.twig', array(
            'campaigns' => $campaigns,
            'tableSetting' => json_encode($tableSetting)
        ));
    }

    /**
     * Creates a new campaign entity.
     *
     * @Route("/new", name="campaign_new", methods={"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $campaign = new Campaign();
        $campaign->setUser($this->getUser());
        $form = $this->createForm('App\Form\CampaignType', $campaign);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($campaign);
            $em->flush();

            return $this->redirectToRoute('campaign_show', array('id' => $campaign->getId()));
        }

        return $this->render('campaign/new.html.twig', array(
            'campaign' => $campaign,
            'form' => $form->createView(),
            'change_breadcrumb' => true,
            'breadcrumb_text' => "Adding a new"."<small> campaign</small>"
        ));
    }

    /**
     * Finds and displays a campaign entity.
     *
     * @Route("/{id}", name="campaign_show", methods={"GET"})
     */
    public function showAction(Campaign $campaign)
    {
        $deleteForm = $this->createDeleteForm($campaign);

        return $this->render('campaign/show.html.twig', array(
            'campaign' => $campaign,
            'delete_form' => $deleteForm->createView(),
            'change_breadcrumb' => true,
            'breadcrumb_text' => $campaign->getCampaignName()."<small> detail</small>"
        ));
    }

    /**
     * Displays a form to edit an existing campaign entity.
     *
     * @Route("/{id}/edit", name="campaign_edit", methods={"GET", "POST"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function editAction(Request $request, Campaign $campaign)
    {
        $deleteForm = $this->createDeleteForm($campaign);
        $campaign->setUser($this->getUser());
        $editForm = $this->createForm('App\Form\CampaignType', $campaign);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('campaign_edit', array('id' => $campaign->getId()));
        }

        return $this->render('campaign/edit.html.twig', array(
            'campaign' => $campaign,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'change_breadcrumb' => true,
            'breadcrumb_text' => $campaign->getCampaignName()."<small> edit</small>"
        ));
    }

    /**
     * Deletes a campaign entity.
     *
     * @Route("/{id}", name="campaign_delete", methods={"DELETE"})
     * @Security("is_granted('EROLE_ADMIN')")
     */
    public function deleteAction(Request $request, Campaign $campaign)
    {
        $form = $this->createDeleteForm($campaign);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($campaign);
            $em->flush();
        }

        return $this->redirectToRoute('campaign_index');
    }

    /**
     * Creates a form to delete a campaign entity.
     *
     * @param Campaign $campaign The campaign entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Campaign $campaign)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('campaign_delete', array('id' => $campaign->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
