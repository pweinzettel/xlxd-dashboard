<?php
  $active = basename($_SERVER['SCRIPT_NAME'],'.php');
?>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <a class="navbar-brand" href="/">XLX</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarNav">
    <ul class="navbar-nav">

    <li class="nav-item <?php if ($active == 'users') echo 'active' ?>">
        <a class="nav-link" href="/users.php">Users / Modules</a>
      </li>

      <li class="nav-item <?php if ($active == 'repeaters') echo 'active' ?>">
        <a class="nav-link" href="/repeaters.php">Repeaters / Nodes</a>
      </li>

      <li class="nav-item <?php if ($active == 'peers') echo 'active' ?>">
        <a class="nav-link" href="/peers.php">Peers</a>
      </li>
<!--
      <li class="nav-item <?php if ($active == 'modules') echo 'active' ?>">
        <a class="nav-link" href="/modules.php">Modules List</a>
      </li>
-->
      <li class="nav-item <?php if ($active == 'reflectors') echo 'active' ?>">
        <a class="nav-link" href="/reflectors.php">Reflectors List</a>
      </li>
<!--
      <li class="nav-item <?php if ($active == 'systeminfo') echo 'active' ?>">
        <a class="nav-link" href="/systeminfo.php">System Info</a>
      </li>
-->
    </ul>
  </div>
</nav>