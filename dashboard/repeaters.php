<?php include_once './secured/functions.php'; ?>

<!doctype html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <script src="https://kit.fontawesome.com/51e04d4a5c.js" crossorigin="anonymous"></script>

  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

  <link rel="stylesheet" href="/inc/css/main.css">

  <title><?php echo get_opt('ReflectorName') . ' - ' . get_opt('PageTitle') ?></title>

  <script type="text/javascript">
    <?php echo "var updates = " . get_opt('UpdateData') . ";"; ?>
  </script>

</head>

<body>
  <?php include_once './secured/menu.php'; ?>
  <div class="row">
    <div class="col">

      <div class="card">
        <div class="card-header">
          <input type="text" class="filter" id="filter" placeholder="Filter">
          <label id="lastupdate"></label>
        </div>
        <ul class="list-group list-group-flush">
          <li class="list-group-item" style="overflow-x: auto;">

            <table class="table">
              <thead class="thead-light">
                <tr>
                  <th class="nowrap" scope="col">Flag</th>
                  <th class="nowrap" scope="col">DV Station</th>
                  <th class="nowrap" scope="col">Band</th>
                  <th class="nowrap" scope="col">Last Heard</th>
                  <th class="nowrap" scope="col">Linked for</th>
                  <th class="nowrap" scope="col">Protocol</th>
                  <th class="nowrap" scope="col">Module</th>
                  <th class="nowrap" scope="col">IP</th>
                </tr>
              </thead>
              <tbody id="nodestbody">

              </tbody>
            </table>
            <div class="hol" style="width: 100%; text-align: center; ">
              <p>Loading content...</p>
            </div>
          </li>
        </ul>
      </div>


    </div>
  </div>



  <!-- Optional JavaScript -->
  <!-- jQuery first, then Popper.js, then Bootstrap JS -->
  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
  <script src="inc/js/functions.js"></script>
  <script src="inc/js/repeaters.js"></script>
</body>

</html>