<?php
include('header.php');
?>
<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Generate a short ID
        function generateShortId($length = 8) {
            return substr(bin2hex(random_bytes(16)), 0, $length);
        }
        $id = generateShortId(8);

        // Get form data
        $customerName = $_POST['customer_name'];
        $amountPaid = $_POST['amount_paid'];
        $paymentDate = $_POST['payment_date'];
        $description = $_POST['description'];

        // Insert data into the database
        $stmt = $pdo->prepare("INSERT INTO payments (id, customer_name, amount_paid, payment_date, description) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$id, $customerName, $amountPaid, $paymentDate, $description]);

        echo "<div class='alert alert-success'>Payment successfully added with ID: $id</div>";
        echo "<a href='index.php' class='btn btn-primary mt-3'>Go to Dashboard</a>";
        exit;
        
    } catch (PDOException $e) {
        echo "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
    }
}
?>
    <div class="container-fluid mt-5">
        <h2 class="mb-4">Add Payment</h2>
        <form method="POST" action="">
            <div class="mb-3">
                <label for="customer_name" class="form-label">Customer Name</label>
                <input type="text" class="form-control" id="customer_name" name="customer_name" required>
            </div>
            <div class="mb-3">
                <label for="amount_paid" class="form-label">Amount Paid</label>
                <input type="number" step="0.01" class="form-control" id="amount_paid" name="amount_paid" required>
            </div>
            <div class="mb-3">
                <label for="payment_date" class="form-label">Payment Date</label>
                <input type="date" class="form-control" id="payment_date" name="payment_date" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Submit Payment</button>
        </form>
    </div>

<?php
include('footer.php');
?>