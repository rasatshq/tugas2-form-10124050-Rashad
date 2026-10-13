<?php
$_SERVER['REQUEST_METHOD'] = 'GET';
require dirname(__DIR__) . '/app.php';
function check(bool $condition): void {
    if (!$condition) { throw new RuntimeException('Validation check failed'); }
}
$input = [];
foreach ($config['fields'] as $field => [$type, $limit]) { $input[$field] = $type === 'number' ? '1' : 'Sample'; }
check(validate($input)[1] === []);
foreach ($config['fields'] as $field => [$type, $limit]) {
    $bad = $input; $bad[$field] = '';
    check(isset(validate($bad)[1][$field]));
    $bad[$field] = $type === 'number' ? '-1' : str_repeat('a', $limit + 1);
    check(isset(validate($bad)[1][$field]));
    $bad[$field] = ['unexpected']; check(isset(validate($bad)[1][$field]));
    if ($type === 'number') {
        foreach (['1.5', (string) ($limit + 1), 'hello'] as $value) {
            $bad[$field] = $value; check(isset(validate($bad)[1][$field]));
        }
    }
}
check(e('<script>"&') === '&lt;script&gt;&quot;&amp;');
check(password_verify('Admin123!', '$2y$10$YRwrkB5q88Se3oqrbs0VLebQ8Ff2YEATymIIC1NQ7aC0Yee7kjJmC'));
check(array_keys($translations['id']) === array_keys($translations['en']));
echo "Validation, escaping, password and translation checks passed.\n";
