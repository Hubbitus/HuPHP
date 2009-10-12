<?php

define('FPDF_FONTPATH', 'font/');
include_once('ufpdf.php');

$pdf = new UFPDF();
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

////////////////////
$pagecount = $pdf->setSourceFile('blank.pdf');
$tplidx = $pdf->importPage(1, '/MediaBox');

$pdf->addPage('Portrait', array(210, 190));
$pdf->useTemplate($tplidx, 5, 5, 200);

$pdf->AddFont('DejaVuSansMono', '', 'font/DejaVuSansMono.php');

$pdf->SetFont('DejaVuSansMono', '', 12);
$pdf->Text(20, 20, iconv('UTF-8', 'CP1251', 'Test - Тест'));

$pdf->Output();


?>