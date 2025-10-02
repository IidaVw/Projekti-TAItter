// Button animations
document.querySelectorAll('.action').forEach(action => {
  action.addEventListener('click', function(e) {
    e.preventDefault();
    this.style.color = 'var(--accent-primary)';
    this.style.transform = 'scale(1.1)';
    setTimeout(() => {
      this.style.color = '';
      this.style.transform = '';
    }, 200);
  });
});

document.querySelectorAll('.btn-primary, .btn-secondary, .login-btn').forEach(btn => {
  btn.addEventListener('mouseenter', function() {
    this.style.transform = 'translateY(-2px)';
  });
  btn.addEventListener('mouseleave', function() {
    this.style.transform = '';
  });
});

// --- Comments System ---
const commentsData = {
  1: [
    { user: "demo_user", text: "Welcome to TAItter! 🎉" },
    { user: "taitter_ai", text: "Glad to have you here 🚀" }
  ],
  2: [
    { user: "autsku", text: "That feature is amazing 🔥" },
    { user: "ai_fan", text: "AI knows me better than I do 😂" }
  ],
  3: [
    { user: "test_user", text: "Wow, love the new dashboard!" }
  ]
};

document.querySelectorAll(".comment-btn").forEach(btn => {
  btn.addEventListener("click", e => {
    const post = e.target.closest(".post-section");
    const postId = post.dataset.id;
    const commentSection = document.querySelector(".comment-section");

    // Reset sidebar
    commentSection.innerHTML = `
      <div class="comment-list"></div>
      <form class="comment-form">
        <input type="text" placeholder="Write a comment..." required />
        <button type="submit">Reply</button>
      </form>
    `;

    const commentList = commentSection.querySelector(".comment-list");

    // Load existing comments
    if (commentsData[postId]) {
      commentsData[postId].forEach(c => {
        const div = document.createElement("div");
        div.classList.add("comment");
        div.innerHTML = `<strong>@${c.user}</strong>: ${c.text}`;
        commentList.appendChild(div);
      });
    }

    // Handle new comment
    const form = commentSection.querySelector(".comment-form");
    form.addEventListener("submit", ev => {
      ev.preventDefault();
      const input = form.querySelector("input");
      const newComment = input.value.trim();
      if (newComment) {
        const div = document.createElement("div");
        div.classList.add("comment");
        div.innerHTML = `<strong>@you</strong>: ${newComment}`;
        commentList.appendChild(div);
        input.value = "";
        commentList.scrollTop = commentList.scrollHeight; // auto scroll
      }
    });
  });
});
