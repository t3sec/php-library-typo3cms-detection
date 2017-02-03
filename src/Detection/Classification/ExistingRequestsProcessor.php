<?php
namespace Detection\Classification;

use Detection\Context;
use Detection\DomParser;
use Detection\AbstractProcessor;
use Detection\ProcessorInterface;


class ExistingRequestsProcessor extends AbstractProcessor implements ProcessorInterface
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

        $request = $context->getRequest();
        while (!is_null($request) && is_object($request)) {
            if (is_string($request->getBody())) {
                $objParser = new DomParser($request->getBody());
                $objParser->parse();
                $metaGenerator = $objParser->getMetaGenerator();
                if (!is_null($objParser->getMetaGenerator()) && is_string($objParser->getMetaGenerator()) && strpos($objParser->getMetaGenerator(), 'TYPO3') !== FALSE) {
                    $matches = array();
                    $isMatch = preg_match('/TYPO3 \d\.\d/', $metaGenerator, $matches);
                    if (is_int($isMatch) && $isMatch === 1 && is_array($matches) && count($matches) == 1) {
                        $context->setTypo3VersionString(array_shift($matches) . ' CMS');
                        $isClassificationSuccessful = TRUE;
                    }
                }
                unset($objParser);
            }

            $request = $context->getRequest();
        }
        unset($request);

        if (!is_null($this->successor) && !$isClassificationSuccessful) {
            $this->successor->process($context);
        }
    }
}