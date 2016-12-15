<?php

namespace core\Kernel;

class StaticKernel
    extends AbstractKernel
{

    /* Methods */
    public static function run()
    {
        self::$instance->getResource();

        self::$instance->sendResponse();

    }

    /**
     * Get the resource from request uri
     *
     * @return void
     * @author 
     **/
    protected function getResource()
    {
        $publicRouter = new \core\Route\PublicRouter($this->request->uri);
        $resource = $publicRouter->resource;

        $this->response->setBody($resource->getContents());

        if ($this->response->mode == 'http') {
            $this->response->setContentType($resource->getMimetype());

            $this->response->setHeader("X-Laabs-Static-Resource", $resource->getPath());

            if (!$cacheControl = \laabs::getCacheControl()) {
                $cacheControl = "public, max-age=3600";
            }

            $this->response->setCacheControl($cacheControl);
        }

    }
}