<?php
namespace T3sec\Typo3Cms\Detection\Classification;

use T3sec\Typo3Cms\Detection\Context;
use T3sec\Typo3Cms\Detection\AbstractProcessor;
use T3sec\Typo3Cms\Detection\ProcessorInterface;
use T3sec\Url\UrlFetcher;


class Typo3FingerprintProcessor extends AbstractProcessor implements ProcessorInterface
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

        $fingerprintData = array(
            0 => array(
                'TYPO3version' => 'TYPO3 8.4 CMS',
                'newFiles' => array(
                    'typo3/sysext/backend/Resources/Public/Icons/icon_fatalerror.gif',
                    'typo3/sysext/impexp/Resources/Public/Icons/status-reference-soft.png'
                )
            ),
            1 => array(
                'TYPO3version' => 'TYPO3 8.3 CMS',
                'newFiles' => array(
                    'typo3/sysext/form/Documentation/Images/FormCreationWizardShowTabs.png',
                    'typo3/sysext/workspaces/Documentation/Images/CustomWorkspaceGeneralTab.png'
                )
            ),
            2 => array(
                'TYPO3version' => 'TYPO3 8.2 CMS',
                'filesize' => array(
                    array('typo3/sysext/backend/Resources/Public/Fonts/FontAwesome/fontawesome-webfont.ttf' => 152796),
                )
            ),
            3 => array(
                'TYPO3version' => 'TYPO3 8.1 CMS',
                'newFiles' => array(
                    'typo3/sysext/backend/Resources/Public/Images/cropper-background.png',
                    'typo3/sysext/install/Resources/Public/Images/bg_transparent_emulation.png'
                )
            ),
            4 => array(
                'TYPO3version' => 'TYPO3 8.0 CMS',
                'newFiles' => array(
                    'typo3/sysext/t3skin/extjs/images/tree/system-tree-search-open.png',
                    'typo3/sysext/lang/Resources/Public/Images/cshimages/beuser_2.png',
                    'typo3/sysext/form/Resources/Public/Images/module-menu-down.png'
                )
            ),
            5 => array(
                'TYPO3version' => 'TYPO3 7.6 CMS',
                'newFiles' => array(
                    'typo3/sysext/backend/Resources/Public/Images/clear.gif',
                    'typo3/sysext/frontend/Resources/Public/Icons/FileIcons/default.gif',
                    'typo3/sysext/impexp/Resources/Public/Images/export.gif',
                    'typo3/sysext/rtehtmlarea/Resources/Public/Images/internal_link.gif',
                    'typo3/sysext/core/Resources/Public/Images/NotFound.png',
                    'typo3/sysext/backend/Resources/Public/Images/typo3-topbar@2x.png'
                )
            ),
            6 => array(
                'TYPO3version' => 'TYPO3 7.3 CMS',
                'newFiles' => array(
                    'typo3/sysext/t3skin/Resources/Public/Images/cropper-background.png',
                    'typo3/sysext/backend/Resources/Public/Images/BackendLayoutWizard/t3grid-layer-icon-close.png'
                )
            ),
            7 => array(
                'TYPO3version' => 'TYPO3 7.0 CMS',
                'newFiles' => array(
                    'typo3/contrib/jquery/jquery-1.11.1.js',
                    'typo3/sysext/beuser/Resources/Public/Images/legend.gif',
                    'typo3/contrib/swiftmailer/notes/rfc/rfc5751.txt'
                )
            ),
            8 => array(
                'TYPO3version' => 'TYPO3 6.2 CMS',
                'newFiles' => array(
                    'typo3/sysext/t3skin/Resources/Public/JavaScript/login.js',
                    'typo3/sysext/install/Resources/Public/Javascript/Install.js',
                )
            ),
            9 => array(
                'TYPO3version' => 'TYPO3 6.1 CMS',
                'newFiles' => array(
                    'typo3/contrib/requirejs/require.js',
                    'typo3/contrib/jquery/jquery-1.9.1.min.js',
                )
            ),
            10 => array(
                'TYPO3version' => 'TYPO3 6.0 CMS',
                'newFiles' => array(
                    'typo3/contrib/jquery/jquery-1.8.2.min.js',
                    'typo3/sysext/lang/Resources/Public/Contrib/jquery.dataTables-1.9.4.min.js',
                )
            ),
            11 => array(
                'TYPO3version' => 'TYPO3 4.7 CMS',
                'newFiles' => array(
                    'typo3/contrib/videojs/video-js/video.js',
                    'typo3/contrib/flowplayer/src/javascript/flowplayer.js/flowplayer-3.2.10.js',
                )
            ),
            12 => array(
                'TYPO3version' => 'TYPO3 4.6 CMS',
                'newFiles' => array(
                    'typo3/contrib/codemirror/contrib/scheme/js/parsescheme.js',
                    'typo3/sysext/css_styled_content/static/v4.5/setup.txt',
                )
            ),
            13 => array(
                'TYPO3version' => 'TYPO3 4.5 CMS',
                'newFiles' => array(
                    'typo3/js/livesearch.js',
                    'typo3/contrib/extjs/resources/css/visual/pivotgrid.css',
                    'typo3/contrib/modernizr/modernizr.min.js',
                    'typo3/contrib/extjs/locale/ext-lang-am.js',
                )
            ),
            14 => array(
                'TYPO3version' => 'TYPO3 4.4 CMS',
                'newFiles' => array(
                    'typo3/js/pagetreefiltermenu.js',
                    't3lib/js/extjs/ux/flashmessages.js',
                    'typo3/contrib/extjs/adapter/ext/ext-base-debug-w-comments.js',
                )
            ),
            15 => array(
                'TYPO3version' => 'TYPO3 4.3 CMS',
                'newFiles' => array(
                    't3lib/js/adminpanel.js',
                    'typo3/js/flashupload.js',
                    'typo3/contrib/extjs/ext-core.js',
                    'typo3/contrib/swfupload/swfupload.js',
                )
            ),
            16 => array(
                'TYPO3version' => 'TYPO3 4.2 CMS',
                'newFiles' => array(
                    'typo3/js/workspaces.js',
                    'typo3/contrib/scriptaculous/sound.js',
                    'typo3/templates/belog.html',
                )
            ),
            17 => array(
                'TYPO3version' => 'TYPO3 4.1 CMS',
                'newFiles' => array(
                    't3lib/jsfunc.inline.js',
                    'typo3/tree.js',
                    'typo3/contrib/prototype/prototype.js',
                    'typo3/contrib/scriptaculous/scriptaculous.js',
                )
            ),
            18 => array(
                'TYPO3version' => 'TYPO3 4.0 CMS',
                'newFiles' => array(
                    'typo3/tab.js',
                    'typo3/sysext/t3skin/ext_icon.gif',
                    'typo3/sysext/cms/tslib/media/fileicons/folder.gif',
                )
            ),
            19 => array(
                'TYPO3version' => 'TYPO3 3.8 CMS',
                'newFiles' => array(
                    't3lib/gfx/up.gif',
                    'typo3/mod/tools/em/download.png',
                    'typo3/sysext/install/imgs/copyrights.txt',
                )
            ),
            20 => array(
                'TYPO3version' => 'TYPO3 3.7 CMS',
                'newFiles' => array(
                    'misc/locallang_XML_dummy.xml',
                    't3lib/gfx/loginimage.jpg',
                    'typo3/mod/help/cshmanual/ext_icon.gif',
                )
            ),
            21 => array(
                'TYPO3version' => 'TYPO3 3.6 CMS',
                'newFiles' => array(
                    'misc/changes_in_typo3-ext.diff.txt',
                    't3lib/ext_php_api.dat',
                    'typo3/ext_php_api.dat',
                )
            ),
        );

        $TYPO3version = NULL;

        foreach ($fingerprintData as $data) {
            if (isset($data['newFiles'])) {
                foreach ($data['newFiles'] as $newFile) {
                    $objHostOnlyUrl = new \Purl\Url($urlHostOnly);
                    $objFullPathUrl = new \Purl\Url($urlFullPath);
                    $objHostOnlyUrl->path = $newFile;
                    $hostOnlyUrl = $objHostOnlyUrl->getUrl();


                    $pathSegments = explode('/', $newFile);
                    foreach ($pathSegments as $segment) {
                        $objFullPathUrl->path->add($segment);
                    }
                    $fullPathUrl = $objFullPathUrl->getUrl();

                    unset($objFullPathUrl, $objHostOnlyUrl);

                    $objFetcher->setUrl($hostOnlyUrl)->fetchUrl(UrlFetcher::HTTP_HEAD, FALSE, FALSE);
                    $fetcherErrnoHostOnly = $objFetcher->getErrno();
                    $responseHttpCode = $objFetcher->getResponseHttpCode();
                    if ($fetcherErrnoHostOnly === 0 && $responseHttpCode === 200) {
                        $isClassificationSuccessful = TRUE;
                        $TYPO3version = $data['TYPO3version'];
                        break 2;
                    }

                    if (0 !== strcmp($hostOnlyUrl, $fullPathUrl)) {
                        $objFetcher->setUrl($fullPathUrl)->fetchUrl(UrlFetcher::HTTP_HEAD, FALSE, FALSE);
                        $fetcherErrnoHostOnly = $objFetcher->getErrno();
                        $responseHttpCode = $objFetcher->getResponseHttpCode();
                        if ($fetcherErrnoHostOnly === 0 && $responseHttpCode === 200) {
                            $isClassificationSuccessful = TRUE;
                            $TYPO3version = $data['TYPO3version'];
                            break 2;
                        }
                    }
                }
                unset($newFile);
            }
            if (!$isClassificationSuccessful && isset($data['filesize'])) {
                foreach ($data['filesize'] as $newFile) {
                    reset($newFile);
                    $filesizePath = key($newFile);
                    $objHostOnlyUrl = new \Purl\Url($urlHostOnly);
                    $objFullPathUrl = new \Purl\Url($urlFullPath);
                    $objHostOnlyUrl->path = $filesizePath;
                    $hostOnlyUrl = $objHostOnlyUrl->getUrl();


                    $pathSegments = explode('/', $filesizePath);
                    foreach ($pathSegments as $segment) {
                        $objFullPathUrl->path->add($segment);
                    }
                    $fullPathUrl = $objFullPathUrl->getUrl();

                    unset($objFullPathUrl, $objHostOnlyUrl);

                    $objFetcher->setUrl($hostOnlyUrl)->fetchUrl(UrlFetcher::HTTP_HEAD, FALSE, FALSE);
                    $fetcherErrnoHostOnly = $objFetcher->getErrno();
                    $responseHttpCode = $objFetcher->getResponseHttpCode();
                    $contentLength = $objFetcher->getContentLength();
                    if ($fetcherErrnoHostOnly === 0 && $responseHttpCode === 200
                        && $contentLength > 0 && $contentLength === $newFile[$filesizePath]
                    ) {
                        $isClassificationSuccessful = TRUE;
                        $TYPO3version = $data['TYPO3version'];
                        break 2;
                    }

                    if (0 !== strcmp($hostOnlyUrl, $fullPathUrl)) {
                        $objFetcher->setUrl($fullPathUrl)->fetchUrl(UrlFetcher::HTTP_HEAD, FALSE, FALSE);
                        $fetcherErrnoHostOnly = $objFetcher->getErrno();
                        $responseHttpCode = $objFetcher->getResponseHttpCode();
                        if ($fetcherErrnoHostOnly === 0 && $responseHttpCode === 200
                            && $contentLength > 0 && $contentLength === $newFile[$filesizePath]
                        ) {
                            $isClassificationSuccessful = TRUE;
                            $TYPO3version = $data['TYPO3version'];
                            break 2;
                        }
                    }
                }
                unset($newFile);
            }
        }
        unset($fingerprintData, $urlFullPath, $pathString, $path, $urlFullPath, $urlHostOnly, $objUrl, $objFetcher);

        if ($isClassificationSuccessful) {
            $context->setTypo3VersionString($TYPO3version);
        }

        if (!is_null($this->successor) && !$isClassificationSuccessful) {
            $this->successor->process($context);
        }
    }
}