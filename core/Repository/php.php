<?php

namespace core\Repository;

class php
    implements RepositoryInterface
{

    /* constants */

    /* Properties */
    protected $path;

    protected $handler;

    /* Methods */
    public function open($path, $id)
    {
        $this->path = $path;
        $filename = $this->path . DIRECTORY_SEPARATOR . $id;

        $this->handler = fopen($filename, "c+");
        
        flock($this->handler, \LOCK_EX);
        
        return true;
    }

    public function close()
    {
        return fclose($this->handler);
    }

    public function read($id)
    {
        $size = filesize($this->path . DIRECTORY_SEPARATOR . $id);
        if ($size > 0) {
            return fread($this->handler, $size);
        }
    }

    public function write($id, $data)
    {
        return fwrite($this->handler, $data);
    }

    // called by Instance::regenerate_id($destroy=true) and Instance::destroy()
    public function destroy($id)
    {
        $filename = $this->path . DIRECTORY_SEPARATOR . $id;

        @unlink($filename);
    }

    public function gc($maxlifetime)
    {
        foreach ((array) scandir($this->path) as $tmpfile) {
            if (is_file($tmpfile)
                && substr($tmpfile, 0, 6) == 'laabs_'
                && ($id = substr($tmpfile, 6))
                && ((filemtime($tmpfile) + $maxlifetime) > microtime())
            ) {
                $this->destroy($id);
            }
        }
    }

    /**
     * Scan for instance with filter
     * @param string $path   The path to the repository to hanlde
     * @param string $filter A string to match on filename
     * 
     * @return array The array of instance ids
     */
    public function scan($path, $filter=false) 
    {
        $ids = array();
        foreach ((array) scandir($path) as $tmpfile) {
            if ($tmpfile == "." || $tmpfile == "..") {
                continue;
            }
            if (is_file($path . DIRECTORY_SEPARATOR . $tmpfile) && strpos($tmpfile, $filter) !== false) {
                $ids[] = $tmpfile;
            }
        }

        return $ids;
    }

}