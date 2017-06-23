<?php

/*
 * Copyright (C) 2017 Maarch
 *
 * This file is part of bundle recordsManagement.
 *
 * Bundle recordsManagement is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Bundle recordsManagement is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with bundle recordsManagement.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace bundle\recordsManagement\Controller;

/**
 * Class to generate codes
 *
 * @author Alexis Ragot <alexis.ragot@maarch.org>
 */
class Code
{

    /**
     * Constructor of code generator controller
     */
    public function __construct()
    {
    }

    /**
     * Generate file with code
     *
     * @param string $data  The data of codes
     * @param string $label The label
     *
     * @return string The path of the file with codes
     */
    public function generateCodes($data, $label)
    {
        $label = strtr(utf8_decode($label), utf8_decode('àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ'), 'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY');
        
        require_once 'bundle/recordsManagement/fpdf/fpdf.php';
        $pdf = new \FPDF();
        $pdf->AddPage();

        $this->barcodeGenerator($data, $pdf);

        $qrCode = $this->qrCodeGenerator($data);
        $pdf->Image($qrCode, 80, 150);

        

        $pdf->SetXY(10, 30);
        $pdf->setFont('Arial', 'B', 16);
        $pdf->MultiCell(0, 10, "Profil d'archivage", 1, "C");
        
        $pdf->SetXY(10, 40);
        $pdf->setFont('Arial', '', 16);
        $pdf->MultiCell(0, 10, $label, 1, "C");

        return $pdf->Output("", "S"); 
    }

    /**
     * Generate a barcode
     *
     * @param string $data The data of the barcode
     */
    private function barcodeGenerator($data, $pdf)
    {
        require_once 'bundle/recordsManagement/Barcode/php-barcode.php';
        $barcode = new \Barcode;

        $x        = 105;
        $y        = 100;
        $height   = 20;   // barcode height in 1D ; module size in 2D
        $width    = 0.4;    // barcode height in 1D ; not use in 2D
        $angle    = 0;   // rotation in degrees
        $type     = 'code128';
        $black    = ''; // color in hexa
        
        $pdf->SetXY(10, 110);
        $pdf->setFont('Arial', 'B', 16);
        $pdf->MultiCell(0, 10, $data, 0, "C");

        $barcode->fpdf($pdf, $black, $x, $y, $angle, $type, $data, $width, $height);
    }

    /**
     * Generate a qrCode
     *
     * @param string $data The data of the qrCode
     */
    private function qrCodeGenerator($data)
    {
        include_once 'bundle/recordsManagement/phpqrcode/qrlib.php';

        $filname = \laabs\tempdir() . DIRECTORY_SEPARATOR . \laabs::newId() . ".png";
        \QRcode::png($data, $filname, QR_ECLEVEL_Q, 7);

        return $filname;
    }
}