<?php
// Koneksi ke server MySQL
$servername = "localhost"; // Ganti dengan server Anda
$username = "root"; // Ganti dengan username Anda
$password = ""; // Ganti dengan password Anda

// Membuat koneksi
$conn = new mysqli($servername, $username, $password);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Membuat database jika belum ada
$dbname = "uts5e";
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
if ($conn->query($sql) === TRUE) {
    echo "Database $dbname berhasil dibuat atau sudah ada.<br>";
} else {
    echo "Error membuat database: " . $conn->error . "<br>";
}

// Menggunakan database
$conn->select_db($dbname);

// Membuat tabel krs jika belum ada
$sql = "CREATE TABLE IF NOT EXISTS krs (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    nim VARCHAR(10) NOT NULL,
    kelas ENUM('5A', '5B', '5C', '5D', '5E') NOT NULL,
    mata_kuliah TEXT NOT NULL
)";
if ($conn->query($sql) === TRUE) {
    echo "Tabel krs berhasil dibuat atau sudah ada.<br>";
} else {
    echo "Error membuat tabel: " . $conn->error . "<br>";
}

// Fungsi untuk membuat data KRS
function createKRS($conn, $nama, $nim, $kelas, $mataKuliah)
{
    $sql = "INSERT INTO krs (nama, nim, kelas, mata_kuliah) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $mataKuliahStr = implode(", ", $mataKuliah); // Menggabungkan mata kuliah yang dipilih
    $stmt->bind_param("ssss", $nama, $nim, $kelas, $mataKuliahStr);

    if ($stmt->execute()) {
        echo "Data KRS berhasil ditambahkan.<br>";
    } else {
        echo "Error: " . $stmt->error . "<br>";
    }
    $stmt->close();
}

// Fungsi untuk membaca data KRS
function readKRS($conn)
{
    $sql = "SELECT * FROM krs";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "<h2>Data KRS Mahasiswa</h2>";
        echo "<table border='1'>
                <tr>
                    <th>Nama</th>
                    <th>NIM</th>
                    <th>Kelas</th>
                    <th>Mata Kuliah</th>
                    <th>Aksi</th>
                </tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['nama']}</td>
                    <td>{$row['nim']}</td>
                    <td>{$row['kelas']}</td>
                    <td>{$row['mata_kuliah']}</td>
                    <td>
                        <a href='?edit={$row['id']}'>Edit</a>
                        <a href='?delete={$row['id']}'>Delete</a>
                    </td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "Tidak ada data KRS.<br>";
    }
}

// Fungsi untuk memperbarui data KRS
function updateKRS($conn, $id, $nama, $nim, $kelas, $mataKuliah)
{
    $sql = "UPDATE krs SET nama=?, nim=?, kelas=?, mata_kuliah=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $mataKuliahStr = implode(", ", $mataKuliah); // Menggabungkan mata kuliah yang dipilih
    $stmt->bind_param("ssssi", $nama, $nim, $kelas, $mataKuliahStr, $id);

    if ($stmt->execute()) {
        echo "Data KRS berhasil diperbarui.<br>";
    } else {
        echo "Error: " . $stmt->error . "<br>";
    }
    $stmt->close();
}

// Fungsi untuk menghapus data KRS
function deleteKRS($conn, $id)
{
    $sql = "DELETE FROM krs WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "Data KRS berhasil dihapus.<br>";
    } else {
        echo "Error: " . $stmt->error . "<br>";
    }
    $stmt->close();
}

// Proses form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = $_POST['nama'];
    $nim = $_POST['nim'];
    $kelas = $_POST['kelas'];
    $mataKuliah = $_POST['mata_kuliah'];

    createKRS($conn, $nama, $nim, $kelas, $mataKuliah);
}

// Proses update
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $nama = $_POST['nama'];
        $nim = $_POST['nim'];
        $kelas = $_POST['kelas'];
        $mataKuliah = $_POST['mata_kuliah'];

        updateKRS($conn, $id, $nama, $nim, $kelas, $mataKuliah);
    }
}

// Proses delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    deleteKRS($conn, $id);
}

// Menampilkan data KRS
readKRS($conn);

// Tutup koneksi
$conn->close();
?>