<?php
include ('auth.php');
require('fpdf/fpdf.php'); // Include FPDF library
include('config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];

    // Fetch payment details
    $stmt = $pdo->prepare("SELECT * FROM payments WHERE id = ?");
    $stmt->execute([$id]);
    $payment = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$payment) {
        die('Payment not found.');
    }

    // Create PDF
    $pdf = new FPDF();
    $pdf->AddPage();

    // Logo
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Image('img/logo-2.png', 10, 6, 30); // Add logo (adjust path and size)
    $pdf->Cell(0, 10, 'Payment Receipt', 0, 1, 'C');
    $pdf->Ln(10);

    // Payment Details in Table
    $pdf->SetFont('Arial', '', 12);

    // Table header
    $pdf->Cell(50, 10, 'Particulars', 1, 0, 'C');
    $pdf->Cell(0, 10, 'Details', 1, 1, 'C');

    // Payment ID
    $pdf->Cell(50, 10, 'Payment ID:', 1, 0);
    $pdf->Cell(0, 10, $payment['id'], 1, 1);

    // Customer Name
    $pdf->Cell(50, 10, 'Customer Name:', 1, 0);
    $pdf->Cell(0, 10, $payment['customer_name'], 1, 1);

    // Amount Paid
    $pdf->Cell(50, 10, 'Amount Paid:', 1, 0);
    $pdf->Cell(0, 10, 'Kes ' . number_format($payment['amount_paid'], 2), 1, 1);

    // Payment Date
    $pdf->Cell(50, 10, 'Payment Date:', 1, 0);
    $pdf->Cell(0, 10, $payment['payment_date'], 1, 1);

    // Description
    $pdf->Cell(50, 10, 'Description:', 1, 0);
    $pdf->MultiCell(0, 10, $payment['description'], 1);
    $pdf->Ln(10);

    // Footer
    $pdf->SetFont('Arial', 'I', 10);
    $pdf->SetY(-10);
    $pdf->Cell(0, 10, 'Consult Centre Limited', 0, 1, 'C');

    // Output the PDF
    $pdf->Output(); // Display in browser
}
?>
