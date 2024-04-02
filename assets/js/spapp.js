$(document).ready(function () {
  // Initialize SPApp
  var app = $.spapp({
    defaultView: "landing",
    templateDir: "assets/views/",
  });

  function showActiveSection(activeSectionId) {
    $("#spapp > section").hide();
    $(activeSectionId).show();
  }

  function updateNavLinks(activeView) {
    $(".navbar-nav .nav-link")
      .removeClass("active")
      .removeAttr("aria-disabled")
      .removeClass("disabled");

    if (activeView === "home") {
      $('.navbar-nav .nav-link[href="#home"]').addClass("active");
    } else if (activeView === "habits") {
      $('.navbar-nav .nav-link[href="#habits"]').addClass("active");
    } else if (activeView === "forum") {
      $('.navbar-nav .nav-link[href="#forum"]').addClass("active");
    } else if (activeView === "landing") {
      $(".navbar-nav .nav-link")
        .addClass("disabled")
        .attr("aria-disabled", "true");
    } else if (activeView === "login") {
      $(".navbar-nav .nav-link")
        .addClass("disabled")
        .attr("aria-disabled", "true");
    } else if (activeView === "registration") {
      $(".navbar-nav .nav-link")
        .addClass("disabled")
        .attr("aria-disabled", "true");
    } else if (activeView === "info") {
      $(".navbar-nav .nav-link")
        .addClass("disabled")
        .attr("aria-disabled", "true");
    } else if (activeView === "edit") {
      $(".navbar-nav .nav-link")
        .addClass("disabled")
        .attr("aria-disabled", "true");
    } else {
      $('.navbar-nav .nav-link[href="#' + activeView + '"]')
        .removeClass("active")
        .removeAttr("aria-disabled")
        .removeClass("disabled");
    }
  }

  app.route({
    view: "landing",
    load: "landing.html",
    onReady: function () {
      showActiveSection("#landing");
      updateNavLinks("landing");
    },
  });

  app.route({
    view: "test",
    load: "test.html",
    onReady: function () {
      showActiveSection("#test");
      updateNavLinks("test");
    },
  });

  app.route({
    view: "registration",
    load: "registration.html",
    onReady: function () {
      showActiveSection("#registration");
      updateNavLinks("registration");
    },
  });

  app.route({
    view: "login",
    load: "login.html",
    onReady: function () {
      showActiveSection("#login");
      updateNavLinks("login");
    },
  });

  app.route({
    view: "home",
    load: "home.html",
    onReady: function () {
      showActiveSection("#home");
      updateNavLinks("home");
      updateUserDashboard();
    },
  });

  app.route({
    view: "info",
    load: "info.html",
    onReady: function () {
      showActiveSection("#info");
      updateNavLinks("info");
    },
  });

  app.route({
    view: "habits",
    load: "habits.html",
    onReady: function () {
      showActiveSection("#habits");
      updateNavLinks("habits");
      updateHabitsPage();
    },
  });

  app.route({
    view: "forum",
    load: "forum.html",
    onReady: function () {
      showActiveSection("#forum");
      updateNavLinks("forum");
      loadForumPosts();
    },
  });

  app.route({
    view: "edit",
    load: "edit.html",
    onReady: function () {
      showActiveSection("#edit");
    },
  });

  app.route({
    view: "profile",
    load: "profile.html",
    onReady: function () {
      showActiveSection("#profile");
      updateNavLinks("profile");
      loadUserProfile()
    },
  });

  app.route({
    view: "settings",
    load: "settings.html",
    onReady: function () {
      showActiveSection("#settings");
      updateNavLinks("settings");
    },
  });
  
  app.run();
});
