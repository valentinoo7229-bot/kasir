<?php
// Password yang Anda inginkan
 $passwordToHash = 'admin123';

// Hash password menggunakan algoritma terbaik saat ini
 $hashedPassword = password_hash($passwordToHash, PASSWORD_DEFAULT);

// Tampilkan hasilnya
echo "Password asli: " . $passwordToHash . "<br>";
echo "Password hash yang harus Anda copy:<br><br>";
echo "<code>" . $hashedPassword . "</code>";
?>