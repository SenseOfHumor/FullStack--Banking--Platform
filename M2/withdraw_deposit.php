<?php
require(__DIR__ . "/partials/nav.php");

// Initialize database connection
$db = getDB();

// Fetch the logged-in user's ID from the session
$user_id = $_SESSION["user"]["id"];

// Fetching the user's ID from the database
$stmt = $db->prepare("SELECT id FROM Users WHERE id = :id");
$stmt->execute([":id" => $user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    // User ID found in the database, assign it to $user_id
    $user_id = $user['id'];
}

// Fetching the number of existing accounts for the logged-in user
$stmt = $db->prepare("SELECT COUNT(*) AS num_accounts FROM Accounts WHERE user_id = :user_id");
$stmt->execute([":user_id" => $user_id]);
$num_accounts = $stmt->fetch(PDO::FETCH_ASSOC)["num_accounts"];

// Fetching the existing account balances for the logged-in user
$stmt = $db->prepare("SELECT balance FROM Accounts WHERE user_id = :user_id");
$stmt->execute([":user_id" => $user_id]);
$account_balances = $stmt->fetchAll(PDO::FETCH_COLUMN);


// Fetching the account numbers for the logged-in user
$stmt = $db->prepare("SELECT account_number FROM Accounts WHERE user_id = :user_id");
$stmt->execute([":user_id" => $user_id]);
$accounts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Account deposit logic
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["balance"])) {
    // Validate input
    $balance = floatval($_POST["balance"]); // Convert balance to float
    if ($balance <= 0) {
        echo "<p style='color: red;'>Please enter a valid deposit amount.</p>";
    } else {
        $selected_account = $_POST["selected_account"];
        // Update selected account balance
        $stmt = $db->prepare("UPDATE Accounts SET balance = balance + :balance WHERE account_number = :account_number AND user_id = :user_id");
        $stmt->execute([
            ":balance" => $balance,
            ":account_number" => $selected_account,
            ":user_id" => $user_id
        ]);

        // Update world account balance (add to it for deposit)
        $stmt = $db->prepare("UPDATE Accounts SET balance = balance + :balance WHERE id = -1");
        $stmt->execute([":balance" => $balance]);

        // Record deposit transaction
        recordTransaction($db, '000000000000', $selected_account, $balance, 'deposit', 'Deposit', $balance);

        // Show success message
        echo "<p style='color: green;'>Deposit of $balance made successfully.</p>";
    }
}

// Withdrawal logic
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["withdraw_amount"])) {
    // Validate input
    $withdraw_amount = floatval($_POST["withdraw_amount"]); // Convert withdrawal amount to float
    $selected_account = $_POST["selected_account"];

    // Check account balance
    $stmt = $db->prepare("SELECT balance FROM Accounts WHERE account_number = :account_number AND user_id = :user_id");
    $stmt->execute([
        ":account_number" => $selected_account,
        ":user_id" => $user_id
    ]);
    $account_balance = $stmt->fetchColumn();

    if ($withdraw_amount <= 0) {
        echo "<p style='color: red;'>Please enter a valid withdrawal amount.</p>";
    } elseif ($withdraw_amount > $account_balance) {
        echo "<p style='color: red;'>Insufficient funds for withdrawal.</p>";
    } else {
        // Proceed with withdrawal transaction
        $stmt = $db->prepare("UPDATE Accounts SET balance = balance - :withdraw_amount WHERE account_number = :account_number AND user_id = :user_id");
        $stmt->execute([
            ":withdraw_amount" => $withdraw_amount,
            ":account_number" => $selected_account,
            ":user_id" => $user_id
        ]);

        // Update world account balance (subtract from it for withdrawal)
        $stmt = $db->prepare("UPDATE Accounts SET balance = balance - :withdraw_amount WHERE id = -1");
        $stmt->execute([":withdraw_amount" => $withdraw_amount]);

        // Record withdrawal transaction
        recordTransaction($db, $selected_account, '000000000000', -$withdraw_amount, 'withdrawal', 'Withdrawal', $account_balance - $withdraw_amount);

        // Show success message
        echo "<p style='color: green;'>Withdrawal of $withdraw_amount made successfully.</p>";
    }
}

