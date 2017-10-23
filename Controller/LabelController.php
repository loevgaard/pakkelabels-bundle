<?php

namespace Loevgaard\DandomainAltapayBundle\Controller;

use Loevgaard\PakkelabelsBundle\Entity\Label;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

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
        /** @var Label[] $labels */
        $labels = $this->get('doctrine')
            ->getManager()
            ->getRepository('LoevgaardPakkelabelsBundle:Label')
            ->findAll();

        return $this->render('@LoevgaardPakkelabels/payment/index.html.twig', [
            'labels' => $labels,
        ]);
    }

    /**
     * @Method("GET")
     * @Route("/{paymentId}/show", name="loevgaard_pakkelabels_payment_show")
     *
     * @param int     $paymentId
     * @param Request $request
     *
     * @return Response
     */
    public function showAction(int $paymentId, Request $request)
    {
        $payment = $this->getPaymentFromId($paymentId);
        if(!$payment) {
            throw $this->createNotFoundException('Payment with id `'.$paymentId.'` not found');
        }

        return $this->render('@LoevgaardDandomainAltapay/payment/show.html.twig', [
            'payment' => $payment,
        ]);
    }

    /**
     * Payment flow
     * 1. The Dandomain payment API POSTs to this page with the terminal slug in the URL
     * 2. After validating all input, we create a payment request to the Altapay API
     * 3. Finally we redirect the user to the URL given by the Altapay API.
     *
     * @Method("POST")
     * @Route("/{terminal}", name="loevgaard_pakkelabels_payment_new")
     *
     * @LogHttpTransaction()
     *
     * @param $terminal
     * @param Request $request
     *
     * @return RedirectResponse
     *
     * @throws PaymentException
     */
    public function newAction($terminal, Request $request)
    {
        $terminalManager = $this->container->get('loevgaard_pakkelabels.terminal_manager');
        $paymentManager = $this->container->get('loevgaard_pakkelabels.payment_manager');

        // convert symfony request to PSR7 request
        $psr7Factory = new DiactorosFactory();
        $psrRequest = $psr7Factory->createRequest($request);

        $handler = new Handler(
            $psrRequest,
            $this->container->getParameter('loevgaard_pakkelabels.shared_key_1'),
            $this->container->getParameter('loevgaard_pakkelabels.shared_key_2')
        );

        $dandomainPaymentRequest = $handler->getPaymentRequest();

        $paymentEntity = $paymentManager->createPaymentFromDandomainPaymentRequest($dandomainPaymentRequest);
        $paymentManager->update($paymentEntity);

        $terminalEntity = $terminalManager->findTerminalBySlug($terminal, true);
        if (!$terminalEntity) {
            // @todo fix translation
            throw TerminalNotFoundException::create('Terminal `'.$terminal.'` does not exist', $request, $paymentEntity);
        }

        if (!$handler->checksumMatches()) {
            // @todo fix translation
            throw ChecksumMismatchException::create('Checksum mismatch. Try again', $request, $paymentEntity);
        }

        $paymentRequestPayload = new PaymentRequestPayload(
            $terminalEntity->getTitle(),
            $dandomainPaymentRequest->getOrderId(),
            $dandomainPaymentRequest->getTotalAmount(),
            $dandomainPaymentRequest->getCurrencySymbol()
        );

        foreach ($dandomainPaymentRequest->getPaymentLines() as $paymentLine) {
            $orderLinePayload = new OrderLinePayload(
                $paymentLine->getName(),
                $paymentLine->getProductNumber(),
                $paymentLine->getQuantity(),
                $paymentLine->getPrice()
            );
            $orderLinePayload->setTaxPercent($paymentLine->getVat());

            $paymentRequestPayload->addOrderLine($orderLinePayload);
        }

        $customerInfoPayload = new CustomerInfoPayload();
        $customerNames = explode(' ', $dandomainPaymentRequest->getCustomerName(), 2);
        $shippingNames = explode(' ', $dandomainPaymentRequest->getDeliveryName(), 2);
        $customerInfoPayload
            ->setBillingFirstName($customerNames[0] ?? '')
            ->setBillingLastName($customerNames[1] ?? '')
            ->setBillingAddress(
                $dandomainPaymentRequest->getCustomerAddress().
                ($dandomainPaymentRequest->getCustomerAddress2() ? "\r\n".$dandomainPaymentRequest->getCustomerAddress2() : '')
            )
            ->setBillingPostal($dandomainPaymentRequest->getCustomerZipCode())
            ->setBillingCity($dandomainPaymentRequest->getCustomerCity())
            ->setBillingCountry($dandomainPaymentRequest->getCustomerCountry())
            ->setShippingFirstName($shippingNames[0] ?? '')
            ->setShippingLastName($shippingNames[1] ?? '')
            ->setShippingAddress(
                $dandomainPaymentRequest->getDeliveryAddress().
                ($dandomainPaymentRequest->getDeliveryAddress2() ? "\r\n".$dandomainPaymentRequest->getDeliveryAddress2() : '')
            )
            ->setShippingPostal($dandomainPaymentRequest->getDeliveryZipCode())
            ->setShippingCity($dandomainPaymentRequest->getDeliveryCity())
            ->setShippingCountry($dandomainPaymentRequest->getDeliveryCountry())
        ;
        $paymentRequestPayload->setCustomerInfo($customerInfoPayload);

        $configPayload = new ConfigPayload();
        $configPayload
            ->setCallbackForm($this->generateUrl('loevgaard_pakkelabels_callback_form', [], UrlGeneratorInterface::ABSOLUTE_URL))
            ->setCallbackOk($this->generateUrl('loevgaard_pakkelabels_callback_ok', [], UrlGeneratorInterface::ABSOLUTE_URL))
            ->setCallbackFail($this->generateUrl('loevgaard_pakkelabels_callback_fail', [], UrlGeneratorInterface::ABSOLUTE_URL))
            ->setCallbackRedirect($this->generateUrl('loevgaard_pakkelabels_callback_redirect', [], UrlGeneratorInterface::ABSOLUTE_URL))
            ->setCallbackOpen($this->generateUrl('loevgaard_pakkelabels_callback_open', [], UrlGeneratorInterface::ABSOLUTE_URL))
            ->setCallbackNotification($this->generateUrl('loevgaard_pakkelabels_callback_notification', [], UrlGeneratorInterface::ABSOLUTE_URL))
        ;
        $paymentRequestPayload->setConfig($configPayload);

        $paymentRequestPayload
            ->setCookiePart($this->getParameter('loevgaard_pakkelabels.cookie_payment_id'), $paymentEntity->getId())
            ->setCookiePart($this->getParameter('loevgaard_pakkelabels.cookie_checksum_complete'), $handler->getChecksum2())
        ;

        $altapay = $this->container->get('loevgaard_pakkelabels.altapay_client');
        $response = $altapay->createPaymentRequest($paymentRequestPayload);

        if (!$response->isSuccessful()) {
            // @todo fix translation
            throw AltapayPaymentRequestException::create('An error occured during payment request. Try again. Message from gateway: '.$response->getErrorMessage(), $request, $paymentEntity);
        }

        return new RedirectResponse($response->getUrl());
    }
}
