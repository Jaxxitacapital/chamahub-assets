<?php
require 'db_chamahub.php';
require 'vendor/autoload.php';

use Dompdf\Dompdf;

$docs = $conn->query("SELECT * FROM documents ORDER BY uploaded_at DESC");

$html = '<h2>📊 Documents Report</h2><table border="1" cellspacing="0" cellpadding="8">';
$html .= '<tr><th>Name</th><th>Uploader</th><th>Category</th><th>Date</th></tr>';
while ($doc = $docs->fetch_assoc()) {
    $html .= "<tr>
        <td>{$doc['name']}</td>
        <td>{$doc['uploaded_by']}</td>
        <td>{$doc['category']}</td>
        <td>{$doc['uploaded_at']}</td>
    </tr>";
}
$html .= '</table>';

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();
$dompdf->stream("documents_report.pdf");
