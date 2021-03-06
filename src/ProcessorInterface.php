<?php
namespace T3sec\Typo3Cms\Detection;


interface ProcessorInterface
{
    /**
     * Processes context.
     *
     * @param  Context $context
     * @return  void
     */
    public function process(Context $context);

}