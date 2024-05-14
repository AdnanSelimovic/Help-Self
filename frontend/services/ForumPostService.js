var ForumPostService = {
  userId: 10, 

  getForumPosts: function () {
    const sortOrder = $('input[name="sortOptions"]:checked').val();
    let orderColumn = "date_posted";
    let orderDirection = "DESC";

    switch (sortOrder) {
      case "latest":
        orderColumn = "date_posted";
        orderDirection = "DESC";
        break;
      case "oldest":
        orderColumn = "date_posted";
        orderDirection = "ASC";
        break;
      case "mostLiked":
        orderColumn = "likes";
        orderDirection = "DESC";
        break;
      case "leastLiked":
        orderColumn = "likes";
        orderDirection = "ASC";
        break;
    }

    RestClient.post(
      "/../../backend/rest/get_forum_posts_sorted.php",
      { order_column: orderColumn, order_direction: orderDirection },
      function (response) {
        console.log("Forum posts retrieved successfully", response);
        var result = JSON.parse(response);
        if (result.status === "success" && Array.isArray(result.data)) {
          ForumPostService.displayForumPosts(result.data);
        } else {
          console.error("Error: ", result.message || "Unknown error");
          toastr.error(result.message || "Failed to load posts");
        }
      },
      function (error) {
        console.error("Failed to retrieve forum posts", error);
        toastr.error("Failed to retrieve forum posts: " + error.responseText);
      }
    );
  },

  displayForumPosts: function (posts) {
    var postsHtml = posts.map((post) => {
      return `
        <div class="card mb-3">
          <div class="card-body">
            <h5 class="card-title">${post.title}</h5>
            <h6 class="card-subtitle mb-2 text-muted">Posted on: ${new Date(post.date_posted).toLocaleDateString()}</h6>
            <p class="card-text">${post.content}</p>
            <a href="#" class="card-link">Comments</a>
            <a href="#" class="card-link">Like</a>
          </div>
        </div>
      `;
    }).join("");
    $("#forum-container").html(postsHtml);
  },
  

  setupForumEventHandlers: function () {
    var self = this;
    $(document).on("change", 'input[name="sortOptions"]', function () {
      self.getForumPosts(); 
    });
    $(document).on("keyup", "#searchPosts", function () {
      self.getForumPosts(); 
    });
  },
};
