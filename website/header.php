<header id="header" class="header">
  <span class="logo-text">Forest Explorer</span>
  <nav class="nav">
    <ul>
      <li>
        <a href="/index.php">HOME</a>
      </li>
      <li>
        <a href="/map.php">MAP</a>
      </li>
      <li>
        <a href="/panel.php">ADMIN PANEL</a>
      </li>
      <li>
        <button id="profile-btn" class="btn">
          <img class="img" src="assets/images/profile-icon.svg">
        </button>
        <div id="myDropdown" class="dropdown-content">
          <a href="/api/logout.php">Logout</a>
        </div>
      </li>
    </ul>
  </nav>
</header>
<script type="text/javascript">
  $('#profile-btn').on('click', (e) => {
    $('#myDropdown').toggleClass('show');
  });
</script>