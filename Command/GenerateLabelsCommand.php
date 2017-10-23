<?php

namespace Loevgaard\DandomainAltapayBundle\Command;

use Doctrine\ORM\EntityManager;
use Loevgaard\PakkelabelsBundle\Entity\Label;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateLabelsCommand extends ContainerAwareCommand
{
    use LockableTrait;

    protected function configure()
    {
        $this->setName('loevgaard:pakkelabels:generate-labels')
            ->setDescription('Generates labels')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$this->lock($this->getName())) {
            $output->writeln('The command is already running in another process.');

            return 0;
        }

        /** @var EntityManager $em */
        $em = $this->getContainer()->get('doctrine')->getManager();

        $pakkelabels = $this->getContainer()->get('loevgaard_pakkelabels.client');

        /** @var Label[] $labels */
        $labels = $em->getRepository('LoevgaardPakkelabelsBundle:Label')->findBy([
            'status' => Label::STATUS_PENDING_CREATION
        ], null, 20);

        foreach ($labels as $label) {
            $res = $pakkelabels->doRequest('post', '/shipments', [
                'json' => $label->arrayForApi()
            ]);

            if(isset($res['error'])) {
                $label->markAsError($res['error']);
            } else {
                $label->markAsSuccess();
            }

            $em->flush();
        }
    }
}
