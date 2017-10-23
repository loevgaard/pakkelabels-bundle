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

    public function __construct(Client $client)
    {
        $this->pakkelabelsClient = $client;
        $this->labelDir = __DIR__.'/../Resources/labels';
    }

    /**
     * @param Label $label
     * @param bool $verifyExistence
     * @return LabelFile
     */
    public function create(Label $label, bool $verifyExistence = false)
    {
        $file = new LabelFile($this->labelDir.'/'.$label->getId().'.png', 'r+');

        if($verifyExistence && !$file->isFile()) {
            $labelRes = $this->pakkelabelsClient->doRequest('get', '/shipments/'.$label->getExternalId().'/labels', [
                'query' => [
                    'label_format' => 'png'
                ]
            ]);

            if(isset($labelRes['error'])) {
                throw new \RuntimeException('The labels for label id ' . $label->getId() . ' could not be downloaded from Pakkelabels');
            }

            $file->fwrite(base64_decode($labelRes['base64']));
        }

        return $file;
    }
}
