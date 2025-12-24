<?php
require '../vendor/autoload.php';
require_once '../includes/db.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$sql = "SELECT id, full_name, phone_number, email, role, joined_at FROM members";
$result = $conn->query($sql);

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

$sheet->setCellValue('A1', 'ID')
      ->setCellValue('B1', 'Full Name')
      ->setCellValue('C1', 'Phone')
      ->setCellValue('D1', 'Email')
      ->setCellValue('E1', 'Role')
      ->setCellValue('F1', 'Joined At');

$rowNum = 2;
while ($row = $result->fetch_assoc()) {
    $sheet->setCellValue("A{$rowNum}", $row['id'])
          ->setCellValue("B{$rowNum}", $row['full_name'])
          ->setCellValue("C{$rowNum}", $row['phone_number'])
          ->setCellValue("D{$rowNum}", $row['email'])
          ->setCellValue("E{$rowNum}", $row['role'])
          ->setCellValue("F{$rowNum}", $row['joined_at']);
    $rowNum++;
}

$writer = new Xlsx($spreadsheet);
$filename = 'chama_members_export.xlsx';

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment; filename=\"$filename\"");
$writer->save("php://output");
exit;
?>
