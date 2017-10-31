<?php

namespace Loevgaard\PakkelabelsBundle\Controller;

use Loevgaard\PakkelabelsBundle\Entity\ShippingMethodMapping;
use Loevgaard\PakkelabelsBundle\Form\ShippingMethodMappingType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/shipping-method-mapping")
 */
class ShippingMethodMappingController extends Controller
{
    /**
     * @Method("GET")
     * @Route("", name="loevgaard_pakkelabels_shipping_method_mapping_index")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $repos = $this->get('loevgaard_pakkelabels.shipping_method_mapping_repository');

        /** @var ShippingMethodMapping[] $shippingMethodMappings */
        $shippingMethodMappings = $repos->findAllWithPaging($request->query->getInt('page', 1));

        return $this->render('@LoevgaardPakkelabels/shipping_method_mapping/index.html.twig', [
            'shippingMethodMappings' => $shippingMethodMappings,
        ]);
    }

    /**
     * @Method("GET")
     * @Route("/{id}/show", name="loevgaard_pakkelabels_shipping_method_mapping_show")
     *
     * @param ShippingMethodMapping $shippingMethodMapping
     *
     * @return Response
     */
    public function showAction(ShippingMethodMapping $shippingMethodMapping)
    {
        return $this->render('@LoevgaardPakkelabels/shipping_method_mapping/show.html.twig', [
            'shippingMethodMapping' => $shippingMethodMapping,
        ]);
    }

    /**
     * @Method({"GET", "POST"})
     * @Route("/new", name="loevgaard_pakkelabels_shipping_method_mapping_new")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function newAction(Request $request)
    {
        $shippingMethodMapping = new ShippingMethodMapping();
        $form = $this->getForm($shippingMethodMapping);
        $res = $this->handleUpdate($form, $shippingMethodMapping, $request);
        if ($res) {
            return $res;
        }

        return $this->updateResponse($shippingMethodMapping, $form);
    }

    /**
     * @Method({"GET", "POST"})
     * @Route("/{id}/edit", name="loevgaard_pakkelabels_shipping_method_mapping_edit")
     *
     * @param ShippingMethodMapping $shippingMethodMapping
     * @param Request        $request
     *
     * @return Response
     */
    public function editAction(ShippingMethodMapping $shippingMethodMapping, Request $request)
    {
        $form = $this->getForm($shippingMethodMapping);
        $res = $this->handleUpdate($form, $shippingMethodMapping, $request);
        if ($res) {
            return $res;
        }

        return $this->updateResponse($shippingMethodMapping, $form);
    }

    /**
     * @param Form           $form
     * @param ShippingMethodMapping $shippingMethodMapping
     * @param Request        $request
     *
     * @return null|RedirectResponse
     */
    private function handleUpdate(Form $form, ShippingMethodMapping $shippingMethodMapping, Request $request)
    {
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($shippingMethodMapping);
            $em->flush();

            $translator = $this->get('translator');

            $this->addFlash(
                'success',
                $translator->trans('shipping_method_mapping.edit.saved', [], 'LoevgaardPakkelabelsBundle')
            );

            return $this->redirectToRoute('loevgaard_pakkelabels_shipping_method_mapping_edit', [
                'id' => $shippingMethodMapping->getId(),
            ]);
        }

        return null;
    }

    /**
     * @param ShippingMethodMapping $shippingMethodMapping
     * @param Form           $form
     *
     * @return Response
     */
    private function updateResponse(ShippingMethodMapping $shippingMethodMapping, Form $form): Response
    {
        return $this->render('@LoevgaardPakkelabels/shipping_method_mapping/edit.html.twig', [
            'shippingMethodMapping' => $shippingMethodMapping,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param ShippingMethodMapping $shippingMethodMapping
     *
     * @return Form
     */
    private function getForm(ShippingMethodMapping $shippingMethodMapping): Form
    {
        return $form = $this->createForm(ShippingMethodMappingType::class, $shippingMethodMapping);
    }
}
