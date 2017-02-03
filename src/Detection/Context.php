<?php
namespace Detection;


class Context
{
    /**
     * Keeps URL to process.
     *
     * @var  string
     */
    protected $url;

    /**
     * Keeps IP address.
     *
     * @var  string
     */
    protected $ip;

    /**
     * Keeps port.
     *
     * @var  integer
     */
    protected $port;

    /**
     * @var Request[]
     */
    protected $request = array();

    /**
     * Keeps information whether TYPO3 CMS has been identified.
     *
     * @var bool
     */
    protected $isTypo3Cms = FALSE;

    /**
     * @param boolean $isTypo3Cms
     * @return  Context
     */
    public function setIsTypo3Cms($isTypo3Cms)
    {
        $this->isTypo3Cms = $isTypo3Cms;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getIsTypo3Cms()
    {
        return $this->isTypo3Cms;
    }

    /**
     * @param null|string $typo3VersionString
     * @return  Context
     */
    public function setTypo3VersionString($typo3VersionString)
    {
        $this->typo3VersionString = $typo3VersionString;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getTypo3VersionString()
    {
        return $this->typo3VersionString;
    }

    /**
     * Keeps Typo3 version string.
     *
     * @var null|string
     */
    protected $typo3VersionString = NULL;


    /**
     * @param mixed $ip
     * @return  Context
     */
    public function setIp($ip)
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * @param mixed $port
     * @return  Context
     */
    public function setPort($port)
    {
        $this->port = $port;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @param  Request $request
     * @return  Context
     */
    public function addRequest(Request $request)
    {
        $this->request[] = $request;

        return $this;
    }

    /**
     * @return Request|null
     */
    public function getRequest()
    {
        if (is_array($this->request) && count($this->request) > 0) {
            return array_pop($this->request);
        }
        return NULL;
    }

    /**
     * @param string $url
     * @return  Context
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }
}