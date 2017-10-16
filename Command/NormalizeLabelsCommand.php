<?php

namespace Loevgaard\DandomainAltapayBundle\Command;

use Doctrine\ORM\EntityManager;
use League\ISO3166\ISO3166;
use Loevgaard\PakkelabelsBundle\Entity\Label;
use Loevgaard\PakkelabelsBundle\Entity\ShippingMethodMapping;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class NormalizeLabelsCommand extends ContainerAwareCommand
{
    use LockableTrait;

    protected function configure()
    {
        $this->setName('loevgaard:pakkelabels:normalize-labels')
            ->setDescription('Normalizes labels before creation')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$this->lock($this->getName())) {
            $output->writeln('The command is already running in another process.');

            return 0;
        }

        $em = $this->getEntityManager();

        $labels = $em->getRepository('LoevgaardPakkelabelsBundle:Label')->findBy([
            'status' => Label::STATUS_PENDING_NORMALIZATION
        ], null, 20);

        $iso3166 = new ISO3166();

        foreach ($labels as $label) {
            // check that countries are valid ISO 3166 alpha 2 codes
            $senderCountryValid = false;
            try {
                $iso3166->alpha2($label->getSenderCountryCode());
                $senderCountryValid = true;
            } catch (\Exception $e) {}

            if(!$senderCountryValid) {
                $senderCountry = $this->countryMapping($label->getSenderCountryCode());
                if($senderCountry) {
                    $label->setSenderCountryCode($senderCountry);
                } else {
                    $label->markAsError('Sender country code `'.$label->getSenderCountryCode().'` is not a valid ISO 3166 alpha 2 country code. Create a mapping or change the country manually.');
                    $em->flush();
                    continue;
                }
            }

            $receiverCountryValid = false;
            try {
                $iso3166->alpha2($label->getReceiverCountryCode());
                $receiverCountryValid = true;
            } catch (\Exception $e) {}

            if(!$receiverCountryValid) {
                $receiverCountry = $this->countryMapping($label->getReceiverCountryCode());
                if($receiverCountry) {
                    $label->setReceiverCountryCode($receiverCountry);
                } else {
                    $label->markAsError('Receiver country code `'.$label->getReceiverCountryCode().'` is not a valid ISO 3166 alpha 2 country code. Create a mapping or change the country manually.');
                    $em->flush();
                    continue;
                }
            }

            // check that the shipping method is mapped
            if($label->getShippingMethod()) {
                $shippingMethodMapping = $this->shippingMethodMapping($label->getShippingMethod());
                if($shippingMethodMapping) {
                    $label->setProductCode($shippingMethodMapping->getProductCode());
                    if(!empty($shippingMethodMapping->getServiceCodes())) {
                        $label->setServiceCodes(join(',', $shippingMethodMapping->getServiceCodes()));
                    }
                } else {
                    $label->markAsError('Shipping method `'.$label->getShippingMethod().'` is not mapped. Map it manually.');
                    $em->flush();
                    continue;
                }
            }

            $label->setStatus(Label::STATUS_PENDING_CREATION);

            $em->flush();
        }
    }

    /**
     * @param string $country
     * @return null|string
     */
    protected function countryMapping(string $country)
    {
        $em = $this->getEntityManager();
        $countryMapping = $em->getRepository('LoevgaardPakkelabelsBundle:CountryMapping')->findOneBy([
            'source' => $country
        ]);

        if($countryMapping && $countryMapping->getCountryCode()) {
            return $countryMapping->getCountryCode();
        }

        return null;
    }

    /**
     * @param string $shippingMethod
     * @return ShippingMethodMapping|null
     */
    protected function shippingMethodMapping(string $shippingMethod)
    {
        $em = $this->getEntityManager();
        $shippingMethodMapping = $em->getRepository('LoevgaardPakkelabelsBundle:ShippingMethodMapping')->findOneBy([
            'source' => $shippingMethod
        ]);

        if($shippingMethodMapping && $shippingMethodMapping->getProductCode()) {
            return $shippingMethodMapping;
        }

        return null;
    }

    /**
     * @return EntityManager
     */
    protected function getEntityManager() : EntityManager
    {
        /** @var EntityManager $em */
        $em = $this->getContainer()->get('doctrine')->getManager();

        return $em;
    }
}
