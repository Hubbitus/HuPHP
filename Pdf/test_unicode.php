<?
/**
*
* @package Pdf
* @version 1.0
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan [at] Hubbitus [ dot. ] info>
* @copyright Copyright (c) 2009, Pahan-Hubbitus (Pavel Alexeev)
*
* @changelog
*	* 2009-10-12 16:59 ver 1.0
*	- Initial version.
**/

require_once('lib/fpdi/fpdf.php');
require_once('lib/fpdi/fpdi.php');

define('FPDF_FONTPATH', 'fonts/');

$pdf = new FPDI();

$pagecount = $pdf->setSourceFile('blank.pdf');
$tplidx = $pdf->importPage(1, '/MediaBox');

$pdf->addPage('Portrait', array(210, 190));
$pdf->useTemplate($tplidx, 5, 5, 200);

$pdf->AddFont('DejaVuSansMono', '', 'DejaVuSansMono.php');

$pdf->SetFont('DejaVuSansMono', '', 12);
$pdf->Text(20, 20, 'Test - Тест');

$pdf->Output();
?>