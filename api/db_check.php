<?php
/**
 * Database Connection Diagnostic Tool
 */

function mask($str)
{
    if (!$str)
        return '(empty)';
    if (strlen($str) <= 4)
        return '****';
    return substr($str, 0, 2) . str_repeat('*', strlen($str) - 4) . substr($str, -2);
}

function get_env_val($name)
{
    $val = getenv($name) ?: ($_ENV[$name] ?? null);
    return $val !== null ? $val : '(not set)';
}

echo "<h2>Database Connection Diagnostics</h2>";

$host = get_db_env_diag('DB_HOST', 'localhost');
$user = get_db_env_diag('DB_USER', 'root');
$pass = get_db_env_diag('DB_PASS', '');
$name = get_db_env_diag('DB_NAME', 'db_parkir');
$port = get_db_env_diag('DB_PORT', '3306');
$ssl = get_db_env_diag('DB_SSL', 'false');

function get_db_env_diag($name, $default = '')
{
    return getenv($name) ?: ($_ENV[$name] ?? $default);
}

echo "<ul>";
echo "<li><strong>DB_HOST:</strong> $host</li>";
echo "<li><strong>DB_USER:</strong> $user</li>";
echo "<li><strong>DB_PASS:</strong> " . mask($pass) . "</li>";
echo "<li><strong>DB_NAME:</strong> $name</li>";
echo "<li><strong>DB_PORT:</strong> $port</li>";
echo "<li><strong>DB_SSL:</strong> $ssl</li>";
echo "</ul>";

echo "<h3>Attempting Connection...</h3>";

$conn = mysqli_init();
if (strtolower($ssl) === 'true') {
    mysqli_ssl_set($conn, NULL, NULL, NULL, NULL, NULL);
    $flags = MYSQLI_CLIENT_SSL;
    echo "<p>SSL flags set.</p>";
}
else {
    $flags = 0;
}

$start = microtime(true);
$connected = @mysqli_real_connect($conn, $host, $user, $pass, $name, $port, NULL, $flags);
$end = microtime(true);

if ($connected) {
    echo "<h4 style='color: green;'>SUCCESS!</h4>";
    echo "<p>Connected in " . round(($end - $start) * 1000, 2) . "ms</p>";
    echo "<p>MySQL Server Info: " . mysqli_get_server_info($conn) . "</p>";
}
else {
    echo "<h4 style='color: red;'>FAILED!</h4>";
    echo "<p>Error Number: " . mysqli_connect_errno() . "</p>";
    echo "<p>Error Message: " . mysqli_connect_error() . "</p>";
}

echo "<hr><p><a href='/index.php'>Return to Home</a></p>";
?>
