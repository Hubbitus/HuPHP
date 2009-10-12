<?php

define('FPDF_FONTPATH', 'font/');
//include_once('ufpdf.php');
include_once('fpdi.php');

$pdf = new FPDI();
$pdf->Open();
$pdf->SetTitle("UFPDF is Cool.\nŨƑƤĐƒ ıš ČŏōĹ");
$pdf->SetAuthor('Steven Wittens');
$pdf->AddFont('DejaVuSansMono', '', 'DejaVuSansMono.php');
$pdf->AddPage();
$pdf->SetFont('DejaVuSansMono', '', 32);
$pdf->Write(12, "UFPDF is Cool.\n");
$pdf->Write(12, "ŨƑƤĐƒ");
$pdf->Write(12, "ıš ČŏōĹ.\n");

$pdf->Write(12, "Это тест\n");
$pdf->Close();
$pdf->Output('unicode.pdf', 'F');

?>