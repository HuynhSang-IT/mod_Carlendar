<?php
defined('_JEXEC') or die;

// Lấy tháng, năm, timezone từ URL hoặc mặc định
$month = isset($_GET['month']) ? (int)$_GET['month'] : (int)date('n');
$year = isset($_GET['year']) ? (int)$_GET['year'] : (int)date('Y');
$timezone = isset($_GET['timezone']) ? $_GET['timezone'] : 'Asia/Ho_Chi_Minh';

// Tên tháng tiếng Việt
$monthNames = ["", "Tháng 1", "Tháng 2", "Tháng 3", "Tháng 4", "Tháng 5", "Tháng 6",
               "Tháng 7", "Tháng 8", "Tháng 9", "Tháng 10", "Tháng 11", "Tháng 12"];

// Lấy dữ liệu lịch từ helper (bạn nhớ gọi ở controller/module để gán $lunarData)
if (!isset($lunarData)) {
    // Nếu chưa có $lunarData thì lấy tạm
    $lunarData = ModLichQGHelper::getCalendarData($timezone);
    date_default_timezone_set($timezone);
}
?>

<style>
.calendar-container {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 1px;
    background: #ccc;
    border: 1px solid #999;
}
.calendar-cell {
    background: white;
    padding: 10px;
    min-height: 60px;
    cursor: pointer;
    user-select: none;
}
.calendar-cell span {
    display: block;
    font-size: 14px;
    text-align: center;
}
.calendar-cell .lunar-date {
    font-size: 12px;
    color: gray;
}
.header-cell {
    background: #2e7d32;
    color: white;
    font-weight: bold;
    text-align: center;
    padding: 5px;
    user-select: none;
}
.calendar-nav {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}
.calendar-timezone {
    margin-top: 10px;
    text-align: right;
    font-size: 13px;
    color: #555;
}
</style>

<form id="timezoneForm" style="margin-bottom: 10px;">
    <label>Chọn múi giờ:</label>
    <select id="timezoneSelect" name="timezone" onchange="onTimezoneChange()">
        <option value="Asia/Ho_Chi_Minh" <?= $timezone == 'Asia/Ho_Chi_Minh' ? 'selected' : '' ?>>Việt Nam (UTC+7)</option>
        <option value="Asia/Tokyo" <?= $timezone == 'Asia/Tokyo' ? 'selected' : '' ?>>Tokyo (UTC+9)</option>
        <option value="Europe/London" <?= $timezone == 'Europe/London' ? 'selected' : '' ?>>London (UTC+0)</option>
        <option value="America/New_York" <?= $timezone == 'America/New_York' ? 'selected' : '' ?>>New York (UTC-5)</option>
        <option value="UTC" <?= $timezone == 'UTC' ? 'selected' : '' ?>>UTC</option>
    </select>
</form>

<div class="calendar-nav">
    <button onclick="navigateMonth(-1)">← Tháng trước</button>
    <strong><?php echo $monthNames[$month] . ' / ' . $year; ?></strong>
    <button onclick="navigateMonth(1)">Tháng sau →</button>
</div>

<div class="calendar-container">
    <div class="header-cell">T2</div>
    <div class="header-cell">T3</div>
    <div class="header-cell">T4</div>
    <div class="header-cell">T5</div>
    <div class="header-cell">T6</div>
    <div class="header-cell">T7</div>
    <div class="header-cell">CN</div>

    <?php foreach ($lunarData as $item): ?>
        <div class="calendar-cell" onclick="showDay('<?php echo $item['day']; ?>')">
            <span><?php echo $item['day']; ?></span>
            <span class="lunar-date"><?php echo $item['lunar']; ?></span>
        </div>
    <?php endforeach; ?>
</div>

<div class="calendar-timezone">
    Múi giờ hiện tại: <strong id="currentTimezone"><?php echo htmlspecialchars($timezone); ?></strong>
</div>

<script>
let currentMonth = <?php echo $month; ?>;
let currentYear = <?php echo $year; ?>;
let currentTimezone = '<?php echo addslashes($timezone); ?>';

function navigateMonth(offset) {
    let newMonth = currentMonth + offset;
    let newYear = currentYear;

    if (newMonth < 1) {
        newMonth = 12;
        newYear--;
    } else if (newMonth > 12) {
        newMonth = 1;
        newYear++;
    }

    const tz = document.getElementById('timezoneSelect').value;
    const url = `index.php?option=com_ajax&module=lich_qg&format=raw&month=${newMonth}&year=${newYear}&timezone=${encodeURIComponent(tz)}`;

    fetch(url)
        .then(response => response.text())
        .then(html => {
            const wrapper = document.createElement('div');
            wrapper.innerHTML = html;
            const newCalendar = wrapper.querySelector('.calendar-nav').parentElement;
            document.querySelector('.calendar-nav').parentElement.replaceWith(newCalendar);
        });

    currentMonth = newMonth;
    currentYear = newYear;
    currentTimezone = tz;
}

function onTimezoneChange() {
    // Reset lại tháng/năm hiện tại
    const tz = document.getElementById('timezoneSelect').value;
    const url = `index.php?option=com_ajax&module=lich_qg&format=raw&month=${currentMonth}&year=${currentYear}&timezone=${encodeURIComponent(tz)}`;

    fetch(url)
        .then(response => response.text())
        .then(html => {
            const wrapper = document.createElement('div');
            wrapper.innerHTML = html;
            const newCalendar = wrapper.querySelector('.calendar-nav').parentElement;
            document.querySelector('.calendar-nav').parentElement.replaceWith(newCalendar);
        });

    currentTimezone = tz;
}
</script>
