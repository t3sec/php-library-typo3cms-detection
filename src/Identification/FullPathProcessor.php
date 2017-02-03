<?php
namespace T3sec\Typo3Cms\Detection\Identification;

use T3sec\Typo3Cms\Detection\Context;
use T3sec\Typo3Cms\Detection\Request;
use T3sec\Typo3Cms\Detection\DomParser;
use T3sec\Typo3Cms\Detection\AbstractProcessor;
use T3sec\Typo3Cms\Detection\ProcessorInterface;
use T3sec\Url\UrlFetcher;


class FullPathProcessor extends AbstractProcessor implements ProcessorInterface
{
    /**
     * @var bool
     */
    protected $allowRedirect = FALSE;


    /**
     * Class constructor.
     *
     * @param ProcessorInterface|null $successor
     * @param bool $allowRedirect
     */
    public function __construct($successor = NULL, $allowRedirect = FALSE)
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
        $isIdentificationSuccessful = FALSE;

        $objRequest = new Request();
        $objFetcher = new UrlFetcher();
        $objUrl = \Purl\Url::parse($context->getUrl());

        $urlFullPath = $objUrl->get('scheme') . '://' . $objUrl->get('host');
        $path = $objUrl->get('path')->getPath();
        $urlFullPath .= (is_string($path) && strlen($path) > 0 && 0 !== strcmp('/', $path) ? $path : '');

        $objFetcher->setUrl($urlFullPath)->fetchUrl(UrlFetcher::HTTP_GET, TRUE, $this->allowRedirect);
        $objRequest->setRequestUrl($urlFullPath)->setResponseUrl($urlFullPath);

        if ($objFetcher->getErrno() === 0) {
            $objRequest->setRequestUrl($urlFullPath)->setResponseUrl($urlFullPath);
            if ($objFetcher->getNumRedirects() >= 0) $objRequest->setResponseUrl($objFetcher->getUrl());

            if (is_null($context->getIp())) $context->setIp($objFetcher->getIpAddress());
            if (is_null($context->getPort())) $context->setPort($objFetcher->getPort());

            $objRequest->setResponseCode($objFetcher->getResponseHttpCode());
            $responseBody = $objFetcher->getBody();
            $objRequest->setBody($responseBody);
            $responseCookies = $objFetcher->getResponseCookies();
            $objRequest->setCookies($responseCookies);

            if (is_array($responseCookies)) {
                $typo3CookiesKeys = array('fe_typo_user', 'be_typo_user');
                $cookieKeys = array_keys($responseCookies);
                $isTypo3Cookies = array_intersect($typo3CookiesKeys, $cookieKeys);
                if (is_array($isTypo3Cookies) && count($isTypo3Cookies)) {
                    $context->setUrl($objRequest->getRequestUrl());
                    $context->setIsTypo3Cms(TRUE);
                    $isIdentificationSuccessful = TRUE;
                }
            }

            if (!$isIdentificationSuccessful && is_string($responseBody) && strlen($responseBody)) {
                $objParser = new DomParser($responseBody);
                $objParser->parse();

                $metaGenerator = $objParser->getMetaGenerator();
                if (!is_null($metaGenerator) && is_string($metaGenerator) && strpos($metaGenerator, 'TYPO3') !== FALSE) {
                    $context->setUrl($objRequest->getRequestUrl());
                    $context->setIsTypo3Cms(TRUE);
                    $isIdentificationSuccessful = TRUE;
                }
                unset($metaGenerator, $objParser);
            }

            $context->addRequest($objRequest);
        }
        unset($responseCookies, $responseBody, $urlHostOnly, $objUrl, $objFetcher, $objRequest);

        if (!is_null($this->successor) && !$isIdentificationSuccessful) {
            $this->successor->process($context);
        }
    }
}