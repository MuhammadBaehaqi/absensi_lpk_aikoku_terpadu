<?php
require '../vendor/autoload.php';
include '../includes/session.php';
include '../includes/config.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Buat objek spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Header kolom
$sheet->setCellValue('A1', 'No');
$sheet->setCellValue('B1', 'Nama Siswa');
$sheet->setCellValue('C1', 'Tanggal');
$sheet->setCellValue('D1', 'Jam Masuk');
$sheet->setCellValue('E1', 'Jam Pulang');
$sheet->setCellValue('F1', 'Keterangan');

// Ambil data absensi dari database
$query = mysqli_query($koneksi, "
    SELECT a.*, p.nama_lengkap 
    FROM tb_absensi a 
    JOIN tb_pengguna p ON a.id_pengguna = p.id_pengguna 
    ORDER BY a.tanggal DESC
");

$rowNum = 2;
$no = 1;

while ($row = mysqli_fetch_assoc($query)) {
    $sheet->setCellValue('A' . $rowNum, $no++);
    $sheet->setCellValue('B' . $rowNum, $row['nama_lengkap']);
    $sheet->setCellValue('C' . $rowNum, $row['tanggal']);
    $sheet->setCellValue('D' . $rowNum, $row['jam_masuk'] ?? '-');
    $sheet->setCellValue('E' . $rowNum, $row['jam_pulang'] ?? '-');
    $sheet->setCellValue('F' . $rowNum, $row['keterangan']);
    $rowNum++;
}

// Siapkan download
$filename = 'Laporan_Absensi_' . date('Ymd_His') . '.xlsx';
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment;filename=\"$filename\"");
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
