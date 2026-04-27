<?php
  require('inc/essentials.php');
  require('inc/db_config.php');
  adminLogin();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin - Reset Password Tokens</title>
  <?php require('inc/links.php'); ?>
</head>
<body class="bg-light">

  <?php require('inc/header.php'); ?>

  <div class="container-fluid" id="main-content">
    <div class="row">
      <div class="col-lg-10 ms-auto p-4 overflow-hidden">
        <h3 class="mb-4">Reset Password Tokens</h3>

        <div class="card border-0 shadow-sm mb-4">
          <div class="card-body">
            
            <div class="table-responsive">
              <table class="table table-hover border">
                <thead>
                  <tr class="bg-dark text-light">
                    <th>ID</th>
                    <th>Tên</th>
                    <th>Email</th>
                    <th>Token</th>
                    <th>Hết hạn</th>
                    <th>Link Reset</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                    $q = "SELECT * FROM `user_cred` WHERE `token` IS NOT NULL ORDER BY `t_expire` DESC";
                    $data = mysqli_query($con, $q);

                    if (mysqli_num_rows($data) == 0) {
                      echo "<tr><td colspan='6' class='text-center'>Không có token nào</td></tr>";
                    }

                    while ($row = mysqli_fetch_assoc($data)) {
                      $reset_link = "http://localhost/reset_password.php?email=" . urlencode($row['email']) . "&token=" . $row['token'];
                      
                      echo <<<data
                        <tr>
                          <td>{$row['id']}</td>
                          <td>{$row['name']}</td>
                          <td>{$row['email']}</td>
                          <td><code>{$row['token']}</code></td>
                          <td>{$row['t_expire']}</td>
                          <td><a href="$reset_link" target="_blank" class="btn btn-sm btn-primary">Mở Link</a></td>
                        </tr>
                      data;
                    }
                  ?>
                </tbody>
              </table>
            </div>

          </div>
        </div>

      </div>
    </div>
  </div>

  <?php require('inc/scripts.php'); ?>
</body>
</html>
