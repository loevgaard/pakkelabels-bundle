<?php

namespace Loevgaard\PakkelabelsBundle\File;

use Loevgaard\Pakkelabels\Client;
use Loevgaard\PakkelabelsBundle\Entity\Label;

class LabelFileFactory
{
    /**
     * @var string
     */
    protected $labelDir;

    /**
     * @var Client
     */
    protected $pakkelabelsClient;

    public function __construct(Client $client, string $labelDir)
    {
        $this->pakkelabelsClient = $client;
        $this->labelDir = $labelDir;
    }

    /**
     * @param Label $label
     *
     * @return LabelFile
     */
    public function create(Label $label): LabelFile
    {
        $file = new LabelFile($this->labelDir.'/'.$label->getId().'.png', 'w+');

        return $file;
    }

    /**
     * @todo This is a bit wrong. This should probably be a service of some sort instead of being called a 'factory'
     *
     * @param Label $label
     * @param bool  $verifyExistence
     *
     * @return LabelFile
     */
    public function read(Label $label, bool $verifyExistence = false): LabelFile
    {
        $labelFilePath = $this->labelDir.'/'.$label->getId().'.png';

        if ($verifyExistence && !file_exists($labelFilePath)) {
            $file = new LabelFile($labelFilePath, 'w+');

            $labelRes = $this->pakkelabelsClient->doRequest('get', '/shipments/'.$label->getExternalId().'/labels', [
                'query' => [
                    'label_format' => 'png',
                ],
            ]);

            if (isset($labelRes['error'])) {
                throw new \RuntimeException('The labels for label id '.$label->getId().' could not be downloaded from Pakkelabels. Error was: '.$labelRes['error']);
            }

            $file->fwrite(base64_decode($labelRes['base64']));
        } else {
            $file = new LabelFile($labelFilePath, 'r');
        }

        return $file;
    }
}
