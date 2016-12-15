<?php

namespace core\Response;

class CliResponse
    extends AbstractResponse
{
    /* -------------------------------------------------------------------------
    - Properties
    ------------------------------------------------------------------------- */
    protected $foregroundColors;
    protected $backgroundColors;

    /* -------------------------------------------------------------------------
    - Methods
    ------------------------------------------------------------------------- */
    public function __construct()
    {
        $this->mode = 'cli';

        $foregroundColors                    = array();
        $foregroundColors['black']           = '0;30';
        $foregroundColors['dark_gray']       = '1;30';
        $foregroundColors['red']             = '0;31';
        $foregroundColors['light_red']       = '1;31';
        $foregroundColors['green']           = '0;32';
        $foregroundColors['light_green']     = '1;32';
        $foregroundColors['brown']           = '0;33';
        $foregroundColors['yellow']          = '1;33';
        $foregroundColors['blue']            = '0;34';
        $foregroundColors['light_blue']      = '0;34';
        $foregroundColors['purple']          = '0;35';
        $foregroundColors['light_purple']    = '1;35';
        $foregroundColors['cyan']            = '0;36';
        $foregroundColors['light_cyan']      = '1;36';
        $foregroundColors['light_gray']      = '1;37';
        $foregroundColors['white']           = '1;37';
        $this->foregroundColors              = $foregroundColors;

        $backgroundColors                = array();
        $backgroundColors['black']       = '40';
        $backgroundColors['red']         = '41';
        $backgroundColors['green']       = '42';
        $backgroundColors['yellow']      = '43';
        $backgroundColors['blue']        = '44';
        $backgroundColors['magenta']     = '45';
        $backgroundColors['cyan']        = '46';
        $backgroundColors['light_gray']  = '47';
        $this->backgroundColors          = $backgroundColors;

        $this->contentType = "text";
    }

    public function setBody($body, $foreground=false, $background=false)
    {
        $this->body = $this->getColoredString($body, $foreground, $background);
    }

    protected function getColoredString($string, $foreground, $background)
    {
        if ((!$foreground && !$background) || (!isset($this->foregroundColors[$foreground]) && !isset($this->backgroundColors[$background]))) {
            return $string;
        }

        $coloredString = '';

        if ($foreground && isset($this->foregroundColors[$foreground])) {
            $coloredString .= "\033[" . $this->foregroundColors[$foreground] . "m";
        }

        if ($foreground && isset($this->backgroundColors[$background])) {
            $coloredString .= "\033[" . $this->backgroundColors[$background] . "m";
        }

        $coloredString .=  $string . "\033[0m";

        return $coloredString;
    }
}