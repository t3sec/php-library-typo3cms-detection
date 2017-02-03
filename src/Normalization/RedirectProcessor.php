<?php
namespace T3sec\Typo3Cms\Detection\Normalization;

use T3sec\Typo3Cms\Detection\Context;
use T3sec\Typo3Cms\Detection\AbstractProcessor;
use T3sec\Typo3Cms\Detection\ProcessorInterface;
use T3sec\Url\UrlFetcher;


class RedirectProcessor extends AbstractProcessor implements ProcessorInterface
{
    /**
     * @var bool
     */
    protected $allowRedirect = TRUE;


    /**
     * Class constructor.
     *
     * @param ProcessorInterface|null $successor
     * @param bool $allowRedirect
     */
    public function __construct($successor = NULL, $allowRedirect = TRUE)
    {
        if (!is_null($successor)) {
            $this->successor = $successor;
        }

        if (!is_bool($allowRedirect)) {
            throw new InvalidArgumentException(
                sprintf('Invalid argument for constructor of %s',
                    get_class($this)
                ),
                1373924180
            );
        }

        $this->allowRedirect = $allowRedirect;
    }

    /**
     * Processes context.
     *
     * @param Context $context
     * @return void
     */
    public function process(Context $context)
    {

        $objOriginUrl = \Purl\Url::parse($context->getUrl());
        $urlOriginHost = $objOriginUrl->get('host');
        $urlOriginScheme = $objOriginUrl->get('scheme');

        $objFetcher = new UrlFetcher();
        $objFetcher->setUrl($context->getUrl())->fetchUrl(UrlFetcher::HTTP_HEAD, FALSE, $this->allowRedirect);

        if ($objFetcher->getErrno() === 0 && $objFetcher->getNumRedirects() > 0) {
            $objUrl = $objOriginUrl;

            $objProcessedUrl = \Purl\Url::parse($objFetcher->getUrl());
            $objProcessedHost = $objProcessedUrl->get('host');
            $objProcessedScheme = $objProcessedUrl->get('scheme');

            if (0 !== strcmp($urlOriginHost, $objProcessedHost)) {
                $objUrl->set('host', $objProcessedHost)->getUrl();
            }

            if (0 !== strcmp($urlOriginScheme, $objProcessedScheme)) {
                $objUrl->set('scheme', $objProcessedScheme);
            }

            $context->setUrl($objUrl->getUrl());

            unset($objProcessedScheme, $urlOriginScheme, $objProcessedHost, $objProcessedUrl);
        }
        unset($objFetcher, $urlOriginHost, $objOriginUrl);


        if (!is_null($this->successor)) {
            $this->successor->process($context);
        }
    }
}