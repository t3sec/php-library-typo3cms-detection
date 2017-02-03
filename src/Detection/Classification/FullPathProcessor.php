<?php
namespace Detection\Classification;

use Detection\Context;
use Detection\DomParser;
use Detection\AbstractProcessor;
use Detection\ProcessorInterface;
use T3sec\Url\UrlFetcher;


class FullPathProcessor extends AbstractProcessor implements ProcessorInterface
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

        $urlFullPath = $objUrl->get('scheme') . '://' . $objUrl->get('host');
        $path = $objUrl->get('path')->getPath();
        $urlFullPath .= (is_string($path) && strlen($path) > 0 && 0 !== strcmp('/', $path) ? $path : '');
        $objFetcher->setUrl($urlFullPath)->fetchUrl(UrlFetcher::HTTP_GET, FALSE, TRUE);

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