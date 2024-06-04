var HabitService = {
  updateHabitDashboard: function () {
    var currentUser = UserService.getCurrentUser();
    if (currentUser) {
      var userId = currentUser.id;
      RestClient.get(
        "get_habits?user_id=" + userId,
        (response) => {
          console.log("Dashboard data retrieved successfully", response);
          var data;
          if (typeof response === "string") {
            data = JSON.parse(response);
          } else {
            data = response;
          }
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
    } else {
      toastr.error("User is not logged in.");
    }
  },

  displayDashboardData: function (data) {
    var habits = Array.isArray(data) ? data : [data];

    console.log("Habits to display:", habits); // Add log here

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

    this.fetchAndDisplayRatings();

    var milestonesReached = habits.reduce(
      (acc, habit) => acc + (habit.milestonesReached || 0),
      0
    );
    $("#milestonesReached").text(`${milestonesReached} Milestones Reached`);

    this.displayHabits(habits);
  },

  fetchAndDisplayRatings: function () {
    var currentUser = UserService.getCurrentUser();
    if (currentUser) {
      var userId = currentUser.id;
      RestClient.get(
        "get_all_user_ratings?user_id=" + userId,
        (response) => {
          var data;
          if (typeof response === "string") {
            data = JSON.parse(response);
          } else {
            data = response;
          }
          console.log("Fetched ratings data: ", data);
          if (data.status === "success") {
            var averageRating = "N/A";
            if (!isNaN(data.average)) {
              averageRating = parseFloat(data.average).toFixed(2);
            }
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
    } else {
      toastr.error("User is not logged in.");
    }
  },

  displayHabits: function (habits) {
    console.log("Displaying habits: ", habits);
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
    var currentUser = UserService.getCurrentUser();
    if (currentUser) {
      var userId = currentUser.id;
      RestClient.get(
        "get_ratings?user_id=" + userId,
        (response) => {
          console.log("Ratings data retrieved successfully", response);
          var data;
          if (typeof response === "string") {
            data = JSON.parse(response);
          } else {
            data = response;
          }
          if (data.status === "success") {
            // Clear the existing chart before redrawing
            if (this.ratingsChart) {
              this.ratingsChart.destroy();
            }
            this.drawRatingsChart(data.data);
          } else {
            console.error(data.message);
            toastr.error(data.message);
          }
        },
        (error) => {
          console.error("Failed to retrieve ratings data", error);
          toastr.error(
            "Failed to retrieve ratings data: " + error.responseText
          );
        }
      );
    } else {
      toastr.error("User is not logged in.");
    }
  },

  drawRatingsChart: function (ratings) {
    var ctx = document.getElementById("habitTrendsChart").getContext("2d");
    var labels = ratings.map((rating) =>
      new Date(rating.date).toLocaleDateString()
    );
    var data = ratings.map((rating) => rating.value);

    this.ratingsChart = new Chart(ctx, {
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
    var currentUser = UserService.getCurrentUser();
    if (currentUser) {
      var userId = currentUser.id;
      RestClient.get(
        "get_habits?user_id=" + userId,
        (response) => {
          console.log("Habits data retrieved successfully", response);
          var data;
          if (typeof response === "string") {
            data = JSON.parse(response);
          } else {
            data = response;
          }
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
    } else {
      toastr.error("User is not logged in.");
    }
  },

  displayHabitsOnHabitsPage: function (habitsData) {
    console.log("Displaying habits on habits page: ", habitsData);
    $("#habitCardsContainer").empty();

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

      $("#habitCardsContainer").append(habitCardHtml);
    });
  },

  createHabit: function () {
    var currentUser = UserService.getCurrentUser();
    if (currentUser) {
      var userId = currentUser.id;
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
          formData.user_id = userId; // Include the user_id in the form data
          RestClient.post(
            "create_habit",
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
    } else {
      toastr.error("User is not logged in.");
    }
  },

  submitRating: function () {
    var rating = $("#habitRatingSlider").val();
    if (!rating) {
      toastr.error("Please select a rating.");
      return;
    }
    RestClient.post(
      "rate_habit",
      { habitId: this.currentHabitId, rating: rating },
      (response) => {
        var data;
        if (typeof response === "string") {
          data = JSON.parse(response);
        } else {
          data = response;
        }
        if (data.status === "success") {
          toastr.success("Rating submitted successfully!");
          $("#ratingModal").modal("hide");
          this.updateHabitDashboard();
          this.updateRatingsDashboard(); // Ensure the graph is updated
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
      "update_habit_progress",
      { habitId: habitId },
      (response) => {
        var data =
          typeof response === "string" ? JSON.parse(response) : response;
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
      "delete_habit?habitId=" + habitId,
      null, // No need for data in the request body
      (response) => {
        console.log("Response:", response);
        var data;
        if (typeof response === "string") {
          try {
            data = JSON.parse(response);
          } catch (e) {
            toastr.error("Failed to process the server response: " + e.message);
            return;
          }
        } else {
          data = response;
        }
        console.log("Parsed data:", data);
        if (data.status === "success") {
          toastr.success("Habit deleted successfully!");
          // Remove the habit from the page
          $(`[data-habit-id="${habitId}"]`).closest(".col-md-4").remove();
        } else {
          toastr.error("Failed to delete habit: " + data.message);
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
      "update_habit_details",
      formData,
      (response) => {
        var data;
        if (typeof response === "string") {
          data = JSON.parse(response);
        } else {
          data = response;
        }
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

  // submitForumPost: function () {
  //   FormValidation.validate(
  //     "#shareHabitForm",
  //     {
  //       title: {
  //         required: true,
  //         minlength: 3,
  //         maxlength: 255,
  //       },
  //       content: {
  //         required: true,
  //         minlength: 10,
  //       },
  //     },
  //     function (formData) {
  //       RestClient.post(
  //         "create_forum_post",
  //         formData,
  //         function (response) {
  //           var data = JSON.parse(response);
  //           if (data.status === "success") {
  //             toastr.success("Post submitted successfully!");
  //             $("#shareHabitModal").modal("hide");
  //           } else {
  //             toastr.error("Failed to submit post: " + data.message);
  //           }
  //         },
  //         function (error) {
  //           toastr.error("Failed to submit post: " + error.responseText);
  //         }
  //       );
  //     }
  //   );
  // },

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
        var currentUser = UserService.getCurrentUser();
        if (currentUser) {
          formData.user_id = currentUser.id; // Add user_id to form data
          RestClient.post(
            "create_forum_post",
            formData,
            function (response) {
              var data;
              if (typeof response === "string") {
                try {
                  data = JSON.parse(response);
                } catch (e) {
                  toastr.error(
                    "Failed to process server response: " + e.message
                  );
                  return;
                }
              } else {
                data = response;
              }
              if (data.status === "success") {
                toastr.success("Post submitted successfully!");
                $("#shareHabitModal").modal("hide");
                ForumPostService.getForumPosts(); // Refresh forum posts after submission
              } else {
                toastr.error("Failed to submit post: " + data.message);
              }
            },
            function (error) {
              toastr.error("Failed to submit post: " + error.responseText);
            }
          );
        } else {
          toastr.error("User is not logged in.");
        }
      }
    );
  },

  setupEventHandlers: function () {
    var self = this;

    // Event handler for the rate button
    $(document).on("click", ".rate-btn", function (event) {
      event.preventDefault();
      self.currentHabitId = $(this).data("habit-id");
      $("#ratingModal").modal("show");
    });

    // Event handler for the add button
    var isRateLimited = false;

    $(document).on("click", ".add-btn", function (event) {
      event.preventDefault();

      if (isRateLimited) {
        return;
      }

      isRateLimited = true;

      setTimeout(function () {
        isRateLimited = false;
      }, 1000); // 1 second rate limit

      var habitId = $(this).data("habit-id");
      self.updateHabitProgress(habitId);
    });

    // Event handler for the delete button
    $(document).on("click", ".delete-btn", function (event) {
      event.preventDefault();
      var habitId = $(this).data("habit-id");
      self.deleteHabit(habitId);
    });

    // Event handler for the update button
    $(document).on("click", ".update-btn", function (event) {
      event.preventDefault();
      var habitId = $(this).data("habit-id");
      $("#updateHabitId").val(habitId);
      $("#updateHabitModal").modal("show");
    });

    // Event handler for the share button
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

    // Event handler for the share habit form submission
    $(document).on("submit", "#shareHabitForm", function (event) {
      event.preventDefault();
      self.submitForumPost();
    });

    // Event handler for the update habit form submission
    $(document).on("submit", "#updateHabitForm", function (event) {
      event.preventDefault();
      self.updateHabitDetails();
    });
  },

  loadUserProfile: function () {
    var currentUser = UserService.getCurrentUser();
    if (currentUser) {
      var userId = currentUser.id;
      RestClient.get(
        "get_user_profile?user_id=" + userId,
        (response) => {
          console.log("Raw response:", response); // Log the raw response
          try {
            if (typeof response === "string") {
              var data = JSON.parse(response);
            } else {
              var data = response;
            }
            console.log("Parsed data:", data); // Log the parsed data
            if (data.status === "success") {
              $("#fullName").text(data.data.full_name);
              $("#username").text("@" + data.data.username);
              $("#description").text(
                data.data.biography || "No biography provided."
              );
              $("#profile-email").text("Email: " + data.data.email);
              var joinedDate = new Date(data.data.created_at);
              var formattedJoinDate = isNaN(joinedDate.getTime())
                ? "Invalid date"
                : joinedDate.toLocaleDateString();
              $("#profile-joined").text("Joined: " + formattedJoinDate);
              $("#profile-location").text(
                "Location: " + (data.data.location || "No location provided.")
              );
            } else {
              toastr.error("Failed to load profile: " + data.message);
            }
          } catch (e) {
            console.error(
              "Failed to parse response:",
              e,
              "Response text:",
              response
            ); // Improved error logging
            toastr.error(
              "Failed to load profile due to invalid server response."
            );
          }
        },
        (error) => {
          toastr.error("Error loading profile");
          console.error("Failed to load profile data:", error);
        },
        "json" // Specify that we expect JSON response
      );
    } else {
      toastr.error("User is not logged in.");
    }
  },

  updateUserProfile: function (formData) {
    var currentUser = UserService.getCurrentUser();
    if (currentUser) {
      formData.user_id = currentUser.id; // Add user_id to form data

      RestClient.post(
        "update_user_profile",
        formData,
        function (response) {
          var data;
          if (typeof response === "string") {
            try {
              data = JSON.parse(response);
            } catch (e) {
              toastr.error(
                "Failed to process the server response: " + e.message
              );
              return;
            }
          } else {
            data = response;
          }
          if (data.status === "success") {
            toastr.success("Profile updated successfully!");
            HabitService.loadUserProfile();
          } else {
            toastr.error("Failed to update profile: " + data.message);
          }
        },
        function (error) {
          toastr.error("Error updating profile");
          console.error("Failed to update profile:", error);
        }
      );
    } else {
      toastr.error("User is not logged in.");
    }
  },

  setupProfileEventHandlers: function () {
    var self = this;

    $(document).on("submit", "#editProfileForm", function (event) {
      event.preventDefault();
      var formData = {
        // first_name: $("#firstName").val(),
        // last_name: $("#lastName").val(),
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
