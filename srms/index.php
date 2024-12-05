<?php
error_reporting(0);
include('includes/config.php'); 
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>IA RESULTS</title>
        <!-- Favicon-->
        <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
        <!-- Core theme CSS (includes Bootstrap)-->
        <link href="css/styles.css" rel="stylesheet" />
    </head>
    <body>
        <!-- Responsive navbar-->
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container">
                <!-- Add logo -->
                <a class="navbar-brand" href="index.php">
                    <img src="images/logo2.png" alt="Logo" style="height: 50px; margin-right: 20px;">
                    <img src="images/logo3.png" alt="Logo" style="height: 50px; margin-right: 20px;">
                    IA RESULTS
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                        <li class="nav-item"><a class="nav-link active" aria-current="page" href="#!">Home</a></li>
                        <li class="nav-item"><a class="nav-link active" href="find-result.php">Students</a></li>
                        <li class="nav-item"><a class="nav-link active" href="admin-login.php">Teacher</a></li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Header with dynamic elements -->
        <header class="text-center text-white" style="background-image: url('images/banner.jpg'); background-size: cover; background-position: center; height: 300px;">
            <div class="d-flex flex-column justify-content-center align-items-center h-100" style="background: rgba(0, 0, 0, 0.5);">
                <h1 class="display-4 fw-bold">Welcome to IA Results Portal</h1>
                <p class="lead">Effortlessly manage and access student internal assessments</p>
                <p class="mt-3"><i class="bi bi-calendar-event"></i> Today: <?php echo date('l, F j, Y'); ?></p>
                <div class="mt-3">
                    <a href="find-result.php" class="btn btn-primary me-2">Find Results</a>
                    <a href="admin-login.php" class="btn btn-secondary">Teacher Login</a>
                </div>
            </div>
        </header>

        <!-- Notice Board Section -->
        <section class="py-5 bg-white">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <h2 class="text-center mb-4">Notice Board</h2>
                        <div class="card shadow">
                            <div class="card-body" style="height: 300px; overflow-y: auto;">
                                <ul class="list-group list-group-flush">
                                    <?php 
                                    $sql = "SELECT * from tblnotice ORDER BY id DESC";
                                    $query = $dbh->prepare($sql);
                                    $query->execute();
                                    $results = $query->fetchAll(PDO::FETCH_OBJ);
                                    if ($query->rowCount() > 0) {
                                        foreach ($results as $result) { ?>
                                            <li class="list-group-item">
                                                <a href="notice-details.php?nid=<?php echo htmlentities($result->id); ?>" target="_blank">
                                                    <strong><?php echo htmlentities($result->noticeTitle); ?></strong>
                                                </a>
                                                <br>
                                                <small class="text-muted">Posted on: <?php echo htmlentities($result->postingDate); ?></small>
                                            </li>
                                    <?php }} else { ?>
                                        <li class="list-group-item text-center">No notices available</li>
                                    <?php } ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer-->
        <!-- Footer -->
        <footer class="py-4 bg-dark text-white">
            <div class="container text-center">
                <p class="mb-0">&copy; IA RESULTS | @SGBIT</p>
                <p class="small mb-0">
                    Developed by <strong>Your Team</strong>. All Rights Reserved.
                </p>
            </div>
        </footer>

        <!-- Bootstrap core JS-->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
        <!-- Core theme JS-->
        <script src="js/scripts.js"></script>
    </body>
</html>