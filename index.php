<?php
require __DIR__ . '/app.php';
if (!isset($_SESSION['user'])) { redirect('login.php'); }
$table = $config['table']; // Table and column names come only from the local config.
$fields = array_keys($config['fields']);
$view = isset($_GET['view']) && is_string($_GET['view']) ? $_GET['view'] : 'list';
if (!in_array($view, ['list', 'new', 'edit', 'delete'], true)) { $view = 'list'; }
$id = filter_var($_GET['id'] ?? '', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$values = array_fill_keys($fields, ''); $errors = []; $error = ''; $rows = []; $record = null;
$flash = $_SESSION['flash'] ?? ''; unset($_SESSION['flash']);

try {
    $pdo = db();
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = filter_var(post('id'), FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        switch (post('action')) {
            case 'save':
                $view = post('id') === '' ? 'new' : 'edit';
                [$values, $errors] = validate($_POST);
                if ($view === 'edit' && !$id) { $error = t('missing'); }
                if (!$errors && !$error) {
                    if ($view === 'edit') {
                        $query = $pdo->prepare("SELECT id FROM $table WHERE id = ?"); $query->execute([$id]);
                        if (!$query->fetch()) { $error = t('missing'); }
                    }
                    if (!$error) {
                        if ($view === 'new') {
                            $columns = implode(', ', $fields); $placeholders = implode(', ', array_fill(0, count($fields), '?'));
                            $query = $pdo->prepare("INSERT INTO $table ($columns) VALUES ($placeholders)");
                            $query->execute(array_values($values));
                        } else {
                            $columns = implode(', ', array_map(fn($field) => "$field = ?", $fields));
                            $query = $pdo->prepare("UPDATE $table SET $columns WHERE id = ?");
                            $query->execute([...array_values($values), $id]);
                        }
                        $_SESSION['flash'] = t('saved'); redirect('index.php');
                    }
                }
                break;
            case 'delete':
                if (!$id) { $error = t('missing'); }
                else {
                    $query = $pdo->prepare("DELETE FROM $table WHERE id = ?"); $query->execute([$id]);
                    $_SESSION['flash'] = $query->rowCount() ? t('deleted') : t('missing'); redirect('index.php');
                }
                break;
            default: $error = t('bad_action');
        }
    }
    if (in_array($view, ['edit', 'delete'], true) && $_SERVER['REQUEST_METHOD'] !== 'POST') {
        $query = $pdo->prepare("SELECT * FROM $table WHERE id = ?"); $query->execute([$id ?: 0]); $record = $query->fetch();
        if (!$record) { $error = t('missing'); $view = 'list'; }
        else { $values = $record; }
    }
    if ($view === 'list') { $rows = $pdo->query("SELECT * FROM $table ORDER BY id DESC")->fetchAll(); }
} catch (PDOException $exception) {
    error_log($exception->getMessage());
    if ($exception->getCode() === '23000') { $errors[$fields[0]] = t('duplicate'); }
    else { $error = t('db_error'); }
}

$label = fn($field) => t($table === 'books' && $field === 'code' ? 'book_code' : $field);

// Set badge based on view
$pageBadge = in_array($view, ['new', 'edit'], true) ? t('badge_form') : t('badge_native');
header_page(t('list_' . $table), $pageBadge);
?>

<?php if ($view === 'list'): ?>

    <?php if ($flash): ?>
        <div class="alert alert-success" role="status">
            <div class="alert-content">
                <span class="alert-icon">✓</span>
                <div>
                    <strong><?= e(t('success_prefix')) ?></strong><?= e($flash) ?>
                </div>
            </div>
            <button type="button" class="alert-close" onclick="this.parentElement.remove()" aria-label="Close">✕</button>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger" role="alert">
            <div class="alert-content">
                <span class="alert-icon">⚠</span>
                <div>
                    <strong class="alert-title"><?= e(t('save_failed')) ?></strong>
                    <p class="alert-desc"><?= e($error) ?></p>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="page-heading">
        <div>
            <div class="heading-title-row">
                <h1><?= e(t('list_' . $table)) ?></h1>
                <span class="badge-total"><?= sprintf(t('total_data') !== 'total_data' ? t('total_data') : 'Total: %d Data', count($rows)) ?></span>
            </div>
            <p class="muted"><?= e(t('intro_' . $table)) ?></p>
        </div>
        <div>
            <a class="btn-primary" href="index.php?view=new"><?= e(t('add')) ?></a>
        </div>
    </div>

    <?php if (!$rows): ?>
        <div class="empty-state">
            <div class="empty-icon-wrap">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/>
                    <path d="m3.3 7 8.7 5 8.7-5"/>
                    <path d="M12 22V12"/>
                </svg>
            </div>
            <h3><?= e(t('empty')) ?></h3>
            <p><?= e(t('empty_desc')) ?></p>
            <a class="btn-primary" href="index.php?view=new"><?= e(t('add_first')) ?></a>
        </div>
    <?php else: ?>
        <div class="table-card">
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <?php foreach ($fields as $field): ?>
                                <th scope="col"><?= e($label($field)) ?></th>
                            <?php endforeach; ?>
                            <th scope="col"><?= e(t('actions')) ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rows as $row): ?>
                            <tr>
                                <?php foreach ($fields as $field): ?>
                                    <?php
                                    $val = $row[$field];
                                    if ($field === 'code'): ?>
                                        <td class="col-code"><?= e($val) ?></td>
                                    <?php elseif ($field === 'name' || $field === 'title'): ?>
                                        <td class="col-name"><strong><?= e($val) ?></strong></td>
                                    <?php elseif ($field === 'category'): ?>
                                        <td><span class="badge-category"><?= e($val) ?></span></td>
                                    <?php elseif ($field === 'quantity'): ?>
                                        <?php
                                        $qty = (int) $val;
                                        $qtyClass = $qty >= 100 ? 'qty-high' : ($qty <= 5 ? 'qty-low' : 'qty-normal');
                                        ?>
                                        <td><span class="stock-qty <?= $qtyClass ?>"><?= e($val) ?></span></td>
                                    <?php elseif ($field === 'unit'): ?>
                                        <td><span class="item-unit"><?= e($val) ?></span></td>
                                    <?php else: ?>
                                        <td><?= e($val) ?></td>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                                <td class="col-actions">
                                    <a class="btn-action btn-edit" href="index.php?view=edit&amp;id=<?= e($row['id']) ?>"><?= e(t('edit')) ?></a>
                                    <a class="btn-action btn-delete" href="index.php?view=delete&amp;id=<?= e($row['id']) ?>"><?= e(t('delete')) ?></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>

