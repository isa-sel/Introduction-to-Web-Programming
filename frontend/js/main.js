$(document).ready(function () {
  
  var app = $.spapp({
    defaultView: "home",
    templateDir: "./" // This is the base directory - templates will be loaded relative to this
  });

  app.route({
    view: "home",
    load: "templates/home.html",
    onCreate: function() {
      console.log('Home created!');
    },
    onReady: function() {
      console.log('Home ready!');
    }
  });

  app.route({
    view: "teams",
    load: "templates/teams.html",
    onCreate: function() {
      console.log('Teams created!');
    },
    onReady: function() {
      console.log('Teams ready!');
    }
  });

  app.route({
    view: "players",
    load: "templates/players.html",
    onCreate: function() {
      console.log('Players created!');
    },
    onReady: function() {
      console.log('Players ready!');
    }
  });

  app.route({
    view: "matches",
    load: "templates/matches.html",
    onCreate: function() {
      console.log('Matches created!');
    },
    onReady: function() {
      console.log('Matches ready!');
    }
  });

  app.route({
    view: "venues",
    load: "templates/venues.html",
    onCreate: function() {
      console.log('Venues created!');
    },
    onReady: function() {
      console.log('Venues ready!');
    }
  });

  app.route({
    view: "login",
    load: "templates/login.html",
    onCreate: function() {
      console.log('Login created!');
    },
    onReady: function() {
      console.log('Login ready!');
    }
  });

  app.route({
    view: "register",
    load: "templates/register.html",
    onCreate: function() {
      console.log('Register created!');
    },
    onReady: function() {
      console.log('Register ready!');
    }
  });

  
  app.route({
    view: "standings",
    load: "templates/standings.html",
    onCreate: function() {
      console.log('Standings created!');
    },
    onReady: function() {
      console.log('Standings ready!');
    }
  });

  app.run();

});