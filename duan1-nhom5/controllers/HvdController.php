<?php
class HvdController
{
    public function home()
    {

        require_once PATH_MODEL . 'TourModel.php';
        require_once PATH_MODEL . 'GuideTourHistoryModel.php';
        require_once PATH_MODEL . 'TourScheduleModel.php';

        $tourModel = new TourModel();
        $historyModel = new GuideTourHistoryModel();
        $scheduleModel = new TourScheduleModel();
        require_once PATH_MODEL . 'BookingModel.php';
        require_once PATH_MODEL . 'BookingDetailModel.php';
        $bookingModel = new BookingModel();
        $bookingDetailModel = new BookingDetailModel();

        $upcomingTours = $tourModel->getAll(['status' => 'upcoming', 'limit' => 5]);
        require_once PATH_MODEL . 'BookingModel.php';
        $bookingModelForUpcoming = new BookingModel();
        foreach ($upcomingTours as &$ut) {
            $ut['booked_participants'] = $bookingModelForUpcoming->countParticipantsByTourId($ut['id'] ?? $ut['tour_id'] ?? 0);
            $ut['departure_location'] = $ut['departure_location'] ?? ($ut['departure'] ?? null);
        }
        unset($ut);
        $totalTours = $tourModel->count();
        $pendingReports = 0;

        $assignedTours = [];
        $guideId = $_GET['guide_id'] ?? ($_SESSION['user_id'] ?? null);
        if ($guideId) {
            $histories = $historyModel->getByGuideId($guideId);
            $today = date('Y-m-d');
            foreach ($histories as $h) {

                // if (!empty($h['start_date']) && $h['start_date'] < $today && !empty($h['end_date']) && $h['end_date'] < $today) {
                //    continue;
                // }
                $tour = $tourModel->findById($h['tour_id']);
                if (!$tour)
                    continue;
                $schedules = $scheduleModel->getByTourId($h['tour_id']);
                foreach ($schedules as &$s) {
                    if (!empty($s['activities'])) {
                        $decoded = json_decode($s['activities'], true);
                        $s['activities_array'] = is_array($decoded) ? $decoded : [];
                    } else {
                        $s['activities_array'] = [];
                    }
                }
                $bookings = $bookingModel->getAll(['tour_id' => $h['tour_id']]);
                $participants = [];
                foreach ($bookings as $b) {
                    $details = $bookingDetailModel->getByBookingId($b['id']);
                    $participants[] = [
                        'booking' => $b,
                        'details' => $details
                    ];
                }

                $assignedTours[] = [
                    'history' => $h,
                    'tour' => $tour,
                    'schedules' => $schedules,
                    'participants' => $participants,
                ];
            }
        }

        require_once PATH_VIEW . 'hdv/home.php';
    }

    public function tours()
    {
        require_once PATH_MODEL . 'TourModel.php';
        require_once PATH_MODEL . 'GuideTourHistoryModel.php';
        require_once PATH_MODEL . 'TourScheduleModel.php';
        require_once PATH_MODEL . 'BookingModel.php';
        require_once PATH_MODEL . 'BookingDetailModel.php';

        $tourModel = new TourModel();
        $historyModel = new GuideTourHistoryModel();
        $scheduleModel = new TourScheduleModel();
        $bookingModel = new BookingModel();
        $bookingDetailModel = new BookingDetailModel();

        $assignedTours = [];
        $guideId = $_GET['guide_id'] ?? ($_SESSION['user_id'] ?? null);
        if ($guideId) {
            $histories = $historyModel->getByGuideId($guideId);
            $today = date('Y-m-d');
            foreach ($histories as $h) {
                // if (!empty($h['start_date']) && $h['start_date'] < $today && !empty($h['end_date']) && $h['end_date'] < $today) {
                //     continue;
                // }
                $tour = $tourModel->findById($h['tour_id']);
                if (!$tour)
                    continue;
                $schedules = $scheduleModel->getByTourId($h['tour_id']);
                foreach ($schedules as &$s) {
                    if (!empty($s['activities'])) {
                        $decoded = json_decode($s['activities'], true);
                        $s['activities_array'] = is_array($decoded) ? $decoded : [];
                    } else {
                        $s['activities_array'] = [];
                    }
                }

                $bookings = $bookingModel->getAll(['tour_id' => $h['tour_id']]);
                $participants = [];
                foreach ($bookings as $b) {
                    $details = $bookingDetailModel->getByBookingId($b['id']);
                    $participants[] = ['booking' => $b, 'details' => $details];
                }

                $assignedTours[] = [
                    'history' => $h,
                    'tour' => $tour,
                    'schedules' => $schedules,
                    'participants' => $participants,
                ];
            }
        }

        require_once PATH_VIEW . 'hdv/tours.php';
    }