<?php elseif ($view === 'delete'): ?>

    <section class="delete-card">
        <div class="card-step-tag tag-delete">HALAMAN 5</div>
        <div class="delete-header">
            <h2><?= e(t('delete_title')) ?></h2>
            <a href="index.php" class="close-link"><?= e(t('close')) ?></a>
        </div>

        <div class="alert alert-warning">
            <div class="alert-content">
                <span class="alert-icon">⚠</span>
                <div>
                    <strong><?= e(t('delete_warning')) ?></strong>
                </div>
            </div>
        </div>

        <div class="delete-details-box">
            <h3 class="details-heading"><?= e(t('selected_item_heading')) ?></h3>
            <div class="details-grid">
                <div class="grid-col">
                    <div class="detail-row">
                        <span class="detail-label"><?= e($label('code')) ?>:</span>
                        <span class="detail-val val-amber"><?= e($values['code'] ?? '') ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label"><?= e($label('name')) ?>:</span>
                        <span class="detail-val val-white val-lg"><?= e($values['name'] ?? '') ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label"><?= e(t('current_stock')) ?>:</span>
                        <span class="detail-val val-green"><?= e($values['quantity'] ?? '') ?> <?= e($values['unit'] ?? '') ?></span>
                    </div>
                </div>
                <div class="grid-col">
                    <div class="detail-row">
                        <span class="detail-label"><?= e($label('category')) ?>:</span>
                        <span class="detail-val val-white"><?= e($values['category'] ?? '') ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label"><?= e($label('unit')) ?>:</span>
                        <span class="detail-val val-white"><?= e($values['unit'] ?? '') ?></span>
                    </div>
                </div>
            </div>
        </div>

        <form method="post" class="delete-form">
            <?php csrf(); ?>
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="<?= e($id) ?>">
            <div class="delete-actions-bar">
                <a class="btn-cancel" href="index.php"><?= e(t('cancel_back')) ?></a>
                <button type="submit" class="btn-danger"><?= e(t('confirm_delete_btn')) ?></button>
            </div>
        </form>

        <div class="sql-preview-note">
            <code>SQL: DELETE FROM <?= e($table) ?> WHERE id = '<?= e($id) ?>';</code>
        </div>
    </section>

