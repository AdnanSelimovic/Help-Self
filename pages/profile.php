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
                                <a class="nav-link" href="/Help-Self/home">Home</a>
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
                                <li><a class="dropdown-item" href="/Help-Self/profile">Profile</a></li>
                                <li><a class="dropdown-item" href="/Help-Self/settings">Settings</a></li>
                                <li><a class="dropdown-item" href="/Help-Self/">Log out</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <main class="container mt-5">
        <div class="row">
            <div class="col-md-4 mb-3">
                <div class="card">
                    <img src="/Help-Self/assets/images/profile-placeholder.png" class="card-img-top"
                        alt="Profile Picture">
                    <div class="card-body">
                        <h5 class="card-title">Username</h5>
                        <p class="card-text">Short bio or description about the user.</p>
                        <a href="/Help-Self/settings" class="btn btn-primary">Edit Profile</a>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Profile Details</h4>
                        <p class="card-text"><strong>Full Name:</strong> John Doe</p>
                        <p class="card-text"><strong>Email:</strong> john.doe@example.com</p>
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