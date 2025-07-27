<?php
require_once '../dompdf-3.1.0/dompdf/autoload.inc.php';
use Dompdf\Dompdf;

include '../includes/session.php';
include '../includes/config.php';

$bulan = $_GET['bulan'] ?? date('m');
$tahun = $_GET['tahun'] ?? date('Y');

$siswa = mysqli_query($koneksi, "SELECT * FROM tb_pengguna WHERE role='siswa'");

$html = '<h3 style="text-align:center;">Laporan Rekap Absensi Bulanan</h3>';
$html .= '<p><strong>Bulan:</strong> ' . date('F', mktime(0, 0, 0, $bulan, 10)) . ' ' . $tahun . '</p>';
$html .= '<table border="1" cellpadding="5" cellspacing="0" width="100%">
<thead style="background-color:#f2f2f2;">
    <tr>
        <th>No</th>
        <th>Nama Siswa</th>
        <th>Hadir</th>
        <th>Izin</th>
        <th>Sakit</th>
        <th>Alpha</th>
    </tr>
</thead>
<tbody>';

$no = 1;
while ($s = mysqli_fetch_assoc($siswa)) {
    $id = $s['id_pengguna'];
    $nama = $s['nama_lengkap'];

    $rekap = ['Hadir' => 0, 'Izin' => 0, 'Sakit' => 0, 'Alpha' => 0];
    $query = mysqli_query($koneksi, "
        SELECT keterangan, COUNT(*) AS total 
        FROM tb_absensi 
        WHERE id_pengguna='$id' AND MONTH(tanggal)='$bulan' AND YEAR(tanggal)='$tahun'
        GROUP BY keterangan
    ");
    while ($row = mysqli_fetch_assoc($query)) {
        $rekap[$row['keterangan']] = $row['total'];
    }

    $html .= '<tr>
        <td align="center">' . $no++ . '</td>
        <td>' . $nama . '</td>
        <td align="center">' . $rekap['Hadir'] . '</td>
        <td align="center">' . $rekap['Izin'] . '</td>
        <td align="center">' . $rekap['Sakit'] . '</td>
        <td align="center">' . $rekap['Alpha'] . '</td>
    </tr>';
}

$html .= '</tbody></table>';

// Generate PDF
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();
$dompdf->stream('Rekap_Semua_Siswa_' . $bulan . '_' . $tahun . '.pdf', ['Attachment' => false]);
exit;
