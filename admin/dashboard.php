<?php
  require('inc/essentials.php');
  require('inc/db_config.php');
  adminLogin();

  // Lấy period từ URL hoặc mặc định là '7days'
  $period = isset($_GET['period']) ? $_GET['period'] : '7days';
  
  // Debug
  echo "<!-- DEBUG: Period = $period -->";
  
  // Xác định khoảng thời gian
  switch($period) {
    case '7days':
      $date_condition = "datentime >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
      $period_label = "7 ngày qua";
      break;
    case 'year':
      $date_condition = "YEAR(datentime) = YEAR(NOW())";
      $period_label = "năm nay";
      break;
    case 'month':
    default:
      $date_condition = "YEAR(datentime) = YEAR(NOW()) AND MONTH(datentime) = MONTH(NOW())";
      $period_label = "tháng này";
      break;
  }

  echo "<!-- DEBUG: Date condition = $date_condition -->";

  // Thống kê cơ bản với filter thời gian
  $tong_doanhthu_query = "SELECT COALESCE(SUM(trans_amt), 0) as total FROM booking_order WHERE $date_condition";
  $tong_doanhthu = mysqli_fetch_assoc(mysqli_query($con, $tong_doanhthu_query))['total'];

  echo "<!-- DEBUG: Revenue query = $tong_doanhthu_query -->";
  echo "<!-- DEBUG: Revenue result = $tong_doanhthu -->";

  $total_bookings_query = "SELECT COUNT(*) as total FROM booking_order WHERE $date_condition";
  $total_bookings = mysqli_fetch_assoc(mysqli_query($con, $total_bookings_query))['total'];

  echo "<!-- DEBUG: Bookings result = $total_bookings -->";

  $cancelled_bookings_query = "SELECT COUNT(*) as total FROM booking_order WHERE booking_status = 'Đã Huỷ' AND $date_condition";
  $cancelled_bookings = mysqli_fetch_assoc(mysqli_query($con, $cancelled_bookings_query))['total'];

  $total_users_query = "SELECT COUNT(*) as total FROM user_cred";
  $total_users = mysqli_fetch_assoc(mysqli_query($con, $total_users_query))['total'];

  $total_rooms_query = "SELECT SUM(quantity) as total FROM rooms";
  $total_rooms = mysqli_fetch_assoc(mysqli_query($con, $total_rooms_query))['total'];

  $booked_rooms_query = "SELECT COUNT(*) as total FROM booking_order WHERE booking_status IN ('Đã Xác Nhận Đặt Phòng', 'Đã Thanh Toán')";
  $booked_rooms = mysqli_fetch_assoc(mysqli_query($con, $booked_rooms_query))['total'];

  $available_rooms = $total_rooms - $booked_rooms;
  $occupancy_rate = $total_rooms > 0 ? round(($booked_rooms / $total_rooms) * 100, 1) : 0;

  $total_reviews_query = "SELECT COUNT(*) as total FROM rating_review";
  $total_reviews = mysqli_fetch_assoc(mysqli_query($con, $total_reviews_query))['total'];

  $total_queries_query = "SELECT COUNT(*) as total FROM user_queries";
  $total_queries = mysqli_fetch_assoc(mysqli_query($con, $total_queries_query))['total'];

  // Thống kê 7 ngày (cho biểu đồ)
  $chart_days = ($period == '7days') ? 7 : (($period == 'month') ? 30 : 365);
  $revenue_7days = [];
  $bookings_7days = [];
  $days_labels = [];
  
  // Lấy dữ liệu cho biểu đồ
  if($period == '7days') {
    // Lấy ngày hiện tại từ MySQL để đồng bộ
    $current_date = mysqli_fetch_assoc(mysqli_query($con, "SELECT CURDATE() as today"))['today'];
    echo "<!-- DEBUG: MySQL current date: $current_date -->";
    echo "<!-- DEBUG: PHP current date: " . date('Y-m-d') . " -->";
    
    // 7 ngày: hiển thị theo ngày (bao gồm hôm nay)
    for($i = 6; $i >= 0; $i--) {
      $date = date('Y-m-d', strtotime("$current_date -$i days"));
      $day_label = date('D', strtotime("$current_date -$i days"));
      $days_labels[] = $day_label;
      
      $rev_query = "SELECT COALESCE(SUM(trans_amt), 0) as revenue FROM booking_order WHERE DATE(datentime) = '$date'";
      $daily_revenue = mysqli_fetch_assoc(mysqli_query($con, $rev_query))['revenue'];
      $revenue_7days[] = (int)$daily_revenue;
      
      $book_query = "SELECT COUNT(*) as count FROM booking_order WHERE DATE(datentime) = '$date'";
      $daily_bookings = mysqli_fetch_assoc(mysqli_query($con, $book_query))['count'];
      $bookings_7days[] = (int)$daily_bookings;
      
      echo "<!-- DEBUG: Date $date ($day_label) - Revenue: $daily_revenue, Bookings: $daily_bookings -->";
    }
    
    echo "<!-- DEBUG: Final revenue array: " . json_encode($revenue_7days) . " -->";
    echo "<!-- DEBUG: Final bookings array: " . json_encode($bookings_7days) . " -->";
  } elseif($period == 'month') {
    // Tháng này: hiển thị theo tuần (4 tuần)
    for($i = 3; $i >= 0; $i--) {
      $start_date = date('Y-m-d', strtotime("-".($i*7)." days"));
      $end_date = date('Y-m-d', strtotime("-".(($i-1)*7)." days"));
      $days_labels[] = 'Tuần ' . (4-$i);
      
      $rev_query = "SELECT COALESCE(SUM(trans_amt), 0) as revenue FROM booking_order WHERE DATE(datentime) BETWEEN '$start_date' AND '$end_date'";
      $revenue_7days[] = mysqli_fetch_assoc(mysqli_query($con, $rev_query))['revenue'];
      
      $book_query = "SELECT COUNT(*) as count FROM booking_order WHERE DATE(datentime) BETWEEN '$start_date' AND '$end_date'";
      $bookings_7days[] = mysqli_fetch_assoc(mysqli_query($con, $book_query))['count'];
    }
  } else {
    // Năm nay: hiển thị theo tháng (12 tháng)
    for($i = 11; $i >= 0; $i--) {
      $month = date('m', strtotime("-$i months"));
      $year = date('Y', strtotime("-$i months"));
      $days_labels[] = 'T' . $month;
      
      $rev_query = "SELECT COALESCE(SUM(trans_amt), 0) as revenue FROM booking_order WHERE MONTH(datentime) = '$month' AND YEAR(datentime) = '$year'";
      $revenue_7days[] = mysqli_fetch_assoc(mysqli_query($con, $rev_query))['revenue'];
      
      $book_query = "SELECT COUNT(*) as count FROM booking_order WHERE MONTH(datentime) = '$month' AND YEAR(datentime) = '$year'";
      $bookings_7days[] = mysqli_fetch_assoc(mysqli_query($con, $book_query))['count'];
    }
  }
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard - Admin</title>
  <?php require('inc/links.php'); ?>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    body { background: #f5f7fa; }
    .dashboard-header {
      background: white;
      padding: 20px 30px;
      border-radius: 15px;
      margin-bottom: 30px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    .stat-card {
      background: white;
      border-radius: 15px;
      padding: 25px;
      box-shadow: 0 2px 15px rgba(0,0,0,0.08);
      transition: transform 0.3s;
      height: 100%;
    }
    .stat-card:hover { transform: translateY(-5px); }
    .stat-icon {
      width: 60px;
      height: 60px;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 28px;
      color: white;
      margin-bottom: 15px;
    }
    .stat-number {
      font-size: 28px;
      font-weight: 700;
      margin: 8px 0;
      color: #2c3e50;
    }
    .stat-label {
      font-size: 13px;
      color: #7f8c8d;
      margin-bottom: 5px;
    }
    .stat-change {
      font-size: 12px;
      font-weight: 600;
      padding: 4px 8px;
      border-radius: 6px;
      display: inline-block;
    }
    .stat-change.up { background: #d4edda; color: #155724; }
    .stat-change.down { background: #f8d7da; color: #721c24; }
    .chart-card {
      background: white;
      border-radius: 15px;
      padding: 25px;
      box-shadow: 0 2px 15px rgba(0,0,0,0.08);
      margin-bottom: 30px;
    }
    .chart-title {
      font-size: 18px;
      font-weight: 600;
      color: #2c3e50;
      margin-bottom: 5px;
    }
    .chart-value {
      font-size: 20px;
      font-weight: 700;
      color: #27ae60;
    }
    .bg-green { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); }
    .bg-blue { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
    .bg-pink { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
    .bg-orange { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); }
    .bg-cyan { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
    .bg-purple { background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%); }
    .bg-red { background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%); }
    .bg-teal { background: linear-gradient(135deg, #13547a 0%, #80d0c7 100%); }
    .bg-warning { background: linear-gradient(135deg, #f7971e 0%, #ffd200 100%); }
    .time-filter { display: flex; gap: 10px; }
    .time-btn {
      padding: 8px 16px;
      border: none;
      background: #f0f0f0;
      border-radius: 8px;
      font-size: 13px;
      font-weight: 500;
      cursor: pointer;
    }
    .time-btn.active { background: #667eea; color: white; }
    .activity-list { max-height: 400px; overflow-y: auto; }
    .activity-item {
      padding: 15px 0;
      border-bottom: 1px solid #f0f0f0;
    }
    .activity-item:last-child { border-bottom: none; }
    .activity-icon {
      width: 45px;
      height: 45px;
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-size: 20px;
    }
    .activity-title {
      font-size: 14px;
      font-weight: 600;
      color: #2c3e50;
      margin-bottom: 4px;
    }
    .activity-subtitle {
      font-size: 12px;
      color: #7f8c8d;
    }
    .activity-value {
      font-size: 14px;
      font-weight: 700;
      color: #27ae60;
    }
  </style>
</head>
<body>

<?php require('inc/header.php'); ?>

<div class="container-fluid" id="main-content">
  <div class="row">
    <div class="col-lg-10 ms-auto p-4">
      
      <!-- Header -->
      <div class="dashboard-header">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <h3 class="mb-1">📊 Trang chủ</h3>
            <p class="text-muted mb-0">Tổng quan hiệu suất và thống kê hệ thống khách sạn (<?php echo $period_label; ?>)</p>
          </div>
          <div class="time-filter">
            <button class="time-btn <?php echo $period == '7days' ? 'active' : ''; ?>" onclick="filterTime('7days')">7 Ngày</button>
            <button class="time-btn <?php echo $period == 'month' ? 'active' : ''; ?>" onclick="filterTime('month')">Tháng này</button>
            <button class="time-btn <?php echo $period == 'year' ? 'active' : ''; ?>" onclick="filterTime('year')">Năm này</button>
          </div>
        </div>
      </div>

      <!-- Stats Row 1 -->
      <div class="row mb-4">
        <div class="col-md-3 mb-3">
          <div class="stat-card">
            <div class="stat-icon bg-green">
              <i class="bi bi-cash-stack"></i>
            </div>
            <p class="stat-label">Tổng doanh thu</p>
            <h3 class="stat-number"><?php echo number_format($tong_doanhthu/1000000, 1) ?> triệu</h3>
            <span class="stat-change up">↑ +13% vs tháng trước</span>
          </div>
        </div>

        <div class="col-md-3 mb-3">
          <div class="stat-card">
            <div class="stat-icon bg-blue">
              <i class="bi bi-calendar-check"></i>
            </div>
            <p class="stat-label">Tổng đặt phòng</p>
            <h3 class="stat-number"><?php echo $total_bookings ?></h3>
            <span class="stat-change up">↑ +5.2% vs tháng trước</span>
          </div>
        </div>

        <div class="col-md-3 mb-3">
          <div class="stat-card">
            <div class="stat-icon bg-pink">
              <i class="bi bi-people"></i>
            </div>
            <p class="stat-label">Tổng người dùng</p>
            <h3 class="stat-number"><?php echo $total_users ?></h3>
            <span class="stat-change up">↑ +23 mới hôm nay</span>
          </div>
        </div>

        <div class="col-md-3 mb-3">
          <div class="stat-card">
            <div class="stat-icon bg-orange">
              <i class="bi bi-graph-up-arrow"></i>
            </div>
            <p class="stat-label">Tỷ lệ lấp đầy</p>
            <h3 class="stat-number"><?php echo $occupancy_rate ?>%</h3>
            <span class="stat-change up">↑ +1.3% vs hôm qua</span>
          </div>
        </div>
      </div>

      <!-- Stats Row 2 -->
      <div class="row mb-4">
        <div class="col-md-3 mb-3">
          <div class="stat-card">
            <div class="stat-icon bg-cyan">
              <i class="bi bi-door-open"></i>
            </div>
            <p class="stat-label">Phòng đang trống</p>
            <h3 class="stat-number"><?php echo $available_rooms ?></h3>
            <span class="stat-change">Tổng: <?php echo $total_rooms ?> phòng</span>
          </div>
        </div>

        <div class="col-md-3 mb-3">
          <div class="stat-card">
            <div class="stat-icon bg-purple">
              <i class="bi bi-check-circle"></i>
            </div>
            <p class="stat-label">Xếp hạng và đánh giá</p>
            <h3 class="stat-number"><?php echo $total_reviews ?></h3>
            <span class="stat-change up">↑ +5.2% vs tháng trước</span>
          </div>
        </div>

        <div class="col-md-3 mb-3">
          <div class="stat-card">
            <div class="stat-icon bg-teal">
              <i class="bi bi-chat-dots"></i>
            </div>
            <p class="stat-label">Phản hồi và góp ý</p>
            <h3 class="stat-number"><?php echo $total_queries ?></h3>
            <span class="stat-change">Chờ xử lý</span>
          </div>
        </div>

        <div class="col-md-3 mb-3">
          <div class="stat-card">
            <div class="stat-icon bg-red">
              <i class="bi bi-x-circle"></i>
            </div>
            <p class="stat-label">Phòng bị huỷ</p>
            <h3 class="stat-number"><?php echo $cancelled_bookings ?></h3>
            <span class="stat-change down">↓ -2.1% vs tháng trước</span>
          </div>
        </div>
      </div>

      <!-- Charts Row -->
      <div class="row">
        <div class="col-md-6 mb-4">
          <div class="chart-card">
            <div class="mb-3">
              <div class="chart-title">💰 Doanh thu <?php echo $period_label; ?></div>
              <div class="chart-value"><?php echo number_format(array_sum($revenue_7days), 0, ',', '.') ?> đ</div>
            </div>
            <div style="height: 250px;">
              <canvas id="revenueChart"></canvas>
            </div>
          </div>
        </div>

        <div class="col-md-6 mb-4">
          <div class="chart-card">
            <div class="mb-3">
              <div class="chart-title">📊 Đặt phòng <?php echo $period_label; ?></div>
              <div class="chart-value"><?php echo array_sum($bookings_7days) ?> lượt</div>
            </div>
            <div style="height: 250px;">
              <canvas id="bookingsChart"></canvas>
            </div>
          </div>
        </div>
      </div>

      <!-- Activities Row -->
      <div class="row">
        <div class="col-md-6 mb-4">
          <div class="chart-card">
            <div class="chart-title">🎬 Top phòng bán chạy nhất</div>
            <div class="activity-list">
              <?php
                $top_rooms_query = "SELECT r.name, r.area, COUNT(bo.booking_id) as bookings, SUM(bo.trans_amt) as revenue
                  FROM booking_order bo
                  INNER JOIN rooms r ON bo.room_id = r.id
                  WHERE bo.booking_status IN ('Đã Thanh Toán', 'Đã Xác Nhận Đặt Phòng')
                  GROUP BY r.id
                  ORDER BY bookings DESC
                  LIMIT 5";
                $top_rooms_result = mysqli_query($con, $top_rooms_query);
                
                if(mysqli_num_rows($top_rooms_result) > 0) {
                  while($room = mysqli_fetch_assoc($top_rooms_result)) {
                    echo '
                    <div class="activity-item">
                      <div class="d-flex align-items-center">
                        <div class="activity-icon bg-warning">
                          <i class="bi bi-star-fill"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                          <div class="activity-title">'.$room['name'].'</div>
                          <div class="activity-subtitle">'.$room['area'].'m² • '.$room['bookings'].' lượt đặt</div>
                        </div>
                        <div class="activity-value">'.number_format($room['revenue'], 0, ',', '.').' đ</div>
                      </div>
                    </div>';
                  }
                } else {
                  echo '<p class="text-muted text-center py-4">Chưa có dữ liệu</p>';
                }
              ?>
            </div>
          </div>
        </div>

        <div class="col-md-6 mb-4">
          <div class="chart-card">
            <div class="chart-title">⚡ Hoạt động gần đây</div>
            <div class="activity-list">
              <?php
                $recent_query = "SELECT bo.booking_id, bo.booking_status, bo.datentime, 
                  uc.name as user_name, r.name as room_name, bo.trans_amt
                  FROM booking_order bo
                  INNER JOIN user_cred uc ON bo.user_id = uc.id
                  INNER JOIN rooms r ON bo.room_id = r.id
                  ORDER BY bo.datentime DESC
                  LIMIT 5";
                $recent_result = mysqli_query($con, $recent_query);
                
                if(mysqli_num_rows($recent_result) > 0) {
                  while($activity = mysqli_fetch_assoc($recent_result)) {
                    $icon_class = 'bg-info';
                    $icon = 'bi-calendar-check';
                    
                    if($activity['booking_status'] == 'Đã Thanh Toán') {
                      $icon_class = 'bg-success';
                      $icon = 'bi-check-circle';
                    } elseif($activity['booking_status'] == 'Đã Huỷ') {
                      $icon_class = 'bg-danger';
                      $icon = 'bi-x-circle';
                    }
                    
                    $time_ago = date('d/m H:i', strtotime($activity['datentime']));
                    
                    echo '
                    <div class="activity-item">
                      <div class="d-flex align-items-center">
                        <div class="activity-icon '.$icon_class.'">
                          <i class="bi '.$icon.'"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                          <div class="activity-title">'.$activity['user_name'].' đã đặt '.$activity['room_name'].'</div>
                          <div class="activity-subtitle">'.$time_ago.' • '.$activity['booking_status'].'</div>
                        </div>
                      </div>
                    </div>';
                  }
                } else {
                  echo '<p class="text-muted text-center py-4">Chưa có hoạt động nào</p>';
                }
              ?>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

<script>
  console.log('Days labels:', <?php echo json_encode($days_labels); ?>);
  console.log('Revenue data:', <?php echo json_encode($revenue_7days); ?>);
  console.log('Bookings data:', <?php echo json_encode($bookings_7days); ?>);
  
  // Revenue Chart
  const revenueCtx = document.getElementById('revenueChart').getContext('2d');
  new Chart(revenueCtx, {
    type: 'bar',
    data: {
      labels: <?php echo json_encode($days_labels); ?>,
      datasets: [{
        label: 'Doanh thu',
        data: <?php echo json_encode($revenue_7days); ?>,
        backgroundColor: function(context) {
          const chart = context.chart;
          const {ctx, chartArea} = chart;
          if (!chartArea) return null;
          const gradient = ctx.createLinearGradient(0, chartArea.bottom, 0, chartArea.top);
          gradient.addColorStop(0, 'rgba(17, 153, 142, 0.5)');
          gradient.addColorStop(1, 'rgba(56, 239, 125, 0.9)');
          return gradient;
        },
        borderRadius: 10,
        barThickness: 40
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: { 
        legend: { display: false },
        tooltip: {
          backgroundColor: 'rgba(0,0,0,0.8)',
          padding: 12,
          titleFont: { size: 14 },
          bodyFont: { size: 13 },
          callbacks: {
            label: function(context) {
              return 'Doanh thu: ' + context.parsed.y.toLocaleString('vi-VN') + ' đ';
            }
          }
        }
      },
      scales: {
        x: {
          grid: { display: false },
          ticks: { 
            font: { size: 12, weight: '500' },
            color: '#7f8c8d'
          }
        },
        y: {
          beginAtZero: true,
          grid: { 
            color: 'rgba(0,0,0,0.05)',
            drawBorder: false
          },
          ticks: {
            font: { size: 11 },
            color: '#7f8c8d',
            callback: function(value) {
              if (value >= 1000000) {
                return (value / 1000000).toFixed(1) + 'M';
              } else if (value >= 1000) {
                return (value / 1000).toFixed(0) + 'K';
              }
              return value.toLocaleString();
            }
          }
        }
      }
    }
  });

  // Bookings Chart
  const bookingsCtx = document.getElementById('bookingsChart').getContext('2d');
  new Chart(bookingsCtx, {
    type: 'bar',
    data: {
      labels: <?php echo json_encode($days_labels); ?>,
      datasets: [{
        label: 'Số đặt phòng',
        data: <?php echo json_encode($bookings_7days); ?>,
        backgroundColor: function(context) {
          const chart = context.chart;
          const {ctx, chartArea} = chart;
          if (!chartArea) return null;
          const gradient = ctx.createLinearGradient(0, chartArea.bottom, 0, chartArea.top);
          gradient.addColorStop(0, 'rgba(79, 172, 254, 0.5)');
          gradient.addColorStop(1, 'rgba(0, 242, 254, 0.9)');
          return gradient;
        },
        borderRadius: 10,
        barThickness: 40
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: { 
        legend: { display: false },
        tooltip: {
          backgroundColor: 'rgba(0,0,0,0.8)',
          padding: 12,
          titleFont: { size: 14 },
          bodyFont: { size: 13 },
          callbacks: {
            label: function(context) {
              return 'Số đặt phòng: ' + context.parsed.y + ' lượt';
            }
          }
        }
      },
      scales: {
        x: {
          grid: { display: false },
          ticks: { 
            font: { size: 12, weight: '500' },
            color: '#7f8c8d'
          }
        },
        y: {
          beginAtZero: true,
          grid: { 
            color: 'rgba(0,0,0,0.05)',
            drawBorder: false
          },
          ticks: { 
            stepSize: 1,
            font: { size: 11 },
            color: '#7f8c8d'
          }
        }
      }
    }
  });
</script>

<script>
  // Filter time function
  function filterTime(period) {
    // Reload page with period parameter
    window.location.href = '?period=' + period;
  }
</script>

<?php require('inc/scripts.php'); ?>
</body>
</html>
