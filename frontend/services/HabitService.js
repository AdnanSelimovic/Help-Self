var HabitService = {
  userId: 10, // Static user ID for demonstration

  // Function to display user's habit dashboard
  updateHabitDashboard: function () {
    RestClient.get(
      "/../../backend/rest/get_habits.php", 
      (response) => {
        console.log("Dashboard data retrieved successfully", response);
        var data = JSON.parse(response);
        if (data.status === "success") {
          this.displayDashboardData(data.data);
          
        } else {
          console.error(data.message);
          toastr.error(data.message);
        }
      },
      (error) => {
        console.error("Failed to retrieve dashboard data", error);
        toastr.error(
          "Failed to retrieve dashboard data: " + error.responseText
        );
      }
    );
  },

  // Helper function to display habit data on the dashboard
  displayDashboardData: function (data) {
    
    var habits = Array.isArray(data) ? data : [data];

    $("#totalHabits").text(`${habits.length} Habits`);
    var totalRatings = 0;
    var numberOfRatings = 0;

    habits.forEach((habit) => {
      if (habit.ratings && Array.isArray(habit.ratings)) {
        habit.ratings.forEach((rating) => {
          totalRatings += rating.value;
          numberOfRatings++;
        });
      }
    });

    var averageRating =
      numberOfRatings > 0 ? totalRatings / numberOfRatings : "N/A";
    $("#averageRating").text(
      averageRating !== "N/A" ? averageRating.toFixed(2) + "/5" : averageRating
    );

    var milestonesReached = habits.reduce(
      (acc, habit) => acc + (habit.milestonesReached || 0),
      0
    );
    $("#milestonesReached").text(`${milestonesReached} Milestones Reached`);

    this.displayHabits(habits);
  },


  // Function to dynamically display habit cards on the dashboard
  displayHabits: function (habits) {
    var habitCardsHtml = habits
      .map((habit) => {
        var progressToMilestone = (
          (habit.currentMilestone / habit.milestone) *
          100
        ).toFixed(2);
        return `
        <div class="col-md-4">
          <div class="card habit-card">
            <div class="card-body">
              <h5 class="card-title">${habit.title}</h5>
              <p class="card-text">${habit.description}</p>
              <p class="card-text">Total Progress: ${habit.totalProgress} ${habit.unit} ${habit.verb}</p>
              <div class="progress mb-2">
                <div class="progress-bar" role="progressbar" style="width: ${progressToMilestone}%" aria-valuenow="${progressToMilestone}" aria-valuemin="0" aria-valuemax="100"></div>
              </div>
              <p class="card-text">Current Milestone: ${habit.currentMilestone} out of ${habit.milestone} ${habit.unit}</p>
              <div class="button-container d-flex align-items-center justify-content-between">
                <a href="#" class="btn btn-accent rate-btn" data-habit-id="${habit.id}">Rate</a>
                <a href="#" class="btn btn-success add-btn" data-habit-id="${habit.id}">Add</a>
              </div>
            </div>
          </div>
        </div>
      `;
      })
      .join("");
    $("#habitsOverview").html(habitCardsHtml);
  },

  updateRatingsDashboard: function () {
    RestClient.get(
      "/../../backend/rest/get_ratings.php",
      (response) => {
        console.log("Ratings data retrieved successfully", response);
        var data = JSON.parse(response);
        if (data.status === "success") {
          this.drawRatingsChart(data.data); // Proceed to draw the chart
        } else {
          console.error(data.message);
          toastr.error(data.message);
        }
      },
      (error) => {
        console.error("Failed to retrieve ratings data", error);
        toastr.error("Failed to retrieve ratings data: " + error.responseText);
      }
    );
  },

  drawRatingsChart: function (ratings) {
    var ctx = document.getElementById("habitTrendsChart").getContext("2d");
    var labels = ratings.map((rating) =>
      new Date(rating.date).toLocaleDateString()
    );
    var data = ratings.map((rating) => rating.value);

    new Chart(ctx, {
      type: "line",
      data: {
        labels: labels,
        datasets: [
          {
            label: "Ratings Over Time",
            data: data,
            fill: false,
            borderColor: "rgb(75, 192, 192)",
            tension: 0.1,
          },
        ],
      },
      options: {
        maintainAspectRatio: false,
        scales: {
          y: {
            beginAtZero: true,
          },
        },
      },
    });
  },

  
  getHabitsForUser: function () {
    RestClient.get(
      "/../../backend/rest/get_habits.php",
      (response) => {
        console.log("Habits data retrieved successfully", response);
        var data = JSON.parse(response);
        if (data.status === "success") {
          this.displayHabitsOnHabitsPage(data.data); // Function to display the habits on the Habits page
        } else {
          console.error(data.message);
          toastr.error(data.message);
        }
      },
      (error) => {
        console.error("Failed to retrieve habits data", error);
        toastr.error("Failed to retrieve habits data: " + error.responseText);
      }
    );
  },

  // Function to actually display the habits on the Habits page
  displayHabitsOnHabitsPage: function (habitsData) {
    // Empty the container first
    $("#habitCardsContainer").empty();

    // Iterate over the habits data and create the HTML for each habit card
    habitsData.forEach(function (habit) {
      var habitCardHtml = `
        <div class="col-md-4">
          <div class="card habit-card">
            <div class="card-body">
              <h5 class="card-title">${habit.title}</h5>
              <p class="card-text">${habit.description}</p>
              <p class="card-text"><strong>Unit:</strong> ${habit.unit}</p>
              <p class="card-text"><strong>Verb:</strong> ${habit.verb}</p>
              <p class="card-text"><strong>Increment:</strong> ${habit.increment}</p>
              <p class="card-text"><strong>Milestone:</strong> ${habit.milestone}</p>
              <div class="button-container d-flex justify-content-between">
                <a href="#" class="btn btn-warning update-btn" data-habit-id="${habit.id}">Update</a>
                <a href="#" class="btn btn-danger delete-btn" data-habit-id="${habit.id}">Delete</a>
              </div>
            </div>
          </div>
        </div>
      `;

      // Append the habit card to the container
      $("#habitCardsContainer").append(habitCardHtml);
    });
  },

  createHabit: function () {
    FormValidation.validate(
      "#createHabitForm",
      {
        habitTitle: {
          required: true,
          minlength: 3,
          maxlength: 25,
        },
        habitDescription: {
          required: true,
          minlength: 10,
          maxlength: 60, 
        },
        habitUnit: {
          required: true,
          maxlength: 20, 
        },
        habitVerb: {
          required: true,
          maxlength: 15, 
        },
        habitIncrement: {
          required: true,
          number: true,
          min: 1,
          max: 1000000, 
        },
        habitMilestone: {
          required: true,
          number: true,
          min: 1,
          max: 1000000, 
        },
      },
      function (formData) {
        RestClient.post(
          "/../../backend/rest/create_habit.php",
          formData,
          function (response) {
            console.log("Habit created successfully", response);
            toastr.success("Habit created successfully!");
            $("#createHabitForm")[0].reset();
            HabitService.updateHabitDashboard();
            HabitService.getHabitsForUser();
          },
          function (error) {
            console.error("Failed to create habit", error);
            toastr.error("Failed to create habit: " + error.responseText);
          }
        );
      }
    );
  },
};

$(document).ready(function () {
//   HabitService.updateHabitDashboard(); // Display the habit dashboard on load
//   HabitService.updateRatingsDashboard(); // Fetch and display ratings on load
//   HabitService.getHabitsForUser();
//   HabitService.createHabit();
});
