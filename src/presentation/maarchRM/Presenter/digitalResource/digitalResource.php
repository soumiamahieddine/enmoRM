<?php
/*
 * Copyright (C) 2015 Maarch
 *
 * This file is part of bundle digitalResource.
 *
 * Bundle digitalResource is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Bundle digitalResource is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with bundle digitalResource.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace Presentation\maarchRM\Presenter\digitalResource;

/**
 * digitalResource html serializer
 *
 * @package digitalResource
 * @author  Maarch Cyril Vazquez <cyril.vazquez@maarch.org>
 */
class digitalResource
{
    use \presentation\maarchRM\Presenter\exceptions\exceptionTrait;
    public $view;

    /**
     * Constuctor of registered mail html serializer
     * @param \dependency\html\Document $view The view
     */
    public function __construct(\dependency\html\Document $view)
    {
        $this->view = $view;
    }

    /**
     * Display the resource with info
     */
    public function retrieve($resource)
    {
        $contents = base64_decode($resource->attachment->data);

        if (strlen($contents) > 65536) {
            try {
                switch ($resource->mimetype) {
                    case 'application/pdf':
                        $fp = fopen('php://temp', 'r+');
                        fwrite($fp, $contents);

                        $fpdi = \laabs::newService('dependency/PDF/Factory')->getFpdi();
                        
                        $docpages = $fpdi->setSourceFile($fp);
                        if ($docpages > 2) {
                            $page = $fpdi->importPage(1);
                            $size = $fpdi->getTemplateSize($page);
                            $fpdi->AddPage($size['orientation'], [round($size['width']), round($size['height'])]);
                            $fpdi->useTemplate($page);

                            $page = $fpdi->importPage(2);
                            $size = $fpdi->getTemplateSize($page);
                            $fpdi->AddPage($size['orientation'], [round($size['width']), round($size['height'])]);
                            $fpdi->useTemplate($page);

                            $contents = $fpdi->Output('S');
                        }
                        break;

                    case 'text/html' :
                    case 'text/plain':
                        $contents = substr($contents, 0, 65536);
                        break;
                }
            } catch (\Exception $exception) {
                \laabs::setResponseCode('500');
            }
        } else {
            switch ($resource->mimetype) {
                case 'text/html' :
                case 'text/plain':
                        $contents = strip_tags($contents);
                        break;
            }

        }

        $url = \laabs::createPublicResource($contents);

        $this->view->addContent(
            '<object class="embed-responsive-item" data="'.$url.'"" type="'.$resource->mimetype.'"></object>'
        );

        return $this->view->saveHtml();
    }

    /**
     * Get resource contents
     * @param string $contents The digitalResource contents
     * 
     * @return string
     **/
    public function contents($contents)
    {
        $url = \laabs::createPublicResource($contents);

        $this->view->addContent(
            '<iframe class="" style="height:100%;width:100%" src="'.$url.'"" ></iframe>'
        );

        return $this->view->saveHtml();
    }

    /**
     * Get resource info
     * @param digitalResource/digitalResource $resource The digitalResourceObject
     * 
     * @return string
     **/
    public function info($resource)
    {
        $this->view->addContentFile("digitalResource/digitalResource/info.html");
        $this->view->setSource("resource", $resource);
        $this->view->merge();

        return $this->view->saveHtml();
    }


}

