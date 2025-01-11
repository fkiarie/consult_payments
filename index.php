<?php
include('header.php');

try {
    $totalpayments = $pdo->query("SELECT COUNT(*) FROM payments")->fetchColumn();
    $totalpaymentsin = $pdo->query("SELECT SUM(amount_paid) FROM payments")->fetchColumn();

    // Fetch detailed requests
    $stmt = $pdo->prepare("SELECT * FROM payments");
    $stmt->execute();
    $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error fetching data: " . $e->getMessage());
    echo "<p>There was an error fetching the data. Please try again later.</p>";
}
?>

<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-xl-6 col-md-6">
            <div class="card bg-primary text-black mb-4">
                <div class="card-header text-center">Total payments</div>
                <div class="card-body text-center text-white fs-3"><?= $totalpayments; ?></div>
            </div>
        </div>
        <div class="col-xl-6 col-md-6">
            <div class="card bg-success text-black mb-4">
                <div class="card-header text-center">Total Amount Paid</div>
                <div class="card-body text-center text-white fs-3"><?= $totalpaymentsin; ?></div>
            </div>
        </div>
    </div>
</div>
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Payments</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Customer Name</th>
                        <th>Amount Paid</th>
                        <th>Payment Date</th>
                        <th>Description</th>
                        <th>Created At</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th>ID</th>
                        <th>Customer Name</th>
                        <th>Amount Paid</th>
                        <th>Payment Date</th>
                        <th>Description</th>
                        <th>Created At</th>
                        <th>Action</th>
                    </tr>
                </tfoot>
                <tbody>
                    <?php foreach ($requests as $payment): ?>
                        <tr>
                            <td><?= htmlspecialchars($payment['id']); ?></td>
                            <td><?= htmlspecialchars($payment['customer_name']); ?></td>
                            <td><?= htmlspecialchars($payment['amount_paid']); ?></td>
                            <td><?= htmlspecialchars($payment['payment_date']); ?></td>
                            <td><?= htmlspecialchars($payment['description']); ?></td>
                            <td><?= htmlspecialchars($payment['created_at']); ?></td>
                            <td>
                                <form method="POST" action="generate_receipt.php" target="_blank">
                                    <input type="hidden" name="id" value="<?= htmlspecialchars($payment['id']); ?>">
                                    <button type="submit" class="btn btn-primary btn-sm">Generate Receipt</button>
                                </form>
                            </td>

                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php
include('footer.php');
?>