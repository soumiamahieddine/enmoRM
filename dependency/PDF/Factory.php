<?php 

namespace dependency\PDF;
/**
 * Factory for PDF third party tools
 */
class Factory
{
    
    /**
     * Constructor
     */
    public function __construct()
    {
        require_once(__DIR__.'/fpdf/fpdf.php');
        require_once(__DIR__.'/fpdi/src/autoload.php');
    }

    /**
     * Get Fdpi
     * @return fdpi
     */
    public function getFpdi()
    {
        return new \setasign\Fpdi\Fpdi();
    }

    /**
     * Get Fdpf
     * @return fdpf
     */
    public function getFpdf()
    {
        return new \FPDF();
    }
}