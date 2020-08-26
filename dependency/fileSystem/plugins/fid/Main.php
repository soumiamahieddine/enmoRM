<?php
/*
 * Copyright (C) 2015 Maarch
 *
 * This file is part of FDI.
 *
 * FDI is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * FDI is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with FDI. If not, see <http://www.gnu.org/licenses/>.
 */
require_once __DIR__ . DIRECTORY_SEPARATOR . 'AbstractSequence.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'AbstractFragment.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'ByteSequence.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'ContainerSignature.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'ContainerSignatureFile.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'ContainerSignatureFile.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'PronomFormat.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'InternalSignature.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'LeftFragment.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'RightFragment.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'SubSequence.php';

/**
 * The PRONOM Droid internal signature identification tool
 *
 * @author Cyril Vazquez Maarch <cyril.vazquez@maarch.org>
 */
class Droid
{
    /**
     * The current signature directory
     * @var string
     */
    protected $signatureDir;

    /**
     * The current cache directory
     * @var string
     */
    protected $cacheDir;

    /**
     * The current signature file name
     * @var string
     */
    protected $signatureFile;

    /**
     * The current signature file version
     * @var string
     */
    protected $signatureFileVersion;

    /**
     * The current signature file date
     * @var string
     */
    protected $signatureFileDate;

    /**
     * The internal signature objects
     * @var array
     */
    public $internalSignatures = array();

    /**
     * The format objects by id
     * @var array
     */
    protected $formats = array();

    /**
     * The format objects by puid
     * @var array
     */
    protected $puids = array();

    /**
     * The format objects by mimetype
     * @var array
     */
    public $mimetypes = array();

    /**
     * The format objects by extension
     * @var array
     */
    public $extensions = array();

    /**
     * The current container signature file name
     * @var string
     */
    protected $containerSignatureFile;

    /**
     * The current container signature file version
     * @var string
     */
    protected $containerSignatureFileVersion;

    /**
     * The container trigger format puids
     * @var array
     */
    protected $triggerPuids;

    /**
     * The container file format mappings
     * @var array
     */
    protected $fileFormatMappings;

    /**
     * The container types
     * @var array
     */
    protected $containerTypes;

    /**
     * The container signatures
     * @var array
     */
    protected $containerSignatures;

    /**
     * The zip container extraction command
     * @var string
     */
    protected $zipExtractCmd;

