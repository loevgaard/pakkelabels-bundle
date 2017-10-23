<?php

namespace Loevgaard\PakkelabelsBundle\Controller;

use Doctrine\ORM\EntityManager;
use Loevgaard\PakkelabelsBundle\Entity\Label;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/label")
 */
class LabelController extends Controller
{
    /**
     * @Method("GET")
     * @Route("", name="loevgaard_pakkelabels_label_index")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $paginator = $this->get('knp_paginator');

        /** @var EntityManager $em */
        $em = $this->get('doctrine')->getManager();

        $qb = $em->createQueryBuilder();
        $qb->select('l')
            ->from('LoevgaardPakkelabelsBundle:Label', 'l')
            ->orderBy('l.id', 'desc')
        ;

        /** @var Label[] $labels */
        $labels = $paginator->paginate(
            $qb,
            $request->query->getInt('page', 1),
            100
        );

        return $this->render('@LoevgaardPakkelabels/label/index.html.twig', [
            'labels' => $labels,
        ]);
    }

    /**
     * @Method("GET")
     * @Route("/{id}/show", name="loevgaard_pakkelabels_label_show")
     *
     * @param Label $label
     *
     * @return Response
     */
    public function showAction(Label $label): Response
    {
        return $this->render('@LoevgaardPakkelabels/label/show.html.twig', [
            'label' => $label,
        ]);
    }

    /**
     * @Method("GET")
     * @Route("/{id}/image", name="loevgaard_pakkelabels_label_image")
     *
     * @param Label $label
     *
     * @return BinaryFileResponse
     */
    public function imageAction(Label $label): BinaryFileResponse
    {
        $labelFactory = $this->get('loevgaard_pakkelabels.label_file_factory');

        // @todo remember to catch exception, maybe create a new exception class
        $labelFile = $labelFactory->create($label, true);

        return new BinaryFileResponse($labelFile);
    }
}
