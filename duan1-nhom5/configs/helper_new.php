if (!function_exists('requireHvd')) {
/**
* Yêu cầu quyền HDV
*/
function requireHvd()
{
requireLogin();
if (!isHvd()) {
$_SESSION['error'] = 'Bạn không có quyền truy cập trang này!';
header('Location: ' . BASE_URL);
exit;
}
}
}

/**
* Format duration to display as "X ngày Y đêm"
*/
if (!function_exists('formatDuration')) {
function formatDuration($days) {
$days = (int)$days;
$nights = max(0, $days - 1);
return "{$days} ngày {$nights} đêm";
}
}

/**
* Format date range for tour display
* Example: formatDateRange('2024-12-23', '2024-12-25') => "23-25/12/2024"
*/
if (!function_exists('formatDateRange')) {
function formatDateRange($startDate, $endDate) {
$start = new DateTime($startDate);
$end = new DateTime($endDate);

if ($start->format('Y-m') === $end->format('Y-m')) {
// Cùng tháng: "23-25/12/2024"
return $start->format('d') . '-' . $end->format('d/m/Y');
} else if ($start->format('Y') === $end->format('Y')) {
// Cùng năm: "30/12-02/01/2024"
return $start->format('d/m') . '-' . $end->format('d/m/Y');
} else {
// Khác năm: "30/12/2023-02/01/2024"
return $start->format('d/m/Y') . '-' . $end->format('d/m/Y');
}
}
}