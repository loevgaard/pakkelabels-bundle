<?php

namespace Loevgaard\PakkelabelsBundle\Command;

use Assert\Assert;
use Doctrine\Common\Persistence\ObjectManager;
use Loevgaard\PakkelabelsBundle\Entity\Label;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateLabelsCommand extends ContainerAwareCommand
{
    use LockableTrait;

    protected function configure()
    {
        $this->setName('loevgaard:pakkelabels:generate-labels')
            ->setDescription('Generates labels')
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

        /** @var ObjectManager $manager */
        $manager = $this->getContainer()->get('doctrine')->getManager();

        $pakkelabels = $this->getContainer()->get('loevgaard_pakkelabels.client');

        /** @var Label[] $labels */
        $labels = $manager->getRepository('LoevgaardPakkelabelsBundle:Label')->findBy([
            'status' => Label::STATUS_PENDING_CREATION
        ], null, $limit);

        if($output->isVerbose()) {
            $output->writeln('Label count: '.count($labels));
        }

        foreach ($labels as $label) {
            $output->writeln('Creating label id: '.$label->getId().' with order id: '.$label->getOrderId(), OutputInterface::VERBOSITY_VERBOSE);

            try {
                $res = $pakkelabels->doRequest('post', '/shipments', [
                    'json' => $label->arrayForApi()
                ]);

                if (isset($res['error'])) {
                    $label->markAsError($res['error']);
                } else {
                    $label->setExternalId($res['id']);

                    // download label
                    $labelRes = $pakkelabels->doRequest('get', '/shipments/' . $res['id'] . '/labels', [
                        'query' => [
                            'label_format' => 'png'
                        ]
                    ]);

                    if (isset($labelRes['error'])) {
                        $label->markAsError($labelRes['error']);
                    } else {
                        $labelFileFactory = $this->getContainer()->get('loevgaard_pakkelabels.label_file_factory');
                        $labelFile = $labelFileFactory->create($label);
                        $labelFile->fwrite(base64_decode($labelRes['base64']));
                        $label->markAsSuccess();

                        $output->writeln('Label was created', OutputInterface::VERBOSITY_VERBOSE);
                    }
                }
            } catch (\Exception $e) {
                $label->markAsError('An error occurred during creation of the label. The error was: '.$e->getMessage());
            }

            $manager->flush();
        }
    }
}
