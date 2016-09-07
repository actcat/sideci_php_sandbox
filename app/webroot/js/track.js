$("a.github_auth").click(function () {
  ga('send', 'event', 'button', 'click', {'page': '/users/auth/github'})
  ga('send', 'event', 'button', 'click', {'page': '/users/auth/github'})
});

mixpanel.track_links("a.github_auth", "Click GitHub Auth");
