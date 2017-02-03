<?php
namespace T3sec\Typo3Cms\Detection\Classification;

use T3sec\Typo3Cms\Detection\Context;
use T3sec\Typo3Cms\Detection\DomParser;
use T3sec\Typo3Cms\Detection\AbstractProcessor;
use T3sec\Typo3Cms\Detection\ProcessorInterface;
use T3sec\Url\UrlFetcher;


class HostOnlyProcessor extends AbstractProcessor implements ProcessorInterface
{
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
     * @return  void
     */
    public function process(Context $context)
    {
        $isClassificationSuccessful = FALSE;

        $objFetcher = new UrlFetcher();
        $objUrl = \Purl\Url::parse($context->getUrl());

        $urlHostOnly = $objUrl->get('scheme') . '://' . $objUrl->get('host');
        $objFetcher->setUrl($urlHostOnly)->fetchUrl(UrlFetcher::HTTP_GET, FALSE, TRUE);


        if ($objFetcher->getErrno() === 0) {
            $responseBody = $objFetcher->getBody();

            if (is_string($responseBody) && strlen($responseBody)) {
                $objParser = new DomParser($responseBody);
                $objParser->parse();

                $metaGenerator = $objParser->getMetaGenerator();
                if (!is_null($metaGenerator) && is_string($metaGenerator) && strpos($metaGenerator, 'TYPO3') !== FALSE) {
                    $matches = array();
                    $isMatch = preg_match('/TYPO3 \d\.\d/', $metaGenerator, $matches);
                    if (is_int($isMatch) && $isMatch === 1 && is_array($matches) && count($matches) == 1) {
                        $context->setTypo3VersionString(array_shift($matches) . ' CMS');
                        $isClassificationSuccessful = TRUE;
                    }
                }
                unset($metaGenerator, $objParser);
            }
        }
        unset($objFetcher, $objUrl);

        if (!is_null($this->successor) && !$isClassificationSuccessful) {
            $this->successor->process($context);
        }
    }
}