<?php
require 'db.php';

/* OTSING */
$search = "";
if (isset($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
}

/* PAGINATION */
$limit = 8;

if (isset($_GET['page'])) {
    $page = (int)$_GET['page'];
} else {
    $page = 1;
}

if ($page < 1) {
    $page = 1;
}

$offset = ($page - 1) * $limit;

/* WHERE */
$where = "WHERE status='available'";
if ($search != "") {
    $where .= " AND (brand LIKE '%$search%' OR model LIKE '%$search%')";
}

/* KOGU ARV */
$count_sql = "SELECT COUNT(*) as total FROM cars $where";
$count_res = mysqli_query($conn, $count_sql);
$count_row = mysqli_fetch_assoc($count_res);
$total_rows = $count_row['total'];
$total_pages = ceil($total_rows / $limit);

/* AUTOD */
$sql = "SELECT * FROM cars 
        $where 
        ORDER BY id DESC 
        LIMIT $limit OFFSET $offset";

$result = mysqli_query($conn, $sql);
?>

<!doctype html>
<html lang="et">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Autorent</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">

<h1 class="mb-4">Autod</h1>

<form method="GET" class="mb-4">
    <input type="text" name="search"
           value="<?php echo $search; ?>"
           placeholder="Otsi..."
           class="form-control">
</form>

<div class="row g-4">

<?php
if ($result && mysqli_num_rows($result) > 0) {

    while ($car = mysqli_fetch_assoc($result)) {
?>
        <div class="col-md-3">
            <div class="card h-100">

                <img src="<?php echo $car['image']; ?>"
                     class="card-img-top"
                     style="height:180px; object-fit:cover;">

                <div class="card-body d-flex flex-column">
                    <h6>
                        <?php echo $car['brand']; ?>
                        <?php echo $car['model']; ?>
                    </h6>

                    <div class="mt-auto">
                        <strong><?php echo $car['price_per_day']; ?> € / päev</strong>
                    </div>
                </div>

            </div>
        </div>
<?php
    }

} else {
    echo "<p>Autod puuduvad.</p>";
}
?>

</div>

<?php
if ($total_pages > 1) {
?>
<nav class="mt-4">
<ul class="pagination justify-content-center">
<?php
for ($i = 1; $i <= $total_pages; $i++) {

    if ($i == $page) {
?>
        <li class="page-item active">
            <span class="page-link"><?php echo $i; ?></span>
        </li>
<?php
    } else {
?>
        <li class="page-item">
            <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo $search; ?>">
                <?php echo $i; ?>
            </a>
        </li>
<?php
    }
}
?>
</ul>
</nav>
<?php
}
?>

</div>
</body>
</html>
