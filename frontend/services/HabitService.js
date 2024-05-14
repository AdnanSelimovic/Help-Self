var HabitService = {
  userId: 10, 

  
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

    // var averageRating =
    //   numberOfRatings > 0 ? totalRatings / numberOfRatings : "N/A";
    // $("#averageRating").text(
    //   averageRating !== "N/A" ? averageRating.toFixed(2) + "/5" : averageRating
    // );
    this.fetchAndDisplayRatings();

    var milestonesReached = habits.reduce(
      (acc, habit) => acc + (habit.milestonesReached || 0),
      0
    );
    $("#milestonesReached").text(`${milestonesReached} Milestones Reached`);

    this.displayHabits(habits);
  },

  fetchAndDisplayRatings: function () {
    RestClient.get(
      "/../../backend/rest/get_all_user_ratings.php",
      (response) => {
        var data = JSON.parse(response);
        if (data.status === "success") {
          var totalRatings = 0;
          var numberOfRatings = data.data.length;
          data.data.forEach((rating) => {
            totalRatings += parseInt(rating.value, 10);
          });
          var averageRating =
            numberOfRatings > 0
              ? (totalRatings / numberOfRatings).toFixed(2)
              : "N/A";
          $("#averageRating").text(
            averageRating !== "N/A" ? averageRating + "/5" : "N/A"
          );
        } else {
          console.error(data.message);
          toastr.error(data.message);
        }
      },
      (error) => {
        console.error("Failed to retrieve ratings", error);
        toastr.error("Failed to retrieve ratings: " + error.responseText);
      }
    );
  },

  
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
              <p class="card-text">Milestones Reached: ${habit.milestonesReached}</p>
              <div class="progress mb-2">
                <div class="progress-bar" role="progressbar" style="width: ${progressToMilestone}%" aria-valuenow="${progressToMilestone}" aria-valuemin="0" aria-valuemax="100"></div>
              </div>
              <p class="card-text">Current Milestone: ${habit.currentMilestone} out of ${habit.milestone} ${habit.unit}</p>
              <div class="button-container d-flex align-items-center justify-content-between">
                <a href="#" class="btn btn-accent rate-btn" data-habit-id="${habit.id}">Rate</a>
                <a href="#" class="btn btn-secondary share-btn" data-habit-id="${habit.id}">Share</a>
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

  submitRating: function () {
    var rating = $("#habitRatingSlider").val();
    if (!rating) {
      toastr.error("Please select a rating.");
      return;
    }
    RestClient.post(
      "/../../backend/rest/rate_habit.php",
      { habitId: this.currentHabitId, rating: rating },
      (response) => {
        var data = JSON.parse(response);
        if (data.status === "success") {
          toastr.success("Rating submitted successfully!");
          $("#ratingModal").modal("hide");
          this.updateHabitDashboard();
          this.updateRatingsDashboard();
        } else {
          toastr.error("Failed to submit rating: " + data.message);
        }
      },
      (error) => {
        toastr.error("Failed to submit rating: " + error.responseText);
      }
    );
  },

  updateHabitProgress: function (habitId) {
    RestClient.post(
      "/../../backend/rest/update_habit_progress.php",
      { habitId: habitId },
      (response) => {
        var data = JSON.parse(response);
        if (data.status === "success") {
          toastr.success("Habit updated successfully!");
          this.updateHabitDashboard();
        } else {
          toastr.error(data.message);
        }
      },
      (error) => {
        toastr.error("Error updating habit: " + error.responseText);
      }
    );
  },

  deleteHabit: function (habitId) {
    RestClient.delete(
      "/../../backend/rest/delete_habit.php",
      { habitId: habitId },
      (response) => {
        console.log("Response:", response); 
        try {
          var data = JSON.parse(response);
          console.log("Parsed data:", data);
          if (data.status === "success") {
            toastr.success("Habit deleted successfully!");
            this.getHabitsForUser(); 
          } else {
            toastr.error("Failed to delete habit: " + data.message);
          }
        } catch (e) {
          toastr.error("Failed to process the server response: " + e.message);
        }
      },
      (error) => {
        toastr.error("Failed to delete habit: " + error.responseText);
      }
    );
  },

  updateHabitDetails: function () {
    var formData = $("#updateHabitForm").serialize(); 

    RestClient.post(
      "/../../backend/rest/update_habit_details.php",
      formData,
      (response) => {
        var data = JSON.parse(response);
        if (data.status === "success") {
          toastr.success("Habit details updated successfully!");
          $("#updateHabitModal").modal("hide");
          this.getHabitsForUser();
          this.updateHabitDashboard();
        } else {
          toastr.error("Failed to update habit details: " + data.message);
        }
      },
      (error) => {
        toastr.error("Failed to update habit details: " + error.responseText);
      }
    );
  },

  submitForumPost: function () {
    FormValidation.validate(
      "#shareHabitForm",
      {
        title: {
          required: true,
          minlength: 3,
          maxlength: 255,
        },
        content: {
          required: true,
          minlength: 10,
        },
      },
      function (formData) {
        RestClient.post(
          "/../../backend/rest/create_forum_post.php",
          formData,
          function (response) {
            var data = JSON.parse(response);
            if (data.status === "success") {
              toastr.success("Post submitted successfully!");
              $("#shareHabitModal").modal("hide");
              
            } else {
              toastr.error("Failed to submit post: " + data.message);
            }
          },
          function (error) {
            toastr.error("Failed to submit post: " + error.responseText);
          }
        );
      }
    );
  },

  setupEventHandlers: function () {
    var self = this; 
    $(document).on("click", ".rate-btn", function (event) {
      event.preventDefault();
      self.currentHabitId = $(this).data("habit-id");
      $("#ratingModal").modal("show");
    });

    $(document).on("click", ".add-btn", function (event) {
      event.preventDefault();
      var habitId = $(this).data("habit-id");
      self.updateHabitProgress(habitId);
    });

    $(document).on("click", ".delete-btn", function (event) {
      event.preventDefault();
      var habitId = $(this).data("habit-id");
      
      self.deleteHabit(habitId);
    });

    $(document).on("click", ".update-btn", function (event) {
      event.preventDefault();
      var habitId = $(this).data("habit-id");
      $("#updateHabitId").val(habitId); 
      $("#updateHabitModal").modal("show");
    });

    $(document).on("click", ".share-btn", function (event) {
      event.preventDefault();
      var habitId = $(this).data("habit-id");

      
      var title = $(this).closest(".habit-card").find(".card-title").text();
      var totalProgressText = $(this)
        .closest(".habit-card")
        .find(".card-text")
        .eq(1)
        .text(); 
      var milestonesText = $(this)
        .closest(".habit-card")
        .find(".card-text")
        .eq(3)
        .text(); 

      
      var totalProgress = totalProgressText.split(": ")[1];
      var milestones = milestonesText.split(": ")[1];

      
      var formattedContent = `I just completed ${totalProgress} and reached ${milestones} in my milestone! And here is how I did it:`;

      
      $("#habitPostTitle").val("About My " + title);
      $("#habitPostContent").val(formattedContent);

      
      $("#shareHabitModal").modal("show");
    });

    $(document).on("submit", "#shareHabitForm", function (event) {
      event.preventDefault();
      HabitService.submitForumPost();
    });
  },

  
  loadUserProfile: function () {
    RestClient.get(
      "/../../backend/rest/get_user_profile.php", 
      function (response) {
        var user = JSON.parse(response);
        if (user.status === "success") {
          $("#fullName").text(user.data.first_name + " " + user.data.last_name);
          $("#username").text("@" + user.data.username);
          $("#description").text(user.data.biography);
          $("#profile-email").text("Email: " + user.data.email);
          $("#profile-joined").text(
            "Joined: " + new Date(user.data.joined).toLocaleDateString()
          );
          $("#profile-location").text("Location: " + user.data.location);
        } else {
          toastr.error("Failed to load profile: " + user.message);
        }
      },
      function (error) {
        toastr.error("Error loading profile");
        console.error("Failed to load profile data:", error);
      }
    );
  },

  
  updateUserProfile: function (formData) {
    RestClient.post(
      "/../../backend/rest/update_user_profile.php", 
      formData,
      function (response) {
        var result = JSON.parse(response);
        if (result.status === "success") {
          toastr.success("Profile updated successfully!");
          ProfileService.loadUserProfile(); 
        } else {
          toastr.error("Failed to update profile: " + result.message);
        }
      },
      function (error) {
        toastr.error("Error updating profile");
        console.error("Failed to update profile:", error);
      }
    );
  },

  
  setupProfileEventHandlers: function () {
    var self = this;
    
    $(document).on("submit", "#editProfileForm", function (event) {
      event.preventDefault();
      var formData = {
        first_name: $("#firstName").val(),
        last_name: $("#lastName").val(),
        biography: $("#biography").val(),
        location: $("#location").val(),
      };
      self.updateUserProfile(formData);
    });

    
    $(document).on("click", "#editProfileButton", function () {
      $("#editProfileModal").modal("show");
    });
  },
};
