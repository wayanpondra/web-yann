<?php
$pdf = new FPDF('l','mm','A5');
       // membuat halaman baru
       $pdf->AddPage();
       // setting jenis font yang akan digunakan
       $pdf->SetFont('Courier','B',16);
       // mencetak string 
       $pdf->Cell(190,7,'UNIVERSITAS MAJU BERSAMA',0,1,'C');//jarak dr kiri, jarak dari tulisan ke tulisan di bawahnya, border, tmbh 1 row/turun kebawah, alignment;
       $pdf->SetFont('Arial','B',12);
       $pdf->Cell(190,7,'DAFTAR MAHASISWA PROGRAM STUDI SISTEM INFORMASI',0,1,'C');
       // Memberikan space kebawah agar tidak terlalu rapat
       $pdf->Cell(10,7,'',0,1);
       $pdf->SetFont('Arial','B',10);
       $pdf->Cell(15,6,'KODE',1,0);
       $pdf->Cell(27,6,'NAMA ',1,0);
       $pdf->Cell(27,6,'JK',1,0);
       $pdf->Cell(25,6,'TGL',1,0);
       $pdf->Cell(85,6,'ALAMAT',1,1);
       $pdf->SetFont('Arial','',10);
       $Daftar = $this->db->get('Daftar')->result();
       foreach ($Daftar as $row){
           $pdf->Cell(15,6,$row->kode,1,0);
           $pdf->Cell(27,6,$row->nama,1,0);
           $pdf->Cell(27,6,$row->jk,1,0);
           $pdf->Cell(25,6,$row->tgl,1,0); 
           $pdf->Cell(85,6,$row->alamat,1,1); 
       }
       $pdf->Output();
?>