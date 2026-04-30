<?php
$files = glob("api/*.php");
foreach ($files as $file) {
    if (basename($file) == "koneksi.php") continue;
    $content = file_get_contents($file);
    $content = preg_replace('/session_start\(\)\s*;\s*/', '', $content);
    if (basename($file) == "check_session.php") {
        if (strpos($content, "koneksi.php") === false) {
            $content = str_replace("<?php", "<?php\nrequire_once __DIR__ . '/koneksi.php';", $content);
        }
    }
    file_put_contents($file, $content);
}
echo "Done.";
?>
