<?php
require_once '../dompdf-3.1.0/dompdf/autoload.inc.php';
use Dompdf\Dompdf;

include '../includes/session.php';
include '../includes/config.php';

// Ambil data absensi
$query = mysqli_query($koneksi, "
    SELECT a.*, p.nama_lengkap 
    FROM tb_absensi a 
    JOIN tb_pengguna p ON a.id_pengguna = p.id_pengguna 
    ORDER BY a.tanggal DESC
");

// Buat HTML isi tabel
$html = '
<h2 style="text-align:center;">Laporan Absensi Siswa</h2>
<table border="1" cellspacing="0" cellpadding="5" width="100%">
    <thead>
        <tr style="background-color: #f2f2f2;">
            <th>No</th>
            <th>Nama</th>
            <th>Tanggal</th>
            <th>Jam Masuk</th>
            <th>Jam Pulang</th>
            <th>Keterangan</th>
        </tr>
    </thead>
    <tbody>';

$no = 1;
while ($row = mysqli_fetch_assoc($query)) {
    $html .= '<tr>
        <td>' . $no++ . '</td>
        <td>' . $row['nama_lengkap'] . '</td>
        <td>' . date('d-m-Y', strtotime($row['tanggal'])) . '</td>
        <td>' . ($row['jam_masuk'] ?? '-') . '</td>
        <td>' . ($row['jam_pulang'] ?? '-') . '</td>
        <td>' . $row['keterangan'] . '</td>
    </tr>';
}

$html .= '</tbody></table>';

// Generate PDF
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream('Laporan_Absensi.pdf', ['Attachment' => false]);