    /**
     * Constructor
     * @param string $signatureFile          The DROID PRONOM signature file path
     * @param string $containerSignatureFile The DROID PRONOM container signature file path
     * @param string $zipExtractCmd          The mask for zip extraction
     */
    public function __construct($signatureFile=false, $containerSignatureFile=false, $zipExtractCmd=false)
    {
        // Create cache directory if needed
        $this->signatureDir = __DIR__ . DIRECTORY_SEPARATOR . 'signatureFiles';

        $this->cacheDir = __DIR__ . DIRECTORY_SEPARATOR . 'cache';
        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0755);
        }

        // Load the signatures
        $this->loadSignatures($signatureFile);

        // Load the container signatures
        $this->loadContainerSignatures($containerSignatureFile);

        // The command to extract zip container
        /*if (!$zipExtractCmd) {
            switch (DIRECTORY_SEPARATOR) {
                // Windows installation
                case '\\':
                    $zipExtractCmd = '"C:\Program Files (x86)\7-Zip\7z.exe" x "%s" -o"%s"';
                    break;

                case "/":
                default:
                    $zipExtractCmd = "7z x %s -o%s";
            }
        }
        $this->zipExtractCmd = $zipExtractCmd;*/
    }

    /**
     * Get the formats
     * @return array
     */
    public function getFormats()
    {
        return $this->formats;
    }

    /**
     * Get the format by Puid
     * @param string $puid
     *
     * @return object
     */
    public function getFormatByPuid($puid)
    {
        if (isset($this->puids[$puid])) {
            return $this->formats[$this->puids[$puid]];
        }
    }

    /**
     * Get the signature file version
     * @return string
     */
    public function getSignatureFileVersion()
    {
        return $this->signatureFileVersion;
    }

    /**
     * Get the signature file date
     * @return string
     */
    public function getSignatureFileDate()
    {
        return $this->signatureFileDate;
    }

    /**
     * Get the container signature file version
     * @return string
     */
    public function getContainerSignatureFileVersion()
    {
        return $this->containerSignatureFileVersion;
    }

    /**
     * Get the container signature file version
     * @return string
     */
    public function getContainerSignatureFileDate()
    {
        return $this->containerSignatureFileDate;
    }

    /**
     *  Try to match a droid format with the filename
     * @param string $filename The filename of the resource
     *
     * @return object The format
     */
    public function match($filename)
    {
        $matchedFormats = array();

        $finfo = new finfo();
        $mimetype = $finfo->file($filename, FILEINFO_MIME_TYPE);
        if ($mimetype) {
            $matchedFormats = $this->matchMimetype($mimetype);
        }

        // Internal signatures
        $matchedFormats = $this->matchInternalSignature($filename, $matchedFormats);

        // No format detected
        if (count($matchedFormats) == 0) {
            switch ($mimetype) {
                case 'text/plain':
                    return $this->getFormatByPuid('x-fmt/111');

                case 'application/octet-stream':
                    return $this->getFormatByPuid('fmt/208');
            }
        }

        // Apply priorities if more than 1 format found
        if (count($matchedFormats) > 1) {
            // Apply priorities to keep only most restricted formats (PDF/A is a restriction on PDF 1.4)
            $matchedFormats = $this->applyPriorities($matchedFormats);
        }

        return reset($matchedFormats);
    }

    /**
     * Match a file extension
     * @param string $extension
     *
     * @return array The possible formats
     */
    public function matchExtension($extension)
    {
        $matchedFormats = array();

        if (isset($this->extensions[$extension])) {
            $formatIds = array_intersect(array_keys($this->formats), $this->extensions[$extension]);
            foreach ($formatIds as $formatId) {
                $matchedFormats[$formatId] = $this->formats[$formatId];
            }
        }

        return $matchedFormats;
    }

    /**
     * Match a content mimetype
     * @param string $mimetype
     *
     * @return array The possible formats
     */
    public function matchMimetype($mimetype)
    {
        $matchedFormats = array();

        if (isset($this->mimetypes[$mimetype])) {
            $formatIds = array_intersect(array_keys($this->formats), $this->mimetypes[$mimetype]);
            foreach ($formatIds as $formatId) {
                $matchedFormats[$formatId] = $this->formats[$formatId];
            }
        }

        return $matchedFormats;
    }

    /**
     * Match a content against internal signatures
     * @param string $filename
     * @param array  $formats
     *
     * @return array The possible formats
     */
    public function matchInternalSignature($filename, $formats=array())
    {
        $matchedFormats = array();

        //if (empty($formats)) {
            $formats = $this->formats;
        //}
        //var_dump($formats);

        $contents = Droid::getContents($filename);

        // Loop on possible formats
        foreach ($formats as $formatId => $format) {
            // If format has no internal signature, continue to the next one
            if (count($format->internalSignatureIds) == 0) {
                continue;
            }

            // Loop on format internal signatures
            foreach ($format->internalSignatureIds as $internalSignatureId) {
                //if ($internalSignatureId != '750') continue;
                $internalSignature = $this->internalSignatures[(integer) $internalSignatureId];

                if ($internalSignature->match($contents)) {
                    // Add the format to the matched formats
                    $matchedFormats[$formatId] = $format;

                    // Skip other internal signatures of format
                    break;
                } else {
                    //unset($matchedFormats[$formatId]);
                }
            }
        }

        // Check if format is a container
        if (count($this->triggerPuids)) {
            foreach ($matchedFormats as $formatId => $format) {
                if ($containerType = array_search($format->puid, $this->triggerPuids)) {
                    $containerFormats = $this->matchContainer($filename, $containerType);

                    // Return container formats
                    if ($containerFormats) {
                        return $containerFormats;
                    }
                }
            }
        }

        // Return the list of matched format
        return $matchedFormats;
    }

    /**
     * Get the contents to match
     * @param string $filename
     *
     * @return string
     */
    public static function getContents($filename)
    {
        $pcreBackTrackLimit = ini_get('pcre.backtrack_limit');
        $filesize = filesize($filename);

        if ($filesize > $pcreBackTrackLimit) {
            $size = floor(($pcreBackTrackLimit / 2));
            $contents = file_get_contents($filename, false, null, 0, $size) . file_get_contents($filename, false, null, ($filesize-$size), $size);
        } else {
            $contents = file_get_contents($filename);
        }

        return $contents;
    }

    /**
     * Match filename as a container. Requires a uncompress command line
     * @param string $filename
     * @param string $containerType
     *
     * @return array The matched formats
     */
    protected function matchContainer($filename, $containerType)
    {
        $tmpdir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . uniqid() . mt_rand();

        $zip = \laabs::newService('dependency/fileSystem/plugins/zip');
        $zip->extract($filename, $tmpdir);

        /*$command = sprintf($this->zipExtractCmd, $filename, $tmpdir);
        $output = array();
        exec($command . " 2>&1", $output, $return);

        if ($return !== 0) {
            if (empty($output)) {
                $message = "Unknown error when uncompressing container file.";
            } else {
                $output[] = $command;
                $message = trim(implode(" ", $output));
            }
            throw new Exception($message, 0, new Exception($command));
        }*/

        $matchedFormats = array();
        $containerSignatures = $this->containerTypes[$containerType];
        foreach ($containerSignatures as $containerSignature) {
            if ($containerSignature->match($tmpdir)) {
                $puid = $this->fileFormatMappings[$containerSignature->id];

                $format = $this->getFormatByPuid($puid);
                $format->containerType = $containerType;

                $matchedFormats[$format->id] = $format;
            }
        }

        \laabs\rmdir($tmpdir, true);

        return $matchedFormats;
    }

    protected function applyPriorities($formats)
    {
        // Apply priorities to keep only most restricted formats (PDF/A is a restriction on PDF 1.4)
        foreach ($formats as $format) {
            if ($format->hasPriorityOverFormatIds) {
                foreach ($format->hasPriorityOverFormatIds as $hasPriorityOverFormatId) {
                    if (isset($formats[$hasPriorityOverFormatId])) {
                        unset($formats[$hasPriorityOverFormatId]);
                    }
                }
            }
        }

        return $formats;
    }

        /**
     * Load the signatures from signature file
     * @param string $signatureFile The DROID PRONOM signature file path
     */
    protected function loadSignatures($signatureFile=false)
    {
        // If no signature file path given, retrieve the last one in default signature files directory
        if (!$signatureFile) {
            $signatureFiles = glob($this->signatureDir . DIRECTORY_SEPARATOR . 'DROID_SignatureFile_V*.xml');

            rsort($signatureFiles);

            if (count($signatureFiles) == 0) {
                throw new Exception("No DROID signature file found in " . $this->signatureDir);
            }

            $signatureFile = $signatureFiles[0];
        }

        $this->signatureFile = $signatureFile;

        // Load signature xml
        $signatureDocument = new \DOMDocument();
        $signatureDocument->load($signatureFile);

        // Get the version
        $this->signatureFileVersion = $signatureDocument->documentElement->getAttribute('Version');
        $this->signatureFileDate = $signatureDocument->documentElement->getAttribute('DateCreated');


        // Get cache file name
        $signatureCacheFilename = $this->cacheDir . DIRECTORY_SEPARATOR . 'signature_' . $this->signatureFileVersion;

        // If the cache exists for the signature file version, reload from cache, else parse the new file and cache the result
        if (is_file($signatureCacheFilename)) {
            $this->loadSignatureCache($signatureCacheFilename);
        } else {
            $this->parseSignatures($signatureDocument, $signatureCacheFilename);
        }
    }

    /**
     * Load the signatures from signature file
     * @param string $containerSignatureFile The DROID PRONOM container signature file path
     */
    protected function loadContainerSignatures($containerSignatureFile=false)
    {
        // If no container  signature file path given, retrieve the last one in default signature files directory
        if (!$containerSignatureFile) {
            $containerSignatureFiles = glob($this->signatureDir . DIRECTORY_SEPARATOR . 'container-signature-*.xml');

            rsort($containerSignatureFiles);

            if (count($containerSignatureFiles) == 0) {
                return;
            }

            $containerSignatureFile = $containerSignatureFiles[0];
        }

        $this->containerSignatureFile = $containerSignatureFile;

        // Load signature xml
        $containerSignatureDocument = new DOMDocument();
        $containerSignatureDocument->load($containerSignatureFile);

        // Get the version
        $this->containerSignatureFileVersion = $containerSignatureDocument->documentElement->getAttribute('signatureVersion');
        $this->containerSignatureFileDate = substr($containerSignatureFile, -12, 4) . "-" . substr($containerSignatureFile, -8, 2) . "-" . substr($containerSignatureFile, -6, 2);

        // Get cache file name
        $containerSignatureCacheFilename = $this->cacheDir . DIRECTORY_SEPARATOR . 'container_' . $this->containerSignatureFileVersion;

        // If the cache exists for the signature file version, reload from cache, else parse the new file and cache the result
        if (is_file($containerSignatureCacheFilename)) {
            $this->loadContainerSignatureCache($containerSignatureCacheFilename);
        } else {
            $this->parseContainerSignatures($containerSignatureDocument, $containerSignatureCacheFilename);
        }
    }

    protected function loadSignatureCache($signatureCacheFilename)
    {
        $cachedObject = unserialize(file_get_contents($signatureCacheFilename));

        $this->internalSignatures = $cachedObject->internalSignatures;

        $this->formats = $cachedObject->formats;

        $this->puids = $cachedObject->puids;

        $this->mimetypes = $cachedObject->mimetypes;

        $this->extensions = $cachedObject->extensions;

    }

    protected function loadContainerSignatureCache($containerSignatureCacheFilename)
    {
        $cachedObject = unserialize(file_get_contents($containerSignatureCacheFilename));

        $this->triggerPuids = $cachedObject->triggerPuids;

        $this->fileFormatMappings = $cachedObject->fileFormatMappings;

        $this->containerTypes = $cachedObject->containerTypes;

        $this->containerSignatures = $cachedObject->containerSignatures;

    }

    protected function parseSignatures($signatureDocument, $signatureCacheFilename)
    {
        $internalSignatureElements = $signatureDocument->getElementsByTagName("InternalSignature");

        foreach ($internalSignatureElements as $internalSignatureElement) {
            $internalSignature = new InternalSignature($internalSignatureElement);

            $this->internalSignatures[(integer) $internalSignature->id] = $internalSignature;
        }

        $formatElements = $signatureDocument->getElementsByTagName("FileFormat");

        foreach ($formatElements as $formatElement) {
            $format = new PronomFormat($formatElement);

            $this->formats[$format->id] = $format;

            $this->puids[$format->puid] = $format->id;

            foreach ($format->mimetypes as $mimetype) {
                $this->mimetypes[$mimetype][] = $format->id;
            }

            foreach ($format->extensions as $extension) {
                $this->extensions[$extension][] = $format->id;
            }
        }

        $cachedObject = new \stdClass();

        $cachedObject->internalSignatures = $this->internalSignatures;

        $cachedObject->formats = $this->formats;

        $cachedObject->puids = $this->puids;

        $cachedObject->mimetypes = $this->mimetypes;

        $cachedObject->extensions = $this->extensions;

        file_put_contents($signatureCacheFilename, serialize($cachedObject));
    }

    protected function parseContainerSignatures($containerSignatureDocument, $containerSignatureCacheFilename)
    {
        $triggerPuidElements = $containerSignatureDocument->getElementsByTagName("TriggerPuid");

        foreach ($triggerPuidElements as $triggerPuidElement) {
            $this->triggerPuids[$triggerPuidElement->getAttribute("ContainerType")] = $triggerPuidElement->getAttribute("Puid");
        }

        $fileFormatMappingElements = $containerSignatureDocument->getElementsByTagName("FileFormatMapping");

        foreach ($fileFormatMappingElements as $fileFormatMappingElement) {
            $this->fileFormatMappings[$fileFormatMappingElement->getAttribute("signatureId")] = $fileFormatMappingElement->getAttribute("Puid");
        }

        $containerSignatureElements = $containerSignatureDocument->getElementsByTagName("ContainerSignature");

        foreach ($containerSignatureElements as $containerSignatureElement) {
            $containerSignature = new ContainerSignature($containerSignatureElement);

            $this->containerSignatures[$containerSignature->id] = $containerSignature;

            // Add to container types
            if (!isset($this->containerTypes[$containerSignature->containerType])) {
                $this->containerTypes[$containerSignature->containerType] = array();
            }

            $this->containerTypes[$containerSignature->containerType][$containerSignature->id] = $containerSignature;
        }

        $cachedObject = new \stdClass();

        $cachedObject->triggerPuids = $this->triggerPuids;

        $cachedObject->fileFormatMappings = $this->fileFormatMappings;

        $cachedObject->containerTypes = $this->containerTypes;

        $cachedObject->containerSignatures = $this->containerSignatures;

        file_put_contents($containerSignatureCacheFilename, serialize($cachedObject));
    }

}
