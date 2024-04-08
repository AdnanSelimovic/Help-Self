$(document).on("submit", "#loginForm", function (event) {
  event.preventDefault();

  var email = $("#email").val();
  var password = $("#password").val();

  $.ajax({
    url: "assets/json/users.json",
    dataType: "json",
    success: function (users) {
      var user = users.find(
        (user) => user.email === email && user.password === password
      );
      if (user) {
        console.log("Login successful");
        window.location.href = "#home";
        $("#loginForm")[0].reset();
      } else {
        console.log("Login failed. Invalid email or password.");
        alert("Invalid email or password. Please try again.");
      }
    },
    error: function () {
      console.log("Error fetching user data");
      alert("Error logging in. Please try again later.");
    },
  });
});
function updateDashboardUI() {
  console.log("updateUserDashboard is called");
  $.ajax({
    url: "assets/json/dashboardData.json",
    dataType: "json",
    success: function (data) {
      $("#dashboardText").text(data.dashboardTitle);
    },
    error: function () {
      console.log("Error fetching dashboard data.");
    },
  });
}
function getRandomColor() {
  var letters = "0123456789ABCDEF";
  var color = "#";
  for (var i = 0; i < 6; i++) {
    color += letters[Math.floor(Math.random() * 16)];
  }
  return color;
}
var username = "user1";
function updateUserDashboard() {
  var currentUser = username;

  $.ajax({
    url: "assets/json/users.json",
    dataType: "json",
    success: function (users) {
      var userData = users.find((user) => user.username === currentUser);
      if (!userData) {
        console.log("User not found.");
        return;
      }
      var totalHabits = userData.habits.length;
      var totalRatings = 0;
      var numberOfRatings = 0;
      userData.habits.forEach((habit) => {
        habit.ratings.forEach((rating) => {
          if (typeof rating.value === "number") {
            totalRatings += rating.value;
            numberOfRatings++;
          }
        });
      });
      var averageRating =
        numberOfRatings > 0 ? totalRatings / numberOfRatings : "N/A";

      var milestonesReached = userData.habits.reduce(
        (acc, habit) => acc + habit.milestonesReached,
        0
      );
      $("#totalHabits").text(`${totalHabits} Habits`);
      $("#averageRating").text(
        averageRating !== "N/A"
          ? averageRating.toFixed(2) + "/5"
          : averageRating
      );
      $("#milestonesReached").text(`${milestonesReached} Milestones`);
      var habitCardsHtml = userData.habits
        .map((habit) => {
          var progressToMilestone =
            (habit.currentMilestone / habit.milestone) * 100;
          return `
          <div class="col-md-4">
            <div class="card habit-card">
              <div class="card-body">
                <h5 class="card-title">${habit.title}</h5>
                <p class="card-text">Total Progress: ${habit.totalProgress} ${habit.unit} ${habit.verb}</p>
                <p class="card-text">Current Milestone: ${habit.currentMilestone} out of ${habit.milestone} ${habit.unit}</p>
                <div class="progress mb-2">
                  <div class="progress-bar" role="progressbar" style="width: ${progressToMilestone}%" aria-valuenow="${progressToMilestone}" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <p class="card-text">Daily Rating: ${habit.dailyRating}/5</p>
                <div class="button-container d-flex align-items-center justify-content-between">
                <a href="#" class="btn btn-accent rate-btn" data-habit-id="${habit.id}">Rate</a>
                <a href="#" class="btn btn-secondary trend-btn" data-habit-id="${habit.id}">Trend</a>
                <a href="#" class="btn btn-success add-btn" data-habit-id="${habit.id}">Add</a>
                </div>
              </div>
            </div>
          </div>
        `;
        })
        .join("");
      $("#habitsOverview").html(habitCardsHtml);
      var datasets = userData.habits.map((habit) => {
        return {
          label: habit.title,
          data: habit.ratings.map((rating) => rating.value),
          fill: false,
          borderColor: getRandomColor(),
          lineTension: 0.1,
        };
      });
      var labels = userData.habits[0].ratings.map((rating) => rating.date);
      var ctx = document.getElementById("habitTrendsChart").getContext("2d");
      var habitTrendsChart = new Chart(ctx, {
        type: "line",
        data: {
          labels: labels,
          datasets: datasets,
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          title: {
            display: true,
            text: "Habit Trends",
          },
          scales: {
            xAxes: [
              {
                display: true,
                scaleLabel: {
                  display: true,
                  labelString: "Date",
                },
              },
            ],
            yAxes: [
              {
                display: true,
                scaleLabel: {
                  display: true,
                  labelString: "Rating",
                },
              },
            ],
          },
        },
      });
    },
    error: function () {
      console.log("Error fetching user data.");
    },
  });
}

