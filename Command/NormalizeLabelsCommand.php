<?php

namespace Loevgaard\PakkelabelsBundle\Command;

use Assert\Assert;
use Doctrine\Common\Persistence\ObjectManager;
use League\ISO3166\ISO3166;
use Loevgaard\PakkelabelsBundle\Entity\Label;
use Loevgaard\PakkelabelsBundle\Entity\ShippingMethodMapping;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class NormalizeLabelsCommand extends ContainerAwareCommand
{
    use LockableTrait;

    protected function configure()
    {
        $this->setName('loevgaard:pakkelabels:normalize-labels')
            ->setDescription('Normalizes labels before creation')
            ->addOption('limit', 'l', InputOption::VALUE_REQUIRED, 'The number of labels to normalize', 20)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$this->lock($this->getName())) {
            $output->writeln('The command is already running in another process.');

            return 0;
        }

        $limit = (int)$input->getOption('limit');
        Assert::that($limit)->integer()->greaterThan(0);

        $manager = $this->getManager();

        /** @var Label[] $labels */
        $labels = $manager->getRepository('LoevgaardPakkelabelsBundle:Label')->findBy([
            'status' => Label::STATUS_PENDING_NORMALIZATION
        ], null, $limit);

        if($output->isVerbose()) {
            $output->writeln('Label count: '.count($labels));
        }

        $iso3166 = new ISO3166();

        foreach ($labels as $label) {
            $output->writeln('Normalizing label id: '.$label->getId().' with order id: '.$label->getOrderId(), OutputInterface::VERBOSITY_VERBOSE);

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
                    $output->writeln('Sender country code `'.$label->getSenderCountryCode().'` is not a valid ISO 3166 alpha 2 country code. Create a mapping or change the country manually.', OutputInterface::VERBOSITY_VERBOSE);
                    $label->markAsError('Sender country code `'.$label->getSenderCountryCode().'` is not a valid ISO 3166 alpha 2 country code. Create a mapping or change the country manually.');
                    $manager->flush();
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
                    $output->writeln('Receiver country code `'.$label->getReceiverCountryCode().'` is not a valid ISO 3166 alpha 2 country code. Create a mapping or change the country manually.', OutputInterface::VERBOSITY_VERBOSE);
                    $label->markAsError('Receiver country code `'.$label->getReceiverCountryCode().'` is not a valid ISO 3166 alpha 2 country code. Create a mapping or change the country manually.');
                    $manager->flush();
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
                    $output->writeln('Shipping method `'.$label->getShippingMethod().'` is not mapped. Map it manually.', OutputInterface::VERBOSITY_VERBOSE);
                    $label->markAsError('Shipping method `'.$label->getShippingMethod().'` is not mapped. Map it manually.');
                    $manager->flush();
                    continue;
                }
            }

            $label->setStatus(Label::STATUS_PENDING_CREATION);

            $manager->flush();

            $output->writeln('Label was normalized', OutputInterface::VERBOSITY_VERBOSE);
        }
    }

    /**
     * @param string $country
     * @return null|string
     */
    protected function countryMapping(string $country)
    {
        $manager = $this->getManager();
        $countryMapping = $manager->getRepository('LoevgaardPakkelabelsBundle:CountryMapping')->findOneBy([
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
        $manager = $this->getManager();
        $shippingMethodMapping = $manager->getRepository('LoevgaardPakkelabelsBundle:ShippingMethodMapping')->findOneBy([
            'source' => $shippingMethod
        ]);

        if($shippingMethodMapping && $shippingMethodMapping->getProductCode()) {
            return $shippingMethodMapping;
        }

        return null;
    }

    /**
     * @return ObjectManager
     */
    protected function getManager()
    {
        $em = $this->getContainer()->get('doctrine')->getManager();

        return $em;
    }
}
