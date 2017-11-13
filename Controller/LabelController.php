<?php

namespace Loevgaard\PakkelabelsBundle\Controller;

use Loevgaard\PakkelabelsBundle\Entity\Label;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
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
        $repos = $this->get('loevgaard_pakkelabels.label_repository');

        $filterForm = $this->createForm('AppBundle\Form\FilterLabelType');

        $qb = $repos->getQueryBuilder();

        if ($request->query->has($filterForm->getName())) {
            // manually bind values from the request
            $filterForm->submit($request->query->get($filterForm->getName()));

            // build the query from the given form object
            $this->get('lexik_form_filter.query_builder_updater')->addFilterConditions($filterForm, $qb);
        }

        /** @var Label[] $labels */
        $labels = $repos->findAllWithPaging($request->query->getInt('page', 1), 100, [], $qb);

        return $this->render('@LoevgaardPakkelabels/label/index.html.twig', [
            'labels' => $labels,
            'filterForm' => $filterForm
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
        $labelFile = $labelFactory->read($label, true);

        return new BinaryFileResponse($labelFile);
    }

    /**
     * @Method("GET")
     * @Route("/{id}/reset-status", name="loevgaard_pakkelabels_label_reset_status")
     *
     * @param Label $label
     *
     * @return RedirectResponse
     */
    public function resetStatusAction(Label $label): RedirectResponse
    {
        $labelRepository = $this->get('loevgaard_pakkelabels.label_repository');
        $label->resetStatus();
        $labelRepository->save($label);

        return $this->redirectToRoute('loevgaard_pakkelabels_label_index');
    }
}
