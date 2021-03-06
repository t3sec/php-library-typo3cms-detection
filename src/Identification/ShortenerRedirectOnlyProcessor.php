<?php
namespace T3sec\Typo3Cms\Detection\Identification;

use T3sec\Typo3Cms\Detection\Context;
use T3sec\Typo3Cms\Detection\AbstractProcessor;
use T3sec\Typo3Cms\Detection\ProcessorInterface;
use T3sec\Url\UrlFetcher;


class ShortenerRedirectOnlyProcessor extends AbstractProcessor implements ProcessorInterface
{
    /**
     * @var array
     */
    protected $shortenerServices = array(
        'b-gat.es',
        'base24.eu',
        'bit.ly',
        'buff.ly',
        'csc0.ly',
        'eepurl.com',
        'fb.me',
        'dlvr.it',
        'goo.gl',
        'indu.st',
        'is.gd',
        'j.mp',
        'kck.st',
        'krz.ch',
        'lgsh.ch',
        'lnkr.ch',
        'moreti.me',
        'myurl.to',
        'npub.li',
        'nkirch.de',
        'nkor.de',
        'opnstre.am',
        'ow.ly',
        'rlmk.me',
        'shar.es',
        't3n.me',
        'tinyurl.com',
        'ur1.ca',
        'xing.com',
        'zite.to',
    );

    /**
     * Class constructor.
     *
     * @param ProcessorInterface|null $successor
     */
    public function __construct($successor = NULL)
    {
        if (!is_null($successor)) {
            $this->successor = $successor;
        }
    }

    /**
     * Processes context.
     *
     * @param Context $context
     * @return void
     */
    public function process(Context $context)
    {

        $objUrl = \Purl\Url::parse($context->getUrl());

        $urlHost = $objUrl->get('host');

        if (in_array($urlHost, $this->shortenerServices, TRUE)) {
            $objFetcher = new UrlFetcher();
            $objFetcher->setUrl($context->getUrl())->fetchUrl(UrlFetcher::HTTP_GET, FALSE, TRUE);

            if ($objFetcher->getErrno() === 0) {
                $context->setUrl($objFetcher->getUrl());
            }
        }

        if (!is_null($this->successor)) {
            $this->successor->process($context);
        }
    }
}