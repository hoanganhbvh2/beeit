<?php session_start(); include '../../includes/db_connect.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Hoạt Động</title>
    <link rel="stylesheet" href="../css/style.css"> <!-- Updated to relative path within views -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="container">
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
            <a class="navbar-brand" href="/project1">Quản Lý</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="/project1">Danh sách nhân sự</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active text-white" aria-current="page" href="/project1/views/activities/activity_management.php">Quản lý hoạt động</a>
                    </li>
                </ul>
            </div>
        </nav>

        <?php
        // Display session messages
        if (isset($_SESSION['message'])) {
            $message_type = $_SESSION['message_type'] ?? 'info'; // Default to info if type not set
            echo '<div class="alert alert-' . $message_type . ' alert-dismissible fade show" role="alert">';
            echo htmlspecialchars($_SESSION['message']);
            echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
            echo '</div>';
            unset($_SESSION['message']); // Clear the message after displaying
            unset($_SESSION['message_type']); // Clear the message type as well
        }
        ?>

        <div class="main-heading">Quản Lý Hoạt Động</div> <!-- Changed from h1 to div with class main-heading -->
        <div class="statistics mb-4 p-3 border rounded">
            <h2 class="text-center mb-3">Thống Kê Hoạt Động</h2>
            <div class="row">
                <div class="col-md-6">
                    <h3 class="text-center">Hoạt động có nhiều người tham gia nhất</h3>
                    <canvas id="mostParticipatedActivityChart"></canvas>
                </div>
                <div class="col-md-6">
                    <h3 class="text-center">Thành viên tham gia nhiều hoạt động nhất</h3>
                    <canvas id="mostActiveMemberChart"></canvas>
                </div>
            </div>
            <?php
            // Hoạt động có nhiều người tham gia nhất
            $sql_most_participated_activity = "
                SELECT a.name AS activity_name, COUNT(ma.member_id) AS participant_count
                FROM activities a
                JOIN member_activities ma ON a.id = ma.activity_id
                WHERE ma.participation_status = 'tham_gia'
                GROUP BY a.id, a.name
                ORDER BY participant_count DESC
                LIMIT 3;"; // Limit to top 3 for chart
            $stmt_most_participated_activity = $conn->prepare($sql_most_participated_activity);
            $stmt_most_participated_activity->execute();
            $most_participated_activities_data = $stmt_most_participated_activity->fetchAll(PDO::FETCH_ASSOC);

            // Thành viên tham gia nhiều hoạt động nhất
            $sql_most_active_member = "
                SELECT m.name AS member_name, COUNT(ma.activity_id) AS activity_count
                FROM members m
                JOIN member_activities ma ON m.id = ma.member_id
                WHERE ma.participation_status = 'tham_gia'
                GROUP BY m.id, m.name
                ORDER BY activity_count DESC
                LIMIT 5;"; // Limit to top 5 for chart
            $stmt_most_active_member = $conn->prepare($sql_most_active_member);
            $stmt_most_active_member->execute();
            $most_active_members_data = $stmt_most_active_member->fetchAll(PDO::FETCH_ASSOC);

            // Encode data for JavaScript
            $most_participated_activity_json = json_encode($most_participated_activities_data);
            $most_active_member_json = json_encode($most_active_members_data);
            ?>
        </div>
        <a href="/project1/views/activities/add_activity.php" class="btn btn-primary mb-3">Thêm Hoạt Động Mới</a>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tên Hoạt Động</th>
                    <th>Mô Tả</th>
                    <th>Ngày Hoạt Động</th>
                    <th>Thành viên tham gia</th>
                    <th>Tổng số lượng người tham gia</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT a.*, 
                               COUNT(DISTINCT ma.member_id) AS total_participants,
                               GROUP_CONCAT(DISTINCT CONCAT(m.name, ' (BEE', m.id, ')') ORDER BY m.name SEPARATOR ', ') AS participating_members
                        FROM activities a
                        LEFT JOIN member_activities ma ON a.id = ma.activity_id AND ma.participation_status = 'tham_gia'
                        LEFT JOIN members m ON ma.member_id = m.id
                        GROUP BY a.id
                        ORDER BY a.activity_date DESC";
                $stmt = $conn->prepare($sql);
                $stmt->execute();
                $activities = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if (count($activities) > 0) {
                    foreach($activities as $row) {
                        echo "<tr>";
                        echo "<td>" . $row["id"] . "</td>";
                        echo "<td>" . htmlspecialchars($row["name"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["description"]) . "</td>";
                        echo "<td>" . $row["activity_date"] . "</td>";
                        echo "<td>" . (empty($row['participating_members']) ? 'Chưa có' : htmlspecialchars($row['participating_members'])) . "</td>";
                        echo "<td>" . $row['total_participants'] . "</td>";
                        echo "<td>
                                <a href='/project1/views/activities/manage_participation.php?activity_id=" . $row["id"] . "' class='btn btn-secondary btn-sm'>Quản lý tham gia</a>
                              </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='7'>Không có hoạt động nào.</td></tr>";
                }
                //$conn->close(); // PDO does not have a close method like mysqli
                ?>
            </tbody>
        </table>
    </div>
    <script>
        const mostParticipatedActivityData = <?php echo $most_participated_activity_json; ?>;
        const mostActiveMemberData = <?php echo $most_active_member_json; ?>;

        // Chart 1: Hoạt động có nhiều người tham gia nhất
        if (mostParticipatedActivityData.length > 0) {
            const activityLabels = mostParticipatedActivityData.map(item => item.activity_name);
            const participantCounts = mostParticipatedActivityData.map(item => item.participant_count);

            new Chart(document.getElementById('mostParticipatedActivityChart'), {
                type: 'bar',
                data: {
                    labels: activityLabels,
                    datasets: [{
                        label: 'Số người tham gia',
                        data: participantCounts,
                        backgroundColor: 'rgba(54, 162, 235, 0.6)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Số lượng'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Hoạt động'
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        }

        // Chart 2: Thành viên tham gia nhiều hoạt động nhất
        if (mostActiveMemberData.length > 0) {
            const memberLabels = mostActiveMemberData.map(item => item.member_name);
            const activityCounts = mostActiveMemberData.map(item => item.activity_count);

            new Chart(document.getElementById('mostActiveMemberChart'), {
                type: 'bar',
                data: {
                    labels: memberLabels,
                    datasets: [{
                        label: 'Số hoạt động tham gia',
                        data: activityCounts,
                        backgroundColor: 'rgba(75, 192, 192, 0.6)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Số lượng'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Thành viên'
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
