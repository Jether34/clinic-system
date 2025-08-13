<?php
$plaintext = 'admin_123.pns1';
$hashedPassword = password_hash($plaintext, PASSWORD_DEFAULT);

echo "✅ Your hashed password is:\n";
echo $hashedPassword;
?>
