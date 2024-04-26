<?php
require(__DIR__ . "/partials/nav.php");

// Initialize database connection
$db = getDB();

// Fetch the logged-in user's ID from the session
$user_id = $_SESSION["user"]["id"];

// Fetching the account numbers for the logged-in user
$stmt = $db->prepare("SELECT account_number FROM Accounts WHERE user_id = :user_id");
$stmt->execute([":user_id" => $user_id]);
$accounts = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Fetching the transaction history for the selected account
$selected_account_number = isset($_GET['account_number']) ? $_GET['account_number'] : null;
$transaction_history = [];
if ($selected_account_number && in_array($selected_account_number, $accounts)) {
    $stmt = $db->prepare("SELECT t.account_src, t.account_dest, t.balance_change, t.transaction_type, t.memo, t.created, t.expected_total, a1.account_number AS src_account_number, a2.account_number AS dest_account_number FROM Transactions t LEFT JOIN Accounts a1 ON t.account_src = a1.account_number LEFT JOIN Accounts a2 ON t.account_dest = a2.account_number WHERE (t.account_src = :account_number OR t.account_dest = :account_number) ORDER BY t.created DESC LIMIT 10");
    $stmt->execute([":account_number" => $selected_account_number]);
    $transaction_history = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<style>
    body {
        background: url('bg1.jpg') no-repeat center center fixed;
        margin: 0;
        width: 100vw;
        height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: 'Poppins', sans-serif;
    }

    .container {
        width: 800px;
        padding: 40px;
        background: #ecf0f3;
        border-radius: 20px;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
    }

    .brand-title {
        margin-bottom: 20px;
        font-weight: 900;
        font-size: 1.8rem;
        color: #363636;
        letter-spacing: 1px;
    }

    label {
        font-weight: bold;
    }

    select {
        width: 100%;
        padding: 10px;
        font-size: 14px;
        border-radius: 5px;
        border: 1px solid #ccc;
        margin-bottom: 20px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    th, td {
        padding: 10px;
        border-bottom: 1px solid #ccc;
        text-align: left;
    }

    th {
        background-color: #f2f2f2;
    }

    td {
        background-color: #fff;
    }

    .error-msg {
        color: red;
        margin-bottom: 20px;
    }
</style>

<div class="container">
    <div class="brand-title">Transaction History</div>

    <form method="GET">
        <div>
            <label for="selected_account">Select Account:</label>
            <select id="selected_account" name="account_number" required>
                <option value="" selected disabled>Select an account</option>
                <?php foreach ($accounts as $account): ?>
                    <option value="<?php echo htmlspecialchars($account); ?>"><?php echo htmlspecialchars($account); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <input type="submit" value="View History">
    </form>

    <?php if ($selected_account_number && in_array($selected_account_number, $accounts)): ?>
        <h2>Transaction History</h2>
        <?php if (empty($transaction_history)): ?>
            <p class="error-msg">No transaction history found for this account.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Transaction Type</th>
                        <th>Destination Account</th>
                        <th>Source Account</th>
                        <th>Balance Change</th>
                        <th>Expected Total</th>
                        <th>Memo</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($transaction_history as $transaction): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($transaction['transaction_type']); ?></td>
                            <td><?php echo htmlspecialchars($transaction['src_account_number'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($transaction['dest_account_number'] ?? 'N/A'); ?></td>
                            <td>$<?php echo number_format($transaction['balance_change'], 2); ?></td>
                            <td>$<?php echo number_format($transaction['expected_total'], 2); ?></td>
                            <td><?php echo htmlspecialchars($transaction['memo']); ?></td>
                            <td><?php echo htmlspecialchars($transaction['created']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    <?php endif; ?>
</div>
