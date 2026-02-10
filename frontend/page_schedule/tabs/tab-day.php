    <?php
    // Fetch total schedules per day
    $scheduleCounts = [];

    $stmt = $conn->prepare("
        SELECT day, COUNT(*) AS total
        FROM generated_schedule
        GROUP BY day
    ");

    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $scheduleCounts[$row['day']] = (int)$row['total'];
    }

    $stmt->close();
    ?>

<!-- HTML STUFF -->

<div id="subject-tab" class="table-container">
    <div class="table-header">
        <div class="header-text">
            <h2>On Day Schedules</h2>
            <p>List of schedules within a day</p>
        </div>

    </div>



    <table>
        <thead>
            <tr>
                <th>Day</th>
                <th>Total Schedules</th>
                <!-- <th>Short Name</th>
                <th>Type</th>
                <th>Units</th>
                <th>Hours</th>
                <th>Status</th>
                <th>Action</th> -->
            </tr>
        </thead>
        <!-- <tbody>
            <tr>
                <td>Monday</td>
                <td>testing</td>
            </tr>
            <tr>
                <td>Tuesday</td>
                <td>testing</td>
            </tr>
            <tr>
                <td>Wednesday</td>
                <td>testing</td>
            </tr>
            <tr>
                <td>Thursday</td>
                <td>testing</td>
            </tr>
            <tr>
                <td>Friday</td>
                <td>testing</td>
            </tr>
            <tr>
                <td>Saturday</td>
                <td>testing</td>
            </tr>

        </tbody> -->
        <tbody>
        <?php
        $days = ["Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"];
        foreach ($days as $dayName):
        ?>
        <tr class="clickable-row" data-href="schedule-ui.php?tab=day&view=day&day=<?= urlencode($dayName) ?>">
            
            <td><?= htmlspecialchars($dayName) ?></td>
            <td><?= $scheduleCounts[$dayName] ?? 0 ?></td>
        </tr>

        <?php endforeach; ?>
        </tbody>

    </table>


</div>