// Function to generate a random account number
function generateAccountNumber() {
    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $account_number = '';
    for ($i = 0; $i < 12; $i++) {
        $account_number .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $account_number;
}

// Function to record a transaction
function recordTransaction($db, $account_src, $account_dest, $balance_change, $transaction_type, $memo, $expected_total) {
    $stmt = $db->prepare("INSERT INTO Transactions (account_src, account_dest, balance_change, transaction_type, memo, expected_total, created) VALUES (:account_src, :account_dest, :balance_change, :transaction_type, :memo, :expected_total, NOW())");
    $stmt->execute([
        ":account_src" => $account_src,
        ":account_dest" => $account_dest,
        ":balance_change" => $balance_change,
        ":transaction_type" => $transaction_type,
        ":memo" => $memo,
        ":expected_total" => $expected_total
    ]);
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
        width: 350px;
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

    input[type="number"] {
        background: #ecf0f3;
        padding: 10px;
        padding-left: 20px;
        height: 50px;
        font-size: 14px;
        border-radius: 50px;
        box-shadow: inset 6px 6px 6px #cbced1, inset -6px -6px 6px white;
        width: calc(100% - 40px);
        margin-bottom: 20px;
    }

    select {
        background: #ecf0f3;
        padding: 10px;
        height: 50px;
        font-size: 14px;
        border-radius: 50px;
        box-shadow: inset 6px 6px 6px #cbced1, inset -6px -6px 6px white;
        width: calc(100% - 40px);
        margin-bottom: 20px;
        border: none;
    }
    input[type="submit"] {
        background-color: #1DA1F2;
        border: none;
        border-radius: 20px;
        color: #fff;
        font-size: 1rem;
        font-weight: bold;
        cursor: pointer;
        padding: 10px;
        width: calc(100% - 40px);
        transition: background-color 0.3s ease;
    }

    input[type="submit"]:hover {
        background-color: #0d8ae5;
    }

    p {
        margin-bottom: 20px;
    }

    ul {
        margin-top: 20px;
    }

    ul li {
        list-style: none;
        margin-bottom: 10px;
    }
</style>

<div class="container">
    <div class="brand-title">Deposit / Withdraw</div>

    <form method="POST">
        <div>
            <label for="balance">Deposit Amount:</label>
            <input type="number" step="0.01" id="balance" name="balance" placeholder="Enter deposit amount" required>
        </div>
        <div>
            <label for="selected_account">Select Account:</label>
            <select id="selected_account" name="selected_account" required>
                <option value="" selected disabled>Select an account</option>
                <?php foreach ($accounts as $account): ?>
                    <option value="<?php echo htmlspecialchars($account['account_number']); ?>"><?php echo htmlspecialchars($account['account_number']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <input type="submit" value="Deposit">
    </form>

    <form method="POST">
        <div>
            <label for="selected_account">Withdraw from Account:</label>
            <select id="selected_account" name="selected_account" required>
                <option value="" selected disabled>Select an account</option>
                <?php foreach ($accounts as $account): ?>
                    <option value="<?php echo htmlspecialchars($account['account_number']); ?>"><?php echo htmlspecialchars($account['account_number']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label for="withdraw_amount">Withdrawal Amount:</label>
            <input type="number" step="0.01" id="withdraw_amount" name="withdraw_amount" placeholder="Enter withdrawal amount" required>
        </div>
        <input type="submit" value="Withdraw">
    </form>

    <?php if ($accounts): ?>
    <h2>Existing Accounts</h2>
    <p>Number of existing accounts: <?php echo htmlspecialchars($num_accounts); ?></p>
    <ul>
        <?php foreach ($accounts as $account): ?>
            <?php
            // Fetch the balance for each account
            $stmt = $db->prepare("SELECT balance FROM Accounts WHERE account_number = :account_number AND user_id = :user_id");
            $stmt->execute([
                ":account_number" => $account["account_number"],
                ":user_id" => $user_id
            ]);
            $balance = $stmt->fetchColumn();
            ?>
            <li>
                <?php echo htmlspecialchars($account["account_number"]); ?> - Balance: <?php echo htmlspecialchars($balance); ?>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

</div>
