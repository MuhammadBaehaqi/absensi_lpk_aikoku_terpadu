<?php
require_once '../dompdf-3.1.0/dompdf/autoload.inc.php';
use Dompdf\Dompdf;

include '../includes/session.php';
include '../includes/config.php';

$id_pengguna = $_GET['id_pengguna'] ?? '';
$bulan = $_GET['bulan'] ?? date('m');
$tahun = $_GET['tahun'] ?? date('Y');

$query_siswa = mysqli_query($koneksi, "SELECT * FROM tb_pengguna WHERE id_pengguna='$id_pengguna'");
$siswa = mysqli_fetch_assoc($query_siswa);

// Ambil rekap absensi
$query = mysqli_query($koneksi, "
    SELECT keterangan, COUNT(*) as total 
    FROM tb_absensi 
    WHERE id_pengguna='$id_pengguna' 
    AND MONTH(tanggal)='$bulan' AND YEAR(tanggal)='$tahun'
    GROUP BY keterangan
");

$data = [];
while ($row = mysqli_fetch_assoc($query)) {
    $data[$row['keterangan']] = $row['total'];
}

// Buat HTML
$html = '
<h3 style="text-align:center;">Laporan Rekap Absensi Bulanan</h3>
<table width="100%" cellpadding="5" cellspacing="0" border="0">
<tr><td><strong>Nama Siswa</strong></td><td>' . $siswa['nama_lengkap'] . '</td></tr>
<tr><td><strong>Bulan</strong></td><td>' . date('F', mktime(0, 0, 0, $bulan, 10)) . ' ' . $tahun . '</td></tr>
</table><br><br>

<table border="1" width="100%" cellpadding="8" cellspacing="0">
<thead style="background:#f2f2f2;">
<tr>
    <th>Hadir</th>
    <th>Izin</th>
    <th>Sakit</th>
    <th>Alpha</th>
</tr>
</thead>
<tbody>
<tr>
    <td align="center">' . ($data['Hadir'] ?? 0) . '</td>
    <td align="center">' . ($data['Izin'] ?? 0) . '</td>
    <td align="center">' . ($data['Sakit'] ?? 0) . '</td>
    <td align="center">' . ($data['Alpha'] ?? 0) . '</td>
</tr>
</tbody>
</table>
';

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream('Rekap_Absensi_' . $siswa['nama_lengkap'] . '_' . $bulan . '_' . $tahun . '.pdf', ['Attachment' => false]);
exit;