function updateHabitsPage() {
  var currentUser = username;

  $.ajax({
    url: "assets/json/users.json",
    dataType: "json",
    success: function (users) {
      var userData = users.find((user) => user.username === currentUser);
      if (!userData) {
        console.log("User not found.");
        return;
      }
      $("#habitCardsContainer").empty();
      userData.habits.forEach(function (habit) {
        var habitCardHtml = `
          <div class="col-md-4">
              <div class="card habit-card">
                  <div class="card-body">
                      <h5 class="card-title">${habit.title}</h5>
                      <p class="card-text">${habit.description}</p>
                      <p class="card-text"><strong>Unit:</strong> ${
                        habit.unit
                      }</p>
                      <p class="card-text"><strong>Verb:</strong> ${
                        habit.verb
                      }</p>
                      <p class="card-text"><strong>Increment:</strong> ${
                        habit.increment
                      } ${habit.unit}</p>
                      <p class="card-text"><strong>Milestone:</strong> ${
                        habit.milestone
                      } ${habit.unit}</p>
                      <p class="card-text"><strong>Creation Date:</strong> ${
                        habit.creationDate || "Not specified"
                      }</p>
                      <div class="button-container d-flex justify-content-between">
                          <a href="#" class="btn btn-warning update-btn" data-habit-id="${
                            habit.id
                          }">Update</a>
                          <a href="#" class="btn btn-action pause-btn" data-habit-id="${
                            habit.id
                          }">${habit.paused ? "Resume" : "Pause"}</a>
                          <a href="#" class="btn btn-danger delete-btn" data-habit-id="${
                            habit.id
                          }">Delete</a>
                      </div>
                  </div>
              </div>
          </div>
        `;
        $("#habitCardsContainer").append(habitCardHtml);
      });
    },
    error: function () {
      console.log("Error fetching user data.");
    },
  });
}
function loadForumPosts() {
  $.ajax({
    url: "assets/json/forum.json",
    dataType: "json",
    success: function (data) {
      let postsHtml = "";
      data.posts.forEach((post) => {
        postsHtml += `
          <div class="forum-post mb-3 p-3 border rounded">
            <div class="post-meta mb-2">
              <strong>${post.author}</strong> posted on <em>${post.datePosted}</em>
            </div>
            <div class="post-title mb-2">
              <h5>${post.title}</h5>
            </div>
            <div class="post-content mb-3">
              <p>${post.content}</p>
            </div>
            <div class="post-footer d-flex justify-content-between text-muted">
              <span>${post.views} Views</span>
              <span>${post.likes} Likes</span>
              <span>${post.comments} Comments</span>
            </div>
          </div>
        `;
      });
      $("#forum-container").html(postsHtml);
    },
    error: function () {
      console.log("Error loading forum posts.");
    },
  });
}
function loadUserProfile() {
  var currentUser = username;
  $.ajax({
    url: "assets/json/users.json",
    dataType: "json",
    success: function (users) {
      var user = users.find((u) => u.username === currentUser);
      if (!user) {
        console.error("User not found.");
        return;
      }
      $("#fullName").text(`${user.firstName} ${user.lastName}`);
      $("#username").text(`@${user.username}`);
      $("#description").text(user.biography);
      $("#profile-email").html(`<strong>Email:</strong> ${user.email}`);
      $("#profile-location").html(
        `<strong>Location:</strong> ${user.location}`
      );
      $("#profile-joined").html(`<strong>Joined:</strong> ${joinedDate}`);
      $(".recent-activity").text("No recent activity yet");
    },
    error: function (xhr, status, error) {
      console.error("Failed to load user profile:", error);
    },
  });
}

