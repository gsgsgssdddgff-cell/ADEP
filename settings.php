<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) { header("Location: login.php"); exit; }
require_once '../common/config.php';

// UPDATE SETTINGS
if (isset($_POST['save_settings'])) {
    $sql = "UPDATE payment_settings SET upi_id=?, bank_name=?, acc_no=?, ifsc=?, contact_no=? WHERE id=1";
    $pdo->prepare($sql)->execute([
        $_POST['upi'], $_POST['bank'], $_POST['acc'], $_POST['ifsc'], $_POST['contact']
    ]);
    echo "<script>alert('✅ Payment Details Updated!');</script>";
}

// FETCH CURRENT SETTINGS
$s = $pdo->query("SELECT * FROM payment_settings WHERE id=1")->fetch();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Settings</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-black text-white p-6">

    <div class="max-w-lg mx-auto bg-[#111] p-8 rounded-xl border border-gray-800">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-yellow-500">PAYMENT SETTINGS</h1>
            <a href="dashboard.php" class="text-sm text-gray-400 hover:text-white">Back</a>
        </div>

        <form method="POST" class="space-y-4">
            
            <div>
                <label class="block text-xs text-gray-500 mb-1">UPI ID (For GPay/PhonePe)</label>
                <input type="text" name="upi" value="<?= $s['upi_id'] ?>" class="w-full bg-black border border-gray-700 p-3 rounded text-white font-mono text-yellow-400">
            </div>

            <div>
                <label class="block text-xs text-gray-500 mb-1">Bank Name</label>
                <input type="text" name="bank" value="<?= $s['bank_name'] ?>" class="w-full bg-black border border-gray-700 p-3 rounded text-white">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Account No</label>
                    <input type="text" name="acc" value="<?= $s['acc_no'] ?>" class="w-full bg-black border border-gray-700 p-3 rounded text-white">
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">IFSC Code</label>
                    <input type="text" name="ifsc" value="<?= $s['ifsc'] ?>" class="w-full bg-black border border-gray-700 p-3 rounded text-white">
                </div>
            </div>

            <div>
                <label class="block text-xs text-gray-500 mb-1">Admin Contact Number</label>
                <input type="text" name="contact" value="<?= $s['contact_no'] ?>" class="w-full bg-black border border-gray-700 p-3 rounded text-white">
            </div>

            <button type="submit" name="save_settings" class="w-full bg-green-600 py-3 rounded font-bold hover:bg-green-500 mt-4">
                SAVE DETAILS
            </button>

        </form>
    </div>

</body>
</html>