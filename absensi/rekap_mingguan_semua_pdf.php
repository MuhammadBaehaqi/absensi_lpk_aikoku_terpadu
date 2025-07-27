<?php
require_once '../dompdf-3.1.0/dompdf/autoload.inc.php';
use Dompdf\Dompdf;

include '../includes/config.php';

$tgl_awal = $_GET['tgl_awal'] ?? '';
$tgl_akhir = $_GET['tgl_akhir'] ?? '';

$html = "<h3 style='text-align:center;'>Laporan Absensi Mingguan Semua Siswa</h3>
<p><strong>Periode:</strong> " . date('d-m-Y', strtotime($tgl_awal)) . " s.d. " . date('d-m-Y', strtotime($tgl_akhir)) . "</p>
<table border='1' cellpadding='6' cellspacing='0' width='100%'>
<thead style='background-color:#f2f2f2;'>
<tr>
    <th>No</th>
    <th>Nama Siswa</th>
    <th>Hadir</th>
    <th>Izin</th>
    <th>Sakit</th>
    <th>Alpha</th>
</tr>
</thead>
<tbody>
";

$siswa = mysqli_query($koneksi, "SELECT * FROM tb_pengguna WHERE role='siswa'");
$no = 1;

while ($s = mysqli_fetch_assoc($siswa)) {
    $id = $s['id_pengguna'];
    $nama = $s['nama_lengkap'];

    $rekap = ['Hadir' => 0, 'Izin' => 0, 'Sakit' => 0, 'Alpha' => 0];
    $q = mysqli_query($koneksi, "
        SELECT keterangan, COUNT(*) AS total 
        FROM tb_absensi 
        WHERE id_pengguna='$id' 
        AND tanggal BETWEEN '$tgl_awal' AND '$tgl_akhir'
        GROUP BY keterangan
    ");
    while ($r = mysqli_fetch_assoc($q)) {
        $rekap[$r['keterangan']] = $r['total'];
    }

    $html .= "<tr>
        <td align='center'>{$no}</td>
        <td>{$nama}</td>
        <td align='center'>{$rekap['Hadir']}</td>
        <td align='center'>{$rekap['Izin']}</td>
        <td align='center'>{$rekap['Sakit']}</td>
        <td align='center'>{$rekap['Alpha']}</td>
    </tr>";
    $no++;
}

$html .= "</tbody></table>";

// Render PDF
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();
$dompdf->stream('Rekap_Mingguan_Semua_Siswa_' . $tgl_awal . '_sd_' . $tgl_akhir . '.pdf', ['Attachment' => false]);
exit;
