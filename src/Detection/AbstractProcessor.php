<?php
namespace Detection;


abstract class AbstractProcessor
{
    /**
     * @var ProcessorInterface|null
     */
    protected $successor = NULL;


    /**
     * @param ProcessorInterface $successor
     */
    public function setSuccessor(ProcessorInterface $successor)
    {
        $this->successor = $successor;
    }

    /**
     * Processes context.
     *
     * @param Context $context
     * @return  void
     */
    public function process(Context $context)
    {
        if (!is_null($this->successor)) {
            $this->successor->process($context);
        }
    }
}