    public function show()
    {
        require_once PATH_MODEL . 'TourModel.php';
        require_once PATH_MODEL . 'TourScheduleModel.php';
        require_once PATH_MODEL . 'BookingModel.php';
        require_once PATH_MODEL . 'BookingDetailModel.php';
        require_once PATH_MODEL . 'GuideTourHistoryModel.php';

        $tourModel = new TourModel();
        $scheduleModel = new TourScheduleModel();
        $bookingModel = new BookingModel();
        $bookingDetailModel = new BookingDetailModel();
        $historyModel = new GuideTourHistoryModel();

        $id = $_GET['id'] ?? 0;
        $guideId = $_GET['guide_id'] ?? ($_SESSION['user_id'] ?? null);

        $tour = $tourModel->findById($id);
        if (!$tour) {
            $_SESSION['error'] = 'Không tìm thấy tour!';
            header('Location: ' . BASE_URL . '?action=hvd/tours');
            exit;
        }

        $schedules = $scheduleModel->getByTourId($id);
        foreach ($schedules as &$s) {
            if (!empty($s['activities'])) {
                $decoded = json_decode($s['activities'], true);
                $s['activities_array'] = is_array($decoded) ? $decoded : [];
            } else {
                $s['activities_array'] = [];
            }
        }
        unset($s);

        $bookings = $bookingModel->getAll(['tour_id' => $id]);
        $participants = [];
        foreach ($bookings as $b) {
            $details = $bookingDetailModel->getByBookingId($b['id']);
            $participants[] = ['booking' => $b, 'details' => $details];

        }

        $assignment = null;
        if ($guideId) {
            $histories = $historyModel->getByGuideId($guideId);
            foreach ($histories as $h) {
                if ((int) $h['tour_id'] === (int) $id) {
                    $assignment = $h;
                    break;
                }
            }
        }

        require_once PATH_VIEW . 'hdv/tour_show.php';
    }

    public function customerEdit()
    {
        require_once PATH_MODEL . 'BookingDetailModel.php';

        $bookingDetailModel = new BookingDetailModel();
        $customerId = $_GET['id'] ?? 0;
        $tour_id = $_GET['tour_id'] ?? 0;
        $guide_id = $_GET['guide_id'] ?? ($_SESSION['user_id'] ?? null);

        $customer = $bookingDetailModel->findById($customerId);
        if (!$customer) {
            $_SESSION['error'] = 'Không tìm thấy thông tin khách hàng!';
            header('Location: ' . BASE_URL . '?action=hvd/tours/customers&id=' . $tour_id . '&guide_id=' . $guide_id);
            exit;
        }

        require_once PATH_VIEW . 'hdv/customer_edit.php';
    }

