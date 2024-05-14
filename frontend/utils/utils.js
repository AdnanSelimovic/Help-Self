// https://startbootstrap.com/template/sb-admin
const Utils = {
  init_spapp: function () {
    var app = $.spapp({
      templateDir: "/Help-Self/frontend/pages/",
      defaultView: "landing",
    });

    app.route({
      view: 'login',
      onReady: function () {
        UserService.login();
      },
    });

    app.route({
      view: 'registration',
      onReady: function () {
        UserService.register();
      },
    });

    app.route({
      view: 'habits',
      onReady: function () {
        HabitService.getHabitsForUser();
        HabitService.createHabit();
        HabitService.setupEventHandlers();
      },
    });

    app.route({
      view: 'home',
      onReady: function () {
        HabitService.updateHabitDashboard(); 
        HabitService.updateRatingsDashboard();
        HabitService.setupEventHandlers();
      },
    });

    app.route({
      view: 'forum',
      onReady: function() {
        ForumPostService.setupForumEventHandlers();
        ForumPostService.getForumPosts();
      },
    });

    app.route({
      view: 'profile',
      onReady: function() {
        // ProfileService.loadUserProfile();
        // ProfileService.setupProfileEventHandlers();
        HabitService.loadUserProfile();
        HabitService.setupProfileEventHandlers();
      },
    });
    app.run();
    this.handleNavigationUpdates();
  },

  handleNavigationUpdates: function () {
    $(window).on("hashchange", function () {
      var activeView = location.hash.replace("#", "");
      Utils.updateNavLinks(activeView);
    });
    var initialView = window.location.hash.replace("#", "") || "landing";
    this.updateNavLinks(initialView);
  },
  updateNavLinks: function (activeView) {
    $(".navbar-nav .nav-link")
      .removeClass("active disabled")
      .attr("aria-disabled", false);

    if (
      ["landing", "login", "registration", "info", "edit"].includes(activeView)
    ) {
      $(
        '.navbar-nav .nav-link[href="#home"], .navbar-nav .nav-link[href="#habits"], .navbar-nav .nav-link[href="#forum"]'
      )
        .addClass("disabled")
        .attr("aria-disabled", "true");
      $("#navbarDropdown")
        .addClass("disabled")
        .attr("aria-disabled", "true")
        .parent()
        .addClass("disabled");

      $('.navbar-nav .nav-link[href="#' + activeView + '"]')
        .addClass("disabled")
        .attr("aria-disabled", "true");
    } else {
    
      $(".navbar-nav .nav-link")
        .removeClass("disabled")
        .attr("aria-disabled", "false")
        .parent()
        .removeClass("disabled");
      $('.navbar-nav .nav-link[href="#' + activeView + '"]').addClass("active");

      
      $("#navbarDropdown")
        .removeClass("disabled")
        .attr("aria-disabled", "false")
        .parent()
        .removeClass("disabled");
    }
  },

  block_ui: function (element) {
    $(element).block({
      message: '<div class="spinner-border text-primary" role="status"></div>',
      css: {
        backgroundColor: "transparent",
        border: "0",
      },
      overlayCSS: {
        backgroundColor: "#000",
        opacity: 0.25,
      },
    });
  },
  unblock_ui: function (element) {
    $(element).unblock({});
  },
  get_query_param: function (name) {
    var regexS = "[\\?&]" + name + "=([^&#]*)",
      regex = new RegExp(regexS),
      results = regex.exec(window.location.search);
    if (results == null) {
      return "";
    } else {
      return decodeURIComponent(results[1].replace(/\+/g, " "));
    }
  },
  get_datatable: function (
    table_id,
    url,
    columns,
    disable_sort,
    callback,
    details_callback = null,
    sort_column = null,
    sort_order = null,
    draw_callback = null,
    page_length = 10
  ) {
    if ($.fn.dataTable.isDataTable("#" + table_id)) {
      details_callback = false;
      $("#" + table_id)
        .DataTable()
        .destroy();
    }
    var table = $("#" + table_id).DataTable({
      order: [
        sort_column == null ? 2 : sort_column,
        sort_order == null ? "desc" : sort_order,
      ],
      orderClasses: false,
      columns: columns,
      columnDefs: [{ orderable: false, targets: disable_sort }],
      processing: true,
      serverSide: true,
      ajax: {
        url: url,
        type: "GET",
      },
      lengthMenu: [
        [5, 10, 15, 50, 100, 200, 500, 5000],
        [5, 10, 15, 50, 100, 200, 500, "ALL"],
      ],
      pageLength: page_length,
      initComplete: function () {
        if (callback) callback();
      },
      drawCallback: function (settings) {
        if (draw_callback) draw_callback();
      },
    });
  },
};
