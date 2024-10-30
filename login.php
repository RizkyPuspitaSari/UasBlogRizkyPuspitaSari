    <?php
    $host = 'localhost';
    $dbname = 'uas_blog'; // Ganti dengan nama database Anda
    $username = 'root';
    $password = '';

    try {
        // Membuat koneksi PDO
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die("Koneksi ke database gagal: " . $e->getMessage());
    }
    try {
        // Membuat koneksi PDO
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
        // Ambil semua pengguna
        $stmt = $pdo->query("SELECT id, password FROM users");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        foreach ($users as $user) {
            $plainPassword = $user['password'];
    
            // Cek apakah password sudah di-hash
            if (!password_get_info($plainPassword)['algo']) {
                // Jika belum di-hash, lakukan hashing
                $hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);
    
                // Update password di database
                $updateStmt = $pdo->prepare("UPDATE users SET password = :hashedPassword WHERE id = :id");
                $updateStmt->execute([
                    'hashedPassword' => $hashedPassword,
                    'id' => $user['id']
                ]);
    
                echo "Password untuk user dengan ID {$user['id']} berhasil di-hash.<br>";
            } else {
                echo "User dengan ID {$user['id']} sudah memiliki password yang di-hash.<br>";
            }
        }
    
        echo "Proses hashing selesai.";
    } catch (PDOException $e) {
        die("Koneksi ke database gagal: " . $e->getMessage());
    }
    session_start();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $usernameOrEmail = $_POST['username'];
        $password = $_POST['password'];

        // Cek user berdasarkan username atau email
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :usernameOrEmail OR email = :usernameOrEmail");
        $stmt->execute(['usernameOrEmail' => $usernameOrEmail]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            // Password benar, buat sesi untuk user
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header("Location: form_admin.php"); // Arahkan ke halaman dashboard
            exit;
        } else {
            // Password atau username/email salah
            echo "<script>alert('Username/email atau password salah!'); window.location.href='login .html';</script>";
        }
    }
