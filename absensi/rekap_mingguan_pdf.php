<?php
require_once '../dompdf-3.1.0/dompdf/autoload.inc.php';
use Dompdf\Dompdf;

include '../includes/session.php';
include '../includes/config.php';

$id_pengguna = $_GET['id_pengguna'] ?? '';
$tgl_awal = $_GET['tgl_awal'] ?? '';
$tgl_akhir = $_GET['tgl_akhir'] ?? '';

// Ambil nama siswa
$siswa = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT nama_lengkap FROM tb_pengguna WHERE id_pengguna='$id_pengguna'"));
$nama = $siswa['nama_lengkap'] ?? '-';

// Ambil data absensi mingguan
$data = mysqli_query($koneksi, "
    SELECT * FROM tb_absensi 
    WHERE id_pengguna = '$id_pengguna' 
    AND tanggal BETWEEN '$tgl_awal' AND '$tgl_akhir'
    ORDER BY tanggal ASC
");

$html = "
<h3 style='text-align:center;'>Rekap Absensi Mingguan</h3>
<p><strong>Nama:</strong> $nama<br>
<strong>Periode:</strong> " . date('d-m-Y', strtotime($tgl_awal)) . " s.d. " . date('d-m-Y', strtotime($tgl_akhir)) . "</p>
<table border='1' cellpadding='6' cellspacing='0' width='100%'>
<thead style='background-color:#f2f2f2;'>
<tr>
    <th>Tanggal</th>
    <th>Jam Masuk</th>
    <th>Jam Pulang</th>
    <th>Keterangan</th>
</tr>
</thead>
<tbody>
";

if (mysqli_num_rows($data) > 0) {
    while ($row = mysqli_fetch_assoc($data)) {
        $html .= '<tr>
            <td>' . date('d-m-Y', strtotime($row['tanggal'])) . '</td>
            <td>' . ($row['jam_masuk'] ?? '-') . '</td>
            <td>' . ($row['jam_pulang'] ?? '-') . '</td>
            <td>' . $row['keterangan'] . '</td>
        </tr>';
    }
} else {
    $html .= '<tr><td colspan="4" align="center">Tidak ada data absensi.</td></tr>';
}

$html .= '</tbody></table>';

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("Rekap_Absensi_Mingguan_" . $nama . ".pdf", ["Attachment" => false]);
exit;
