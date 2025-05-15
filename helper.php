<?php
defined('_JEXEC') or die;

class ModLichQGHelper
{
    /**
     * Chuyển đổi lịch dương sang âm lịch theo múi giờ
     * Bạn nên thay thế hàm này bằng thư viện chuẩn nếu cần chính xác
     */
    public static function convertSolarToLunar($day, $month, $year, $timezone = 'Asia/Ho_Chi_Minh') {
        date_default_timezone_set($timezone);

        // Demo giả lập (bạn thay bằng hàm thực tế)
        $lunarDay = ($day + 10) % 30;
        $lunarDay = $lunarDay == 0 ? 30 : $lunarDay;
        $lunarMonth = ($month + 11) % 12 + 1;

        return ['lunarDay' => $lunarDay, 'lunarMonth' => $lunarMonth];
    }

    /**
     * Lấy dữ liệu lịch âm - dương
     * Truyền tham số timezone để điều chỉnh múi giờ/quốc gia
     */
    public static function getCalendarData($timezone = 'Asia/Ho_Chi_Minh')
    {
        date_default_timezone_set($timezone);

        $month = isset($_GET['month']) ? (int)$_GET['month'] : (int)date('n');
        $year  = isset($_GET['year']) ? (int)$_GET['year'] : (int)date('Y');

        $firstDayOfMonth = mktime(0, 0, 0, $month, 1, $year);
        $totalDays = date('t', $firstDayOfMonth);
        $startWeekday = date('N', $firstDayOfMonth);

        $calendarData = [];

        // Ô trống đầu tuần
        for ($i = 1; $i < $startWeekday; $i++) {
            $calendarData[] = ['day' => '', 'lunar' => ''];
        }

        for ($day = 1; $day <= $totalDays; $day++) {
            $lunar = self::convertSolarToLunar($day, $month, $year, $timezone);
            $lunarStr = $lunar['lunarDay'] . '/' . $lunar['lunarMonth'];

            $calendarData[] = [
                'day' => $day,
                'lunar' => $lunarStr
            ];
        }

        return $calendarData;
    }
}
