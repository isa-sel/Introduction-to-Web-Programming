// js/main.js - Full code with admin-only UI, dark theme for admin, and dynamic statistics on home
$(document).ready(function () {

  var app = $.spapp({
      defaultView: "home",
      templateDir: "./"
  });

  // Navigation guard middleware
  const requireAuth = function () {
      if (!AuthService.isAuthenticated()) {
          window.location.href = '#login';
          return false;
      }
      return true;
  };

  const requireAdmin = function () {
      if (!AuthService.isAdmin()) {
          alert('Access denied. Admin privileges required.');
          window.location.href = '#home';
          return false;
      }
      return true;
  };

  // Show/hide admin-only elements + admin theme
  function updateRoleBasedUI() {
      if (AuthService.isAdmin()) {
          $('.admin-only').show();
          $('body').addClass('admin-theme');
      } else {
          $('.admin-only').hide();
          $('body').removeClass('admin-theme');
      }
  }

  // Update navigation based on auth status
  const updateNavigation = function () {
      const isAuth = AuthService.isAuthenticated();
      const user = AuthService.getUser();

      if (isAuth) {
          $('.nav-link[href="#login"], .nav-link[href="#register"]').parent().hide();

          if ($('#userMenu').length === 0) {
              const userMenu = `
                  <li class="nav-item dropdown" id="userMenu">
                      <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" 
                         data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                          <i class="fas fa-user-circle"></i> ${user.username || user.email}
                      </a>
                      <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
                          <a class="dropdown-item" href="#" onclick="AuthService.logout(); return false;">
                              <i class="fas fa-sign-out-alt"></i> Odjava
                          </a>
                      </div>
                  </li>
              `;
              $('.navbar-nav.ml-auto').append(userMenu);
          }
      } else {
          $('.nav-link[href="#login"], .nav-link[href="#register"]').parent().show();
          $('#userMenu').remove();
      }
  };

  // SPA Route definitions
  app.route({
      view: "home",
      load: "templates/home.html",
      onCreate: function () {
          console.log('Home created!');
      },
      onReady: function () {
          console.log('Home ready!');
          updateNavigation();
          updateRoleBasedUI();

          if (AuthService.isAuthenticated()) {
            $('#joinSection').addClass('d-none');
            $('#thankYouSection').removeClass('d-none');
            $('#registerNowBtn').hide();
        } else {
            $('#joinSection').removeClass('d-none');
            $('#thankYouSection').addClass('d-none');
            $('#registerNowBtn').show();
        }
        
          // Dinamički prikaži brojeve iz baze
          fetch('http://localhost:8080/api/statistics', {
              headers: AuthService.isAuthenticated()
                  ? { 'Authorization': 'Bearer ' + AuthService.getToken() }
                  : {}
          })
          .then(response => response.json())
          .then(data => {
              $('#stats-teams').text(data.teams ?? 'N/A');
              $('#stats-matches').text(data.matches ?? 'N/A');
              $('#stats-players').text(data.players ?? 'N/A');
          })
          .catch(() => {
              $('#stats-teams, #stats-matches, #stats-players').text('N/A');
          });
      }
  });

  app.route({
      view: "teams",
      load: "templates/teams.html",
      onCreate: function () {
          console.log('Teams created!');
      },
      onReady: function () {
          console.log('Teams ready!');
          updateNavigation();
          updateRoleBasedUI();
      }
  });

  app.route({
      view: "players",
      load: "templates/players.html",
      onCreate: function () {
          console.log('Players created!');
          if (!requireAuth()) return false;
      },
      onReady: function () {
          console.log('Players ready!');
          updateNavigation();
          updateRoleBasedUI();
      }
  });

  app.route({
      view: "matches",
      load: "templates/matches.html",
      onCreate: function () {
          console.log('Matches created!');
      },
      onReady: function () {
          console.log('Matches ready!');
          updateNavigation();
          updateRoleBasedUI();
      }
  });

  app.route({
      view: "venues",
      load: "templates/venues.html",
      onCreate: function () {
          console.log('Venues created!');
      },
      onReady: function () {
          console.log('Venues ready!');
          updateNavigation();
          updateRoleBasedUI();
      }
  });

  app.route({
      view: "login",
      load: "templates/login.html",
      onCreate: function () {
          console.log('Login created!');
          if (AuthService.isAuthenticated()) {
              window.location.href = '#home';
              return false;
          }
      },
      onReady: function () {
          console.log('Login ready!');
          updateNavigation();
          updateRoleBasedUI();

          $('#loginForm').off('submit');

          $('#loginForm').on('submit', async function (e) {
              e.preventDefault();
              console.log('Login form submitted');

              const email = $('#email').val();
              const password = $('#password').val();

              const submitBtn = $(this).find('button[type="submit"]');
              const originalText = submitBtn.html();
              submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Prijavljivanje...');

              try {
                  console.log('Attempting login...');
                  const result = await AuthService.login(email, password);
                  console.log('Login successful:', result);

                  alert('Uspješna prijava!');
                  window.location.href = '#home';
              } catch (error) {
                  console.error('Login error:', error);
                  alert(error.message || 'Greška pri prijavi');
                  submitBtn.prop('disabled', false).html(originalText);
              }
          });
      }
  });

  app.route({
      view: "register",
      load: "templates/register.html",
      onCreate: function () {
          console.log('Register created!');
          if (AuthService.isAuthenticated()) {
              window.location.href = '#home';
              return false;
          }
      },
      onReady: function () {
          console.log('Register ready!');
          updateNavigation();
          updateRoleBasedUI();

          // Clear any existing handlers to prevent duplicates
          $('#registerForm').off('submit');

          $('#registerForm').on('submit', async function (e) {
              e.preventDefault();
              console.log('Register form submitted');

              // Clear previous alerts
              $('#registerAlert').addClass('d-none').removeClass('alert-success alert-danger');

              const formData = {
                  full_name: $('#fullName').val(),
                  username: $('#username').val(),
                  email: $('#email').val(),
                  password: $('#password').val(),
                  role: 'user'
              };

              const confirmPassword = $('#confirmPassword').val();

              console.log('Form data:', formData);

              if (formData.password !== confirmPassword) {
                  $('#registerAlert')
                      .removeClass('d-none alert-success')
                      .addClass('alert-danger')
                      .text('Lozinke se ne poklapaju!');
                  return;
              }

              if (!$('#agreeTerms').is(':checked')) {
                  $('#registerAlert')
                      .removeClass('d-none alert-success')
                      .addClass('alert-danger')
                      .text('Morate prihvatiti uslove korištenja!');
                  return;
              }

              const submitBtn = $(this).find('button[type="submit"]');
              const originalText = submitBtn.html();
              submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Registracija...');

              try {
                  console.log('Sending registration request...');
                  const result = await AuthService.register(formData);
                  console.log('Registration successful:', result);

                  $('#registerAlert')
                      .removeClass('d-none alert-danger')
                      .addClass('alert-success')
                      .text('Uspješna registracija! Preusmjeravanje...');

                  setTimeout(() => {
                      window.location.href = '#home';
                  }, 1500);
              } catch (error) {
                  console.error('Registration error:', error);

                  let errorMessage = 'Greška pri registraciji';
                  if (error.message) {
                      if (error.message.includes('username-om već postoji')) {
                          errorMessage = 'Korisničko ime je već zauzeto. Molimo izaberite drugo.';
                      } else if (error.message.includes('emailom već postoji')) {
                          errorMessage = 'Email adresa je već registrovana.';
                      } else {
                          errorMessage = error.message;
                      }
                  }

                  $('#registerAlert')
                      .removeClass('d-none alert-success')
                      .addClass('alert-danger')
                      .text(errorMessage);

                  submitBtn.prop('disabled', false).html(originalText);
              }
          });
      }
  });

  app.route({
      view: "standings",
      load: "templates/standings.html",
      onCreate: function () {
          console.log('Standings created!');
      },
      onReady: function () {
          console.log('Standings ready!');
          updateNavigation();
          updateRoleBasedUI();
      }
  });

  app.run();
});
