<?php

namespace Loevgaard\DandomainAltapayBundle\Controller;

use Loevgaard\DandomainAltapayBundle\Form\CountryMappingType;
use Loevgaard\PakkelabelsBundle\Entity\CountryMapping;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/country-mapping")
 */
class CountryMappingController extends Controller
{
    /**
     * @Method("GET")
     * @Route("", name="loevgaard_pakkelabels_country_mapping_index")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        /** @var CountryMapping[] $countryMappings */
        $countryMappings = $this->get('doctrine')
            ->getManager()
            ->getRepository('LoevgaardPakkelabelsBundle:CountryMapping')
            ->findAll();

        return $this->render('@LoevgaardPakkelabels/country_mapping/index.html.twig', [
            'countryMappings' => $countryMappings,
        ]);
    }

    /**
     * @Method("GET")
     * @Route("/{id}/show", name="loevgaard_pakkelabels_country_mapping_show")
     *
     * @param CountryMapping $countryMapping
     *
     * @return Response
     */
    public function showAction(CountryMapping $countryMapping)
    {
        return $this->render('@LoevgaardPakkelabels/country_mapping/show.html.twig', [
            'countryMapping' => $countryMapping,
        ]);
    }

    /**
     * @Method({"GET", "POST"})
     * @Route("/new", name="loevgaard_pakkelabels_country_mapping_new")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function newAction(Request $request)
    {
        $countryMapping = new CountryMapping();
        $form = $this->getForm($countryMapping);
        $res = $this->handleUpdate($form, $countryMapping, $request);
        if($res) {
            return $res;
        }

        return $this->updateResponse($countryMapping, $form);
    }

    /**
     * @Method({"GET", "POST"})
     * @Route("/{id}/edit", name="loevgaard_pakkelabels_country_mapping_edit")
     *
     * @param CountryMapping $countryMapping
     * @param Request $request
     *
     * @return Response
     */
    public function editAction(CountryMapping $countryMapping, Request $request)
    {
        $form = $this->getForm($countryMapping);
        $res = $this->handleUpdate($form, $countryMapping, $request);
        if($res) {
            return $res;
        }

        return $this->updateResponse($countryMapping, $form);
    }

    /**
     * @param Form $form
     * @param CountryMapping $countryMapping
     * @param Request $request
     * @return null|RedirectResponse
     */
    private function handleUpdate(Form $form, CountryMapping $countryMapping, Request $request)
    {
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $new = is_null($countryMapping->getId());

            $em = $this->getDoctrine()->getManager();
            $em->persist($countryMapping);
            $em->flush();

            $translator = $this->get('translator');

            $this->addFlash(
                'success',
                $new ?
                    $translator->trans('country_mapping.new.created', [], 'LoevgaardPakkelabelsBundle') :
                    $translator->trans('country_mapping.edit.updated', [], 'LoevgaardPakkelabelsBundle')
            );

            return $this->redirectToRoute('loevgaard_pakkelabels_country_mapping_edit', [
                'id' => $countryMapping->getId()
            ]);
        }

        return null;
    }

    /**
     * @param CountryMapping $countryMapping
     * @param Form $form
     * @return Response
     */
    private function updateResponse(CountryMapping $countryMapping, Form $form) : Response
    {
        return $this->render('@LoevgaardPakkelabels/country_mapping/edit.html.twig', [
            'countryMapping' => $countryMapping,
            'form' => $form
        ]);
    }

    /**
     * @param CountryMapping $countryMapping
     * @return Form
     */
    private function getForm(CountryMapping $countryMapping) : Form
    {
        return $form = $this->createForm(CountryMappingType::class, $countryMapping);
    }

}
