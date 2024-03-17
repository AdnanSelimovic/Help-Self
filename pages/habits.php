<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HelpSelf - Habits</title>

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
                                <a class="nav-link" href="/Help-Self/home">Home</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link active" aria-current="page" href="/Help-Self/habits">Habits</a>
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
                            <li><a class="dropdown-item" href="/Help-Self/profile">Profile</a></li>
                                <li><a class="dropdown-item" href="/Help-Self/settings">Settings</a></li>
                                <li><a class="dropdown-item" href="/Help-Self/landing">Log out</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>



    </header>


    <!-- Habits Section -->
    <main class="container mt-5">
        <h2 class="text-center mb-4">Habits</h2>

        <div class="row">
            <div class="col-12 text-center mb-4">
                <a href="/Help-Self/habits/edit" class="btn btn-accent w-100">Create Habit</a>
            </div>
        </div>

        <!-- Habit Cards -->
        <div class="row">
            <div class="col-md-4">
                <div class="card habit-card">
                    <div class="card-body">
                        <h5 class="card-title">Miles Walked</h5>
                        <p class="card-text">Walking is a simple way to improve health and mood.</p>
                        <p class="card-text"><strong>Unit:</strong> Miles</p>
                        <p class="card-text"><strong>Verb:</strong> Walked</p>
                        <p class="card-text"><strong>Increment:</strong> 25 miles</p>
                        <p class="card-text"><strong>Milestone:</strong> 100 miles</p>
                        <p class="card-text"><strong>Creation Date:</strong> 01-01-2024</p>
                        <div class="button-container d-flex justify-content-between">
                            <a href="#" class="btn btn-warning">Update</a>
                            <a href="#" class="btn btn-action">Pause</a>
                            <a href="#" class="btn btn-danger">Delete</a>
                        </div>
                    </div>
                </div>
            </div>


            <div class="col-md-4">
                <div class="card habit-card">
                    <div class="card-body">
                        <h5 class="card-title">Books Read</h5>
                        <p class="card-text">Reading expands the mind and enriches the soul.</p>
                        <p class="card-text"><strong>Unit:</strong> Books</p>
                        <p class="card-text"><strong>Verb:</strong> Read</p>
                        <p class="card-text"><strong>Increment:</strong> 1 books</p>
                        <p class="card-text"><strong>Milestone:</strong> 1 books</p>
                        <p class="card-text"><strong>Creation Date:</strong> 02-02-2024</p>
                        <div class="d-flex justify-content-between">
                            <a href="#" class="btn btn-warning">Update</a>
                            <a href="#" class="btn btn-action">Pause</a>
                            <a href="#" class="btn btn-danger">Delete</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card habit-card">
                    <div class="card-body">
                        <h5 class="card-title">Water Intake</h5>
                        <p class="card-text">Staying hydrated is essential for overall health.</p>
                        <p class="card-text"><strong>Unit:</strong> Litres</p>
                        <p class="card-text"><strong>Verb:</strong> Drank</p>
                        <p class="card-text"><strong>Increment:</strong> 0.5 litres</p>
                        <p class="card-text"><strong>Milestone:</strong> 3 litres</p>
                        <p class="card-text"><strong>Creation Date:</strong> 03-03-2024</p>
                        <div class="d-flex justify-content-between">
                            <a href="#" class="btn btn-warning">Update</a>
                            <a href="#" class="btn btn-action">Pause</a>
                            <a href="#" class="btn btn-danger">Delete</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card habit-card">
                    <div class="card-body">
                        <h5 class="card-title">Daily Meditation</h5>
                        <p class="card-text">Meditation helps with stress and improves focus.</p>
                        <p class="card-text"><strong>Unit:</strong> Minutes</p>
                        <p class="card-text"><strong>Verb:</strong> Meditated</p>
                        <p class="card-text"><strong>Increment:</strong> 10 minutes</p>
                        <p class="card-text"><strong>Milestone:</strong> 60 minutes</p>
                        <p class="card-text"><strong>Creation Date:</strong> 04-04-2024</p>
                        <div class="button-container d-flex justify-content-between">
                            <a href="#" class="btn btn-warning">Update</a>
                            <a href="#" class="btn btn-action2">Resume</a>
                            <a href="#" class="btn btn-danger">Delete</a>
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