<?php else: ?>

    <div class="form-subnav">
        <a class="back-link" href="index.php">← <?= e(t('back_to_list')) ?></a>
        <span class="method-tag"><?= e(t('native_method')) ?></span>
    </div>

    <?php if ($error || $errors): ?>
        <div class="alert alert-danger" role="alert">
            <div class="alert-content">
                <span class="alert-icon">⚠</span>
                <div>
                    <strong class="alert-title"><?= e(t('save_failed')) ?></strong>
                    <p class="alert-desc"><?= e($error ?: t('save_failed_desc')) ?></p>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <section class="form-card">
        <div class="form-card-header">
            <div class="form-title-group">
                <h2>
                    <?= e(t($view === 'new' ? 'new_title' : 'edit_title')) ?>
                    <?php if ($view === 'edit'): ?>
                        <span class="badge-id">ID : <?= e($values['code'] ?: $id) ?></span>
                    <?php endif; ?>
                </h2>
                <p class="form-subtitle"><?= e(t($view === 'new' ? 'new_subtitle' : 'edit_subtitle')) ?></p>
            </div>
            <div class="form-header-right">
                <span class="all-required"><?= e(t('all_required')) ?></span>
            </div>
        </div>

        <form method="post" class="main-form">
            <?php csrf(); ?>
            <input type="hidden" name="action" value="save">
            <input type="hidden" name="id" value="<?= $view === 'edit' ? e($id) : '' ?>">

            <div class="fields-stack">
                <!-- Kode Barang -->
                <div class="field-item <?= isset($errors['code']) ? 'has-error' : '' ?>">
                    <div class="field-label-row">
                        <label for="code"><?= e($label('code')) ?> <span class="req">*</span></label>
                        <span class="field-hint">Maks. 30 karakter, unik</span>
                    </div>
                    <input id="code" name="code" type="text" value="<?= e($values['code']) ?>" maxlength="30" required <?= isset($errors['code']) ? 'aria-invalid="true"' : '' ?>>
                    <?php if (isset($errors['code'])): ?>
                        <p class="field-error-msg">● <?= e($errors['code']) ?></p>
                    <?php endif; ?>
                </div>

                <!-- Nama Barang -->
                <div class="field-item <?= isset($errors['name']) ? 'has-error' : '' ?>">
                    <div class="field-label-row">
                        <label for="name"><?= e($label('name')) ?> <span class="req">*</span></label>
                        <span class="field-hint">Maks. 120 karakter</span>
                    </div>
                    <input id="name" name="name" type="text" value="<?= e($values['name']) ?>" maxlength="120" required <?= isset($errors['name']) ? 'aria-invalid="true"' : '' ?>>
                    <?php if (isset($errors['name'])): ?>
                        <p class="field-error-msg">● <?= e($errors['name']) ?></p>
                    <?php endif; ?>
                </div>

                <!-- Kategori -->
                <div class="field-item <?= isset($errors['category']) ? 'has-error' : '' ?>">
                    <div class="field-label-row">
                        <label for="category"><?= e($label('category')) ?> <span class="req">*</span></label>
                        <span class="field-hint">Maks. 80 karakter</span>
                    </div>
                    <input id="category" name="category" type="text" value="<?= e($values['category']) ?>" maxlength="80" required <?= isset($errors['category']) ? 'aria-invalid="true"' : '' ?>>
                    <?php if (isset($errors['category'])): ?>
                        <p class="field-error-msg">● <?= e($errors['category']) ?></p>
                    <?php endif; ?>
                </div>

                <!-- 2 Kolom: Jumlah Stok & Satuan -->
                <div class="fields-row-2">
                    <div class="field-item <?= isset($errors['quantity']) ? 'has-error' : '' ?>">
                        <div class="field-label-row">
                            <label for="quantity"><?= e($label('quantity')) ?> <span class="req">*</span></label>
                            <span class="field-hint">0 - 1.000.000</span>
                        </div>
                        <input id="quantity" name="quantity" type="number" min="0" max="1000000" step="1" value="<?= e($values['quantity']) ?>" required <?= isset($errors['quantity']) ? 'aria-invalid="true"' : '' ?>>
                        <?php if (isset($errors['quantity'])): ?>
                            <p class="field-error-msg">● <?= e($errors['quantity']) ?></p>
                        <?php else: ?>
                            <p class="field-hint-sub"><?= e(t('stock_hint')) ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="field-item <?= isset($errors['unit']) ? 'has-error' : '' ?>">
                        <div class="field-label-row">
                            <label for="unit"><?= e($label('unit')) ?> <span class="req">*</span></label>
                            <span class="field-hint">Maks. 30 karakter</span>
                        </div>
                        <input id="unit" name="unit" type="text" value="<?= e($values['unit']) ?>" maxlength="30" required <?= isset($errors['unit']) ? 'aria-invalid="true"' : '' ?>>
                        <?php if (isset($errors['unit'])): ?>
                            <p class="field-error-msg">● <?= e($errors['unit']) ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="form-actions-bar">
                <a class="btn-cancel" href="index.php"><?= e(t('cancel')) ?></a>
                <button type="submit" class="btn-primary"><?= e(t('save')) ?></button>
            </div>
        </form>
    </section>

<?php endif; ?>

<?php
$footerText = in_array($view, ['new', 'edit'], true) ? t('footer_form') : t('footer_list');
footer_page($footerText);
?>
