<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HelpSelf - Home</title>

    <link rel="icon" type="image/svg" href="/Help-Self/assets/images/favicon.svg">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <link rel="stylesheet" href="/Help-Self/assets/css/style.css">



</head>

<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-light bg-light navbar-custom">
            <div class="container-fluid">
                <a class="navbar-brand" href="/Help-Self/">
                    <img src="/Help-Self/assets/images/logo.svg" alt="HelpSelf Logo" height="30">
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">

                    <div class="nav-group mx-auto">
                        <ul class="navbar-nav">
                            <li class="nav-item">
                                <a class="nav-link active" aria-current="page" href="/Help-Self/home">Home</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/Help-Self/habits">Habits</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/Help-Self/forum">Forum</a>
                            </li>
                        </ul>
                    </div>

                    <ul class="navbar-nav">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                Account
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <li><a class="dropdown-item" href="pages\profile.php">Profile</a></li>
                                <li><a class="dropdown-item" href="pages\settings.php">Settings</a></li>
                                <li><a class="dropdown-item" href="pages\landing.php">Log out</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <!-- Dashboard Section -->
    <main class="container mt-5">
        <h2 class="text-center mb-4">Dashboard</h2>

        <!-- Overview and Stats -->
        <div class="row mb-3">
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Total Habits</h5>
                        <p class="card-text">3 Habits</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Average Rating</h5>
                        <p class="card-text">4.32/5</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Milestones Reached</h5>
                        <p class="card-text">25 Milestones</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- GraphsTrends -->
        <div class="row">
            <div class="col-12 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Habit Trends</h5>
                        <!-- graphs here -->
                        <p class="text-center">Graphs showing habit trends over time</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Habits Overview -->
        <h3 class="mb-3 mt-4">Habits Overview</h3>
        <div class="row">
            <!-- First Habit Card -->
            <div class="col-md-4">
                <div class="card habit-card">
                    <div class="card-body">
                        <h5 class="card-title">Miles Walked</h5>
                        <p class="card-text">Total Progress: 500 miles walked</p>
                        <p class="card-text">Today's Milestone: 75 miles out of 100 miles</p>
                        <div class="progress mb-2">
                            <div class="progress-bar" role="progressbar" style="width: 75%" aria-valuenow="75"
                                aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <p class="card-text">Daily Rating: 4/5</p>
                        <div class="button-container d-flex align-items-center justify-content-between">
                            <a href="#" class="btn btn-accent">Rate</a>
                            <a href="#" class="btn btn-secondary">Trend</a>
                            <a href="#" class="btn btn-success">Add</a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Second Habit Card -->
            <div class="col-md-4">
                <div class="card habit-card">
                    <div class="card-body">
                        <h5 class="card-title">Books Read</h5>
                        <p class="card-text">Total Progress: 20 books read</p>
                        <p class="card-text">Today's Milestone: 1 books out of 1 books</p>
                        <div class="progress mb-2">
                            <div class="progress-bar bg-primary" role="progressbar" style="width: 100%"
                                aria-valuenow="80" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <p class="card-text">Daily Rating: 5/5</p>
                        <div class="button-container d-flex align-items-center justify-content-between">
                            <a href="#" class="btn btn-accent">Rate</a>
                            <a href="#" class="btn btn-secondary">Trend</a>
                            <a href="#" class="btn btn-success">Add</a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Third Habit Card -->
            <div class="col-md-4">
                <div class="card habit-card">
                    <div class="card-body">
                        <h5 class="card-title">Water Intake</h5>
                        <p class="card-text">Total Progress: 2 litres drank</p>
                        <p class="card-text">Today's Milestone: 2 litres out of 3 litres</p>
                        <div class="progress mb-2">
                            <div class="progress-bar bg-primary" role="progressbar" style="width: 66%"
                                aria-valuenow="66" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <p class="card-text">Daily Rating: N/A</p>
                        <div class="button-container d-flex align-items-center justify-content-between">
                            <a href="#" class="btn btn-accent">Rate</a>
                            <a href="#" class="btn btn-secondary">Trend</a>
                            <a href="#" class="btn btn-success">Add</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>







    </main>






    <!-- Footer -->
    <?php include 'pages\includes\footer.php'; ?>

    <!-- Include Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
</body>

</html>