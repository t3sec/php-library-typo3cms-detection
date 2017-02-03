<?php
namespace T3sec\Typo3Cms\Detection\Classification;

use T3sec\Typo3Cms\Detection\Context;
use T3sec\Typo3Cms\Detection\DomParser;
use T3sec\Typo3Cms\Detection\AbstractProcessor;
use T3sec\Typo3Cms\Detection\ProcessorInterface;
use T3sec\Url\UrlFetcher;


class Typo3ArtefactsProcessor extends AbstractProcessor implements ProcessorInterface
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
        $objFetcher->setUserAgent('Opera/99.0');
        $objUrl = \Purl\Url::parse($context->getUrl());

        $urlHostOnly = $objUrl->get('scheme') . '://' . $objUrl->get('host');
        $urlFullPath = $objUrl->get('scheme') . '://' . $objUrl->get('host');
        $path = $objUrl->path->getData();
        $path = array_reverse($path);
        $pathString = '';
        $i = 0;
        foreach ($path as $pathSegment) {
            if (!empty($pathSegment)) {
                if ($i === 0) {
                    if (!is_int(strpos($pathSegment, '.'))) {
                        $pathString = $pathString . '/' . $pathSegment;
                    }
                } else {
                    $pathString = $pathString . '/' . $pathSegment;
                }
            }
            $i++;
        }
        $urlFullPath .= $pathString;


        $fetcherErrnoHostOnly = $fetcherErrnoFullPath = 0;

        $objHostOnlyBackendUrl = new \Purl\Url($urlHostOnly);
        $objHostOnlyBackendUrl->path = 'typo3/index.php';
        $hostOnlyBackendUrl = $objHostOnlyBackendUrl->getUrl();
        $objFetcher->setUrl($hostOnlyBackendUrl)->fetchUrl(UrlFetcher::HTTP_GET, FALSE, FALSE);
        $fetcherErrnoHostOnly = $objFetcher->getErrno();
        $responseBodyHostOnly = $objFetcher->getBody();
        unset($objHostOnlyBackendUrl);


        $objFullPathBackendUrl = new \Purl\Url($urlFullPath);
        $objFullPathBackendUrl->path->add('typo3')->add('index.php');
        $fullPathBackendUrl = $objFullPathBackendUrl->getUrl();


        if (0 !== strcmp($hostOnlyBackendUrl, $fullPathBackendUrl)) {
            $objFetcher->setUrl($fullPathBackendUrl)->fetchUrl(UrlFetcher::HTTP_GET, FALSE, FALSE);
            $fetcherErrnoFullPath = $objFetcher->getErrno();
            $responseBodyFullPath = $objFetcher->getBody();
        } else {
            $fetcherErrnoFullPath = $fetcherErrnoHostOnly;
            $responseBodyFullPath = $responseBodyHostOnly;
        }
        unset($objFullPathBackendUrl);


        if ($fetcherErrnoFullPath === 0) {

            if (is_string($responseBodyHostOnly) && strlen($responseBodyHostOnly)) {
                $objParser = new DomParser($responseBodyHostOnly);
                $objParser->parse();

                $metaGenerator = $objParser->getMetaGenerator();
                if (!is_null($metaGenerator) && is_string($metaGenerator) && strpos($metaGenerator, 'TYPO3') !== FALSE) {
                    $matches = array();
                    $isMatch = preg_match('/TYPO3 \d\.\d/', $metaGenerator, $matches);
                    if (is_int($isMatch) && $isMatch === 1 && is_array($matches) && count($matches) == 1) {
                        $context->setTypo3VersionString(array_shift($matches) . ' CMS');
                        $isClassificationSuccessful = TRUE;
                    }
                } else {
                    if (is_string($responseBodyFullPath) && strlen($responseBodyFullPath)) {
                        $objParser->setContent($responseBodyFullPath);

                        $metaGenerator = $objParser->getMetaGenerator();
                        if (!is_null($metaGenerator) && is_string($metaGenerator) && strpos($metaGenerator, 'TYPO3') !== FALSE) {
                            $matches = array();
                            $isMatch = preg_match('/TYPO3 \d\.\d/', $metaGenerator, $matches);
                            if (is_int($isMatch) && $isMatch === 1 && is_array($matches) && count($matches) == 1) {
                                $context->setTypo3VersionString(array_shift($matches) . ' CMS');
                                $isClassificationSuccessful = TRUE;
                            }
                        }
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