    public function customerUpdate()
    {
        require_once PATH_MODEL . 'BookingDetailModel.php';

        $bookingDetailModel = new BookingDetailModel();
        $customerId = $_POST['id'] ?? 0;
        $tour_id = $_POST['tour_id'] ?? 0;
        $guide_id = $_POST['guide_id'] ?? ($_SESSION['user_id'] ?? null);

        // Lấy thông tin khách hàng hiện tại
        $currentCustomer = $bookingDetailModel->findById($customerId);
        if (!$currentCustomer) {
            $_SESSION['error'] = 'Không tìm thấy thông tin khách hàng!';
            header('Location: ' . BASE_URL . '?action=hvd/tours/customers&id=' . $tour_id . '&guide_id=' . $guide_id);
            exit;
        }

        // Dữ liệu cập nhật
        $updateData = [
            'fullname' => $_POST['fullname'] ?? '',
            'gender' => $_POST['gender'] ?? null,
            'birthdate' => $_POST['birthdate'] ?? null,
            'phone' => $_POST['phone'] ?? null,
            'id_card' => $_POST['id_card'] ?? null,
            'passport' => $_POST['passport'] ?? null,
            'hobby' => $_POST['hobby'] ?? null,
            'special_requirements' => $_POST['special_requirements'] ?? null,
            'dietary_restrictions' => $_POST['dietary_restrictions'] ?? null
        ];

        // Thực hiện cập nhật
        $result = $bookingDetailModel->update($customerId, $updateData);

        if ($result) {
            $_SESSION['success'] = 'Cập nhật thông tin khách hàng thành công!';
        } else {
            $_SESSION['error'] = 'Cập nhật thông tin khách hàng thất bại!';
        }

        header('Location: ' . BASE_URL . '?action=hvd/tours/customers&id=' . $tour_id . '&guide_id=' . $guide_id);
        exit;
    }
    public function logs()
    {
        require_once PATH_MODEL . 'TourModel.php';
        require_once PATH_MODEL . 'TourLogModel.php';

        $tourModel = new TourModel();
        $logModel = new TourLogModel();

        $tour_id = $_GET['tour_id'] ?? 0;
        $guide_id = $_GET['guide_id'] ?? ($_SESSION['user_id'] ?? null);

        $tour = $tourModel->findById($tour_id);
        if (!$tour) {
            $_SESSION['error'] = 'Không tìm thấy tour!';
            header('Location: ' . BASE_URL . '?action=hvd/tours');
            exit;
        }

        $logs = $logModel->getByTourId($tour_id);

        require_once PATH_VIEW . 'hdv/tour_logs.php';
    }

    public function logStore()
    {
        require_once PATH_MODEL . 'TourLogModel.php';
        $logModel = new TourLogModel();

        $tour_id = $_POST['tour_id'] ?? 0;
        $guide_id = $_POST['guide_id'] ?? ($_SESSION['user_id'] ?? null);
        $content = $_POST['content'] ?? '';
        $log_time = $_POST['log_time'] ?? date('Y-m-d H:i:s');

        $image = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            try {
                $image = upload_file('logs', $_FILES['image']);
            } catch (Exception $e) {
                $_SESSION['error'] = 'Lỗi upload ảnh: ' . $e->getMessage();
            }
        }

        $data = [
            'tour_id' => $tour_id,
            'guide_id' => $guide_id,
            'content' => $content,
            'log_time' => $log_time,
            'image' => $image
        ];

        if ($logModel->create($data)) {
            $_SESSION['success'] = 'Thêm nhật ký thành công!';
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra!';
        }

        header("Location: " . BASE_URL . "?action=hvd/logs&tour_id=$tour_id&guide_id=$guide_id");
        exit;
    }

    public function logUpdate()
    {
        require_once PATH_MODEL . 'TourLogModel.php';
        $logModel = new TourLogModel();

        $id = $_POST['id'] ?? 0;
        $tour_id = $_POST['tour_id'] ?? 0;
        $guide_id = $_POST['guide_id'] ?? 0;
        $content = $_POST['content'] ?? '';
        $log_time = $_POST['log_time'] ?? date('Y-m-d H:i:s');

        $log = $logModel->findById($id);
        if (!$log) {
            $_SESSION['error'] = 'Không tìm thấy nhật ký!';
            header("Location: " . BASE_URL . "?action=hvd/logs&tour_id=$tour_id&guide_id=$guide_id");
            exit;
        }

        $image = $log['image']; // Keep old image by default
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            try {
                $image = upload_file('logs', $_FILES['image']);
            } catch (Exception $e) {
                $_SESSION['error'] = 'Lỗi upload ảnh: ' . $e->getMessage();
            }
        }

        $data = [
            'content' => $content,
            'log_time' => $log_time,
            'image' => $image
        ];

        if ($logModel->update($id, $data)) {
            $_SESSION['success'] = 'Cập nhật nhật ký thành công!';
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra!';
        }

        header("Location: " . BASE_URL . "?action=hvd/logs&tour_id=$tour_id&guide_id=$guide_id");
        exit;
    }

    public function logDelete()
    {
        require_once PATH_MODEL . 'TourLogModel.php';
        $logModel = new TourLogModel();

        $id = $_GET['id'] ?? 0;
        $tour_id = $_GET['tour_id'] ?? 0;
        $guide_id = $_GET['guide_id'] ?? 0;

        if ($logModel->delete($id)) {
            $_SESSION['success'] = 'Xóa nhật ký thành công!';
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra!';
        }

        header("Location: " . BASE_URL . "?action=hvd/logs&tour_id=$tour_id&guide_id=$guide_id");
        exit;
    }

    public function attendance()
    {
        require_once PATH_MODEL . 'TourModel.php';
        require_once PATH_MODEL . 'TourScheduleModel.php';
        require_once PATH_MODEL . 'TourAttendanceModel.php';
        require_once PATH_MODEL . 'GuideTourHistoryModel.php';

        $tourModel = new TourModel();
        $scheduleModel = new TourScheduleModel();
        $attendanceModel = new TourAttendanceModel();
        $historyModel = new GuideTourHistoryModel();

        $tour_id = $_GET['tour_id'] ?? 0;
        $guide_id = $_GET['guide_id'] ?? ($_SESSION['user_id'] ?? null);

        $tour = $tourModel->findById($tour_id);
        if (!$tour) {
            $_SESSION['error'] = 'Không tìm thấy tour!';
            header('Location: ' . BASE_URL . '?action=hvd/tours');
            exit;
        }

        // Tìm lịch phân công để lấy departure_schedule_id
        $histories = $historyModel->getByGuideId($guide_id);
        $assignment = null;
        foreach ($histories as $h) {
            if ((int) $h['tour_id'] === (int) $tour_id) {
                $assignment = $h;
                break;
            }
        }

        // Lấy danh sách các chặng (ngày tour)
        $tourSchedules = $scheduleModel->getByTourId($tour_id);

        // Fallback if no specific schedules are defined for this tour
        if (empty($tourSchedules)) {
            $tourSchedules[] = [
                'id' => 0,
                'day_number' => '1',
                'title' => 'Danh sách chung'
            ];
        }

        // Chặng hiện tại được chọn (qua URL param hoặc mặc định là chặng đầu tiên)
        $selected_schedule_id = $_GET['schedule_id'] ?? ($tourSchedules[0]['id'] ?? 0);

        // Lấy departure_schedule_id từ booking đầu tiên tìm thấy của tour này
        require_once PATH_MODEL . 'BookingModel.php';
        $bookingModel = new BookingModel();
        $sampleBooking = $bookingModel->getAll(['tour_id' => $tour_id, 'limit' => 1]);

        $departure_schedule_id = $sampleBooking[0]['departure_schedule_id'] ?? 0;

        $guests = [];
        // Support fetching guests by tour_id if departure_schedule_id is missing, or by ds_id if present
        // Allow selected_schedule_id to be 0
        if (($departure_schedule_id || $tour_id) && is_numeric($selected_schedule_id)) {
            $guests = $attendanceModel->getGuestListWithStatus($departure_schedule_id, $selected_schedule_id, $tour_id);
        }

        require_once PATH_VIEW . 'hdv/attendance.php';
    }

    public function attendanceStore()
    {
        require_once PATH_MODEL . 'TourAttendanceModel.php';
        $attendanceModel = new TourAttendanceModel();

        $tour_id = $_POST['tour_id'] ?? 0;
        $guide_id = $_POST['guide_id'] ?? 0;
        $departure_schedule_id = !empty($_POST['departure_schedule_id']) ? $_POST['departure_schedule_id'] : null;
        $tour_schedule_id = $_POST['tour_schedule_id'] ?? 0;

        $attendances = $_POST['attendance'] ?? []; // Array: [booking_detail_id => ['status' => ..., 'note' => ...]]

        foreach ($attendances as $booking_detail_id => $data) {
            $saveData = [
                'booking_detail_id' => $booking_detail_id,
                'departure_schedule_id' => $departure_schedule_id,
                'tour_schedule_id' => $tour_schedule_id,
                'status' => $data['status'],
                'note' => $data['note'] ?? '',
                'updated_by' => $guide_id
            ];
            $attendanceModel->saveAttendance($saveData);
        }

        $_SESSION['success'] = 'Cập nhật điểm danh thành công!';
        header("Location: " . BASE_URL . "?action=hvd/attendance&tour_id=$tour_id&guide_id=$guide_id&schedule_id=$tour_schedule_id");
        exit;
    }

    public function finishTour()
    {
        require_once PATH_MODEL . 'GuideTourHistoryModel.php';
        $historyModel = new GuideTourHistoryModel();

        $tour_id = $_GET['tour_id'] ?? 0;
        $guide_id = $_GET['guide_id'] ?? ($_SESSION['user_id'] ?? null);

        // Find assignment ID
        $histories = $historyModel->getByGuideId($guide_id);
        $assignment = null;
        foreach ($histories as $h) {
            if ((int) $h['tour_id'] === (int) $tour_id) {
                $assignment = $h;
                break;
            }
        }

        if ($assignment) {
            $historyModel->updateStatus($assignment['id'], 'completed');
            $_SESSION['success'] = 'Xác nhận hoàn thành tour thành công!';
        } else {
            $_SESSION['error'] = 'Không tìm thấy phân công tour!';
        }

        header('Location: ' . BASE_URL . '?action=hvd/tours/show&id=' . $tour_id . '&guide_id=' . $guide_id);
        exit;
    }

    public function customers()
    {
        require_once PATH_MODEL . 'TourModel.php';
        require_once PATH_MODEL . 'GuideTourHistoryModel.php';
        require_once PATH_MODEL . 'BookingModel.php';
        require_once PATH_MODEL . 'BookingDetailModel.php';

        $tourModel = new TourModel();
        $historyModel = new GuideTourHistoryModel();
        $bookingModel = new BookingModel();
        $bookingDetailModel = new BookingDetailModel();

        $guideId = $_GET['guide_id'] ?? ($_SESSION['user_id'] ?? 0);

        // Fetch assigned tours
        $histories = $historyModel->getByGuideId($guideId);
        $assignedTours = [];

        foreach ($histories as $h) {
            $tour = $tourModel->findById($h['tour_id']);
            if ($tour) {
                // Calculate guest count
                $bookings = $bookingModel->getAll(['tour_id' => $tour['id']]);
                $guestCount = 0;
                $specialNeedsCount = 0;

                foreach ($bookings as $b) {
                    if (($b['status'] ?? '') === 'cancelled')
                        continue;
                    $details = $bookingDetailModel->getByBookingId($b['id']);
                    $guestCount += count($details);

                    // Count special needs
                    foreach ($details as $d) {
                        if (!empty($d['dietary_restrictions']) || !empty($d['special_requirements']) || !empty($d['hobby'])) {
                            $specialNeedsCount++;
                        }
                    }
                }

                $tour['real_guest_count'] = $guestCount;
                $tour['special_needs_count'] = $specialNeedsCount;

                $assignedTours[] = ['tour' => $tour, 'history' => $h];
            }
        }

        // Sort: Ongoing -> Upcoming -> Completed
        usort($assignedTours, function ($a, $b) {
            $statusA = $a['history']['status'] ?? '';
            $statusB = $b['history']['status'] ?? '';

            // Define priority: ongoing > upcoming > others
            $today = date('Y-m-d');

            $isOngoingA = ($statusA !== 'completed' && ($a['history']['start_date'] ?? '') <= $today && ($a['history']['end_date'] ?? '') >= $today) ? 1 : 0;
            $isOngoingB = ($statusB !== 'completed' && ($b['history']['start_date'] ?? '') <= $today && ($b['history']['end_date'] ?? '') >= $today) ? 1 : 0;

            if ($isOngoingA !== $isOngoingB)
                return $isOngoingB - $isOngoingA; // Descending

            return strcmp($b['history']['start_date'] ?? '', $a['history']['start_date'] ?? '');
        });

        require_once PATH_VIEW . 'hdv/customers.php';
    }

    public function tourCustomers()
    {
        require_once PATH_MODEL . 'TourModel.php';
        require_once PATH_MODEL . 'BookingModel.php';
        require_once PATH_MODEL . 'BookingDetailModel.php';
        require_once PATH_MODEL . 'GuideTourHistoryModel.php';

        $tourModel = new TourModel();
        $bookingModel = new BookingModel();
        $bookingDetailModel = new BookingDetailModel();
        $historyModel = new GuideTourHistoryModel();

        $id = $_GET['id'] ?? 0;
        $guideId = $_GET['guide_id'] ?? ($_SESSION['user_id'] ?? 0);

        $tour = $tourModel->findById($id);
        if (!$tour) {
            $_SESSION['error'] = 'Không tìm thấy thông tin tour!';
            header('Location: ' . BASE_URL . '?action=hvd/customers');
            exit;
        }

        // Get participants
        $bookings = $bookingModel->getAll(['tour_id' => $id]);
        $participants = [];
        foreach ($bookings as $b) {
            // Include cancelled bookings too but mark them? Or filter?
            // User context: "Manage customers". Usually wants to see everyone or valid ones.
            // Let's filter cancelled for this management view to reduce noise, unless user specifically asked.
            // In tour_show we show them. Let's show them here too for consistency, with visual indicator.
            $details = $bookingDetailModel->getByBookingId($b['id']);
            $participants[] = ['booking' => $b, 'details' => $details];
        }

        // Get assignment info for status check
        $assignment = null;
        $histories = $historyModel->getByGuideId($guideId);
        foreach ($histories as $h) {
            if ((int) $h['tour_id'] === (int) $id) {
                $assignment = $h;
                break;
            }
        }

        require_once PATH_VIEW . 'hdv/tour_customers.php';
    }

    public function schedule()
    {
        require_once PATH_MODEL . 'GuideTourHistoryModel.php';

        $tourModel = new TourModel();
        $historyModel = new GuideTourHistoryModel();

        $guideId = $_GET['guide_id'] ?? ($_SESSION['user_id'] ?? null);
        $assignedTours = [];

        if ($guideId) {
            $histories = $historyModel->getByGuideId($guideId); // Get all history
            foreach ($histories as $h) {
                $tour = $tourModel->findById($h['tour_id']);
                if ($tour) {
                    $assignedTours[] = [
                        'history' => $h,
                        'tour' => $tour
                    ];
                }
            }
        }

        // Calendar Logic
        $month = isset($_GET['month']) ? (int) $_GET['month'] : (int) date('m');
        $year = isset($_GET['year']) ? (int) $_GET['year'] : (int) date('Y');

        // Navigation
        $prevMonth = $month - 1;
        $prevYear = $year;
        if ($prevMonth < 1) {
            $prevMonth = 12;
            $prevYear--;
        }

        $nextMonth = $month + 1;
        $nextYear = $year;
        if ($nextMonth > 12) {
            $nextMonth = 1;
            $nextYear++;
        }

        require_once PATH_VIEW . 'hdv/schedule.php';
    }
}
