<?php

namespace core\Kernel;

interface KernelInterface
{

    /**
     * Start a new Kernel instance singleton
     * @param string $requestMode
     * @param string $requestType
     * @param string $responseType
     * @param string $responseLanguage
     * 
     * @throws Exception if a Kernel has already been started
     */
    public static function start($requestMode, $requestType, $responseType, $responseLanguage);

    /**
     * Get the Kernel instance singleton
     * @return object The Kernel object started
     * @throws Exception if no Kernel has been started yet
     */
    public static function get();

    /**
     * Run the kernel to process request, send headers if http mode and print resource
     */
    public static function run();

    /**
     * End the Kernel instance singleton to allow a new start (for batch process that will run several kernels)
     */
    public static function end();

}