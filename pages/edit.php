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
                                <a class="nav-link disabled" href="/Help-Self/home">Home</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link disabled" aria-current="page" href="/Help-Self/habits">Habits</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link disabled" href="/Help-Self/forum">Forum</a>
                            </li>
                        </ul>
                    </div>
                    
                    <ul class="navbar-nav">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle disabled" href="#" id="navbarDropdown" role="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                Account
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <li><a class="dropdown-item" href="#">Action</a></li>
                                <li><a class="dropdown-item" href="#">Another action</a></li>
                                <li><a class="dropdown-item" href="#">Something else here</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>



    </header>


    <main class="container mt-5">
        <h2 class="text-center mb-4">Edit Habit</h2>

        <div class="row justify-content-center">
            <div class="col-md-8">
                <form action="process_edit_habit.php" method="post">
                    <div class="mb-3">
                        <label for="habitTitle" class="form-label">Habit Title</label>
                        <input type="text" class="form-control" id="habitTitle" name="habitTitle" maxlength="25"
                            required>
                    </div>
                    <div class="mb-3">
                        <label for="habitDescription" class="form-label">Habit Description</label>
                        <textarea class="form-control" id="habitDescription" name="habitDescription" maxlength="60"
                            rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="habitUnit" class="form-label">Unit</label>
                        <input type="text" class="form-control" id="habitUnit" name="habitUnit" maxlength="20" required>
                    </div>
                    <div class="mb-3">
                        <label for="habitVerb" class="form-label">Verb</label>
                        <input type="text" class="form-control" id="habitVerb" name="habitVerb" maxlength="15" required>
                    </div>
                    <div class="mb-3">
                        <label for="habitIncrement" class="form-label">Increment</label>
                        <input type="number" class="form-control" id="habitIncrement" name="habitIncrement" min="1"
                            max="1000000" required>
                    </div>
                    <div class="mb-3">
                        <label for="habitMilestone" class="form-label">Milestone</label>
                        <input type="number" class="form-control" id="habitMilestone" name="habitMilestone" min="1"
                            max="1000000" required>
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-warning">Save</button>
                        <a href="/Help-Self/habits" class="btn btn-danger">Cancel</a>
                    </div>
                </form>
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