<?php
require('../fpdf/fpdf.php');
require_once('../includes/db.php');

class PDF extends FPDF {
    function Header() {
        $this->SetFont('Arial','B',12);
        $this->Cell(0,10,'Chama Members List',0,1,'C');
        $this->Ln(5);
    }

    function MemberTable($data) {
        $this->SetFont('Arial','B',10);
        $this->Cell(10,10,'ID',1);
        $this->Cell(40,10,'Full Name',1);
        $this->Cell(30,10,'Phone',1);
        $this->Cell(50,10,'Email',1);
        $this->Cell(20,10,'Role',1);
        $this->Cell(30,10,'Joined At',1);
        $this->Ln();

        $this->SetFont('Arial','',9);
        foreach ($data as $row) {
            $this->Cell(10,8,$row['id'],1);
            $this->Cell(40,8,$row['full_name'],1);
            $this->Cell(30,8,$row['phone_number'],1);
            $this->Cell(50,8,$row['email'],1);
            $this->Cell(20,8,$row['role'],1);
            $this->Cell(30,8,$row['joined_at'],1);
            $this->Ln();
        }
    }
}

$sql = "SELECT * FROM members";
$result = $conn->query($sql);
$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

$pdf = new PDF();
$pdf->AddPage();
$pdf->MemberTable($data);
$pdf->Output('D', 'members_list.pdf');
?>
