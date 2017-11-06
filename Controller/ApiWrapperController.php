<?php

namespace Loevgaard\PakkelabelsBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/api-wrapper")
 */
class ApiWrapperController extends Controller
{
    /**
     * @Method("GET")
     * @Route("", name="loevgaard_pakkelabels_api_wrapper")
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function indexAction(Request $request)
    {
        $uri = $request->query->get('uri');
        $params = $request->query->get('params') ?: [];
        $params = array_replace($params, ['per_page' => 200]);

        $client = $this->get('loevgaard_pakkelabels.client');
        $res = $client->doRequest('get', $uri, [
            'query' => $params,
        ]);

        return new JsonResponse($res);
    }
}
