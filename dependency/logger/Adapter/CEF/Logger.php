<?php
/*
 * Copyright (C) 2015 Maarch
 *
 * This file is part of dependency logger.
 *
 * Dependency logger is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Dependency logger is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with dependency logger.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace dependency\logger\Adapter\CEF;

require_once __DIR__.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."log4php".DIRECTORY_SEPARATOR."Logger.php";

/**
 * Logger class
 */
class Logger implements \dependency\logger\LoggerInterface
{
    const LEVEL_UNKNOWN = 0;
    const LEVEL_LOW = 2;
    const LEVEL_MEDIUM = 5;
    const LEVEL_HIGH = 8;
    const LEVEL_VERY_HIGH = 10;

    private $cefVersion;
    private $deviceVendor;
    private $deviceProduct;
    private $deviceVersion;

    private $extensions = [];

    private $logger;

    /**
     * Constuctor of logger
     * @param string $cefVersion    The CEF version
     * @param string $deviceVendor  The device vendor
     * @param string $deviceProduct The device product
     * @param string $deviceVersion The device version
     * @param array  $loggers       The configuration of loggers
     * @param array  $appenders     The configuration of appenders
     * @param string $defaultLogger The default logger name
     */
    public function __construct($cefVersion, $deviceVendor, $deviceProduct, $deviceVersion, $loggers, $appenders, $defaultLogger)
    {
        $this->cefVersion = $cefVersion;
        $this->deviceVendor = $deviceVendor;
        $this->deviceProduct = $deviceProduct;
        $this->deviceVersion = $deviceVersion;
        $this->deviceVersion = $deviceVersion;

        $this->configure($loggers, $appenders);

        $this->logger = $this->getLogger($defaultLogger);
    }

    /**
     * Log a message
     * @param string $message   The log message
     * @param int    $level     The log level
     * @param string $eventType The log event type
     */
    public function log($message, $level, $eventType)
    {
        if (empty($message) || empty($eventType)) {
            throw  new \Exception("invalid parameters");
        }
        if (!in_array($level, array(self::LEVEL_UNKNOWN, self::LEVEL_LOW, self::LEVEL_MEDIUM, self::LEVEL_HIGH, self::LEVEL_VERY_HIGH))) {
            throw  new \Exception("invalid parameters");
        }

        $log = $this->getStaticHeader();
        $log .= "|".$eventType;
        $log .= "|".$message;
        $log .= "|".$level;

        if (count($this->getExtensions()) > 0) {
            $log .= "|".$this->getExtensions();
        }

        $log .= "\n";

        switch ($level) {
            case self::LEVEL_UNKNOWN:
                $this->logger->trace($log);
                break;
            case self::LEVEL_LOW:
                $this->logger->info($log);
                break;
            case self::LEVEL_MEDIUM:
                $this->logger->warn($log);
                break;
            case self::LEVEL_HIGH:
                $this->logger->error($log);
                break;
            case self::LEVEL_VERY_HIGH:
                $this->logger->fatal($log);
                break;
        }

        $this->extensions = [];
    }

    /**
     * Set the time format
     * @param array $loggers   The configuration of loggers
     * @param array $appenders The configuration of appenders
     */
    public function configure($loggers = array(), $appenders = array())
    {
        \Logger::configure(
            array(
                'loggers' => $loggers,
                'appenders' => $appenders,
            )
        );
    }

    /**
     * Get logger
     * @param string $name
     *
     * @return Logger The logger called or the default logger
     */
    public function getLogger($name = 'main')
    {
        return \Logger::getLogger($name);
    }


    /**
     * Get the static CEF header
     * @return string The CEF header
     */
    private function getStaticHeader()
    {
        $header = "";
        $header .= \laabs\date("M d H:i:s")." ".$_SERVER["REMOTE_PORT"]." CEF:".$this->cefVersion;
        $header .= "|".$this->deviceVendor;
        $header .= "|".$this->deviceProduct;
        $header .= "|".$this->deviceVersion;

        return $header;
    }

    /**
     * Get the extensions
     * @return string The extension
     */
    private function getExtensions()
    {
        $extension = "";
        foreach ($this->extensions as $key => $value) {
            $extension .= " ".$key."=".$value;
        }

        return $extension;
    }

    /**
     * Get the extensions
     * @return string The extension
     */
    public function setExtension($name, $value)
    {
        $this->extensions[$name] = $value;
    }

    /**
     * Get the extensions
     * @return string The extension
     */
    public function setCustomExtension($name, $value)
    {
        $this->extensions[$this->deviceVendor.$this->deviceProduct.$name] = $value;
    }
}
