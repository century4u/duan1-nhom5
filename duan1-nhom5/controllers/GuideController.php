<?php

class GuideController
{
    private $guideModel;
    private $tourHistoryModel;
    private $availabilityModel;

    public function __construct()
    {
        $this->guideModel = new GuideModel();
        $this->tourHistoryModel = new GuideTourHistoryModel();
        $this->availabilityModel = new GuideAvailabilityModel();
    }

    /**
     * Danh sách HDV
     */
    public function index()
    {
        $filters = [
            'specialization' => $_GET['specialization'] ?? '',
            'status' => isset($_GET['status']) ? (int)$_GET['status'] : null,
            'search' => $_GET['search'] ?? ''
        ];

        // Loại bỏ các filter rỗng
        $filters = array_filter($filters, function($value) {
            return $value !== '' && $value !== null;
        });

        $guides = $this->guideModel->getAll($filters);
        
        // Thêm thông tin bổ sung cho mỗi HDV
        foreach ($guides as &$guide) {
            $guide['tours_count'] = $this->guideModel->countTours($guide['id']);
            $ratingInfo = $this->tourHistoryModel->getAverageRating($guide['id']);
            $guide['average_rating'] = $ratingInfo['avg_rating'] ?? null;
            $guide['total_tours'] = $ratingInfo['total_tours'] ?? 0;
            
            // Parse languages từ JSON
            if (!empty($guide['languages'])) {
                $guide['languages_array'] = json_decode($guide['languages'], true);
            } else {
                $guide['languages_array'] = [];
            }
        }

        $specializations = GuideModel::getSpecializations();

        $title = 'Quản lý Hướng dẫn viên';
        $view = 'guide/index';
        require_once PATH_VIEW_ADMIN.'main.php';
    }

    /**
     * Hiển thị form tạo HDV mới
     */
    public function create()
    {
        $specializations = GuideModel::getSpecializations();
        $languages = GuideModel::getCommonLanguages();

        $title = 'Tạo HDV Mới';
        $view = 'guide/create';
        require_once PATH_VIEW_ADMIN.'main.php';
    }

    /**
     * Xử lý tạo HDV mới
     */
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?action=guides/create');
            exit;
        }

        $data = [
            'code' => $_POST['code'] ?? '',
            'full_name' => $_POST['full_name'] ?? '',
            'birthdate' => $_POST['birthdate'] ?? null,
            'gender' => $_POST['gender'] ?? null,
            'phone' => $_POST['phone'] ?? '',
            'email' => $_POST['email'] ?? '',
            'address' => $_POST['address'] ?? '',
            'id_card' => $_POST['id_card'] ?? null,
            'passport' => $_POST['passport'] ?? null,
            'languages' => $_POST['languages'] ?? [],
            'certificates' => $_POST['certificates'] ?? '',
            'experience_years' => (int)($_POST['experience_years'] ?? 0),
            'experience_description' => $_POST['experience_description'] ?? '',
            'specialization' => $_POST['specialization'] ?? 'mixed',
            'performance_rating' => !empty($_POST['performance_rating']) ? (float)$_POST['performance_rating'] : null,
            'health_status' => $_POST['health_status'] ?? 'good',
            'health_notes' => $_POST['health_notes'] ?? '',
            'status' => isset($_POST['status']) ? (int)$_POST['status'] : 1
        ];

        // Validate
        $errors = $this->validateGuide($data);

        // Xử lý upload ảnh
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            try {
                $data['avatar'] = upload_file('guides', $_FILES['avatar']);
            } catch (Exception $e) {
                $errors[] = $e->getMessage();
            }
        }

        // Kiểm tra code đã tồn tại chưa
        if (empty($errors)) {
            $existingGuide = $this->guideModel->findByCode($data['code']);
            if ($existingGuide) {
                $errors[] = 'Mã HDV đã tồn tại!';
            }
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old_data'] = $data;
            header('Location: ' . BASE_URL . '?action=guides/create');
            exit;
        }

        $guideId = $this->guideModel->create($data);

        if ($guideId) {
            $_SESSION['success'] = 'Tạo HDV thành công!';
            header('Location: ' . BASE_URL . '?action=guides');
            exit;
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi tạo HDV!';
            header('Location: ' . BASE_URL . '?action=guides/create');
            exit;
        }
    }

    /**
     * Hiển thị chi tiết HDV
     */
    public function show()
    {
        $id = $_GET['id'] ?? 0;
        $guide = $this->guideModel->findById($id);

        if (!$guide) {
            $_SESSION['error'] = 'Không tìm thấy HDV!';
            header('Location: ' . BASE_URL . '?action=guides');
            exit;
        }

        // Parse languages từ JSON
        if (!empty($guide['languages'])) {
            $guide['languages_array'] = json_decode($guide['languages'], true);
        } else {
            $guide['languages_array'] = [];
        }

        // Lấy lịch sử dẫn tour
        $tourHistory = $this->tourHistoryModel->getByGuideId($id);
        $guide['tour_history'] = $tourHistory;
        $guide['tours_count'] = count($tourHistory);

        // Lấy đánh giá trung bình
        $ratingInfo = $this->tourHistoryModel->getAverageRating($id);
        $guide['average_rating'] = $ratingInfo['avg_rating'] ?? null;
        $guide['rated_tours_count'] = $ratingInfo['total_tours'] ?? 0;

        // Lấy lịch làm việc (30 ngày tới)
        $startDate = date('Y-m-d');
        $endDate = date('Y-m-d', strtotime('+30 days'));
        $availability = $this->availabilityModel->getByGuideId($id, $startDate, $endDate);
        $guide['availability'] = $availability;

        $specializations = GuideModel::getSpecializations();
        $languages = GuideModel::getCommonLanguages();

        $title = 'Chi tiết HDV - ' . $guide['full_name'];
        $view = 'guide/show';
        require_once PATH_VIEW_ADMIN.'main.php';
    }

    /**
     * Hiển thị form chỉnh sửa HDV
     */
    public function edit()
    {
        $id = $_GET['id'] ?? 0;
        $guide = $this->guideModel->findById($id);

        if (!$guide) {
            $_SESSION['error'] = 'Không tìm thấy HDV!';
            header('Location: ' . BASE_URL . '?action=guides');
            exit;
        }

        // Parse languages từ JSON
        if (!empty($guide['languages'])) {
            $guide['languages_array'] = json_decode($guide['languages'], true);
        } else {
            $guide['languages_array'] = [];
        }

        $specializations = GuideModel::getSpecializations();
        $languages = GuideModel::getCommonLanguages();

        $title = 'Chỉnh sửa HDV';
        $view = 'guide/edit';
        require_once PATH_VIEW_ADMIN.'main.php';
    }

    /**
     * Xử lý cập nhật HDV
     */
    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?action=guides');
            exit;
        }

        $id = $_POST['id'] ?? 0;
        $guide = $this->guideModel->findById($id);

        if (!$guide) {
            $_SESSION['error'] = 'Không tìm thấy HDV!';
            header('Location: ' . BASE_URL . '?action=guides');
            exit;
        }

        $data = [
            'code' => $_POST['code'] ?? '',
            'full_name' => $_POST['full_name'] ?? '',
            'birthdate' => $_POST['birthdate'] ?? null,
            'gender' => $_POST['gender'] ?? null,
            'phone' => $_POST['phone'] ?? '',
            'email' => $_POST['email'] ?? '',
            'address' => $_POST['address'] ?? '',
            'id_card' => $_POST['id_card'] ?? null,
            'passport' => $_POST['passport'] ?? null,
            'languages' => $_POST['languages'] ?? [],
            'certificates' => $_POST['certificates'] ?? '',
            'experience_years' => (int)($_POST['experience_years'] ?? 0),
            'experience_description' => $_POST['experience_description'] ?? '',
            'specialization' => $_POST['specialization'] ?? 'mixed',
            'performance_rating' => !empty($_POST['performance_rating']) ? (float)$_POST['performance_rating'] : null,
            'health_status' => $_POST['health_status'] ?? 'good',
            'health_notes' => $_POST['health_notes'] ?? '',
            'status' => isset($_POST['status']) ? (int)$_POST['status'] : 1,
            'avatar' => $guide['avatar'] // Giữ nguyên ảnh cũ nếu không upload mới
        ];

        // Validate
        $errors = $this->validateGuide($data, $id);

        // Xử lý upload ảnh mới
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            try {
                // Xóa ảnh cũ nếu có
                if (!empty($guide['avatar']) && file_exists(PATH_ASSETS_UPLOADS . $guide['avatar'])) {
                    unlink(PATH_ASSETS_UPLOADS . $guide['avatar']);
                }
                $data['avatar'] = upload_file('guides', $_FILES['avatar']);
            } catch (Exception $e) {
                $errors[] = $e->getMessage();
            }
        }

        // Kiểm tra code đã tồn tại chưa (trừ HDV hiện tại)
        if (empty($errors) && $data['code'] !== $guide['code']) {
            $existingGuide = $this->guideModel->findByCode($data['code']);
            if ($existingGuide) {
                $errors[] = 'Mã HDV đã tồn tại!';
            }
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old_data'] = $data;
            header('Location: ' . BASE_URL . '?action=guides/edit&id=' . $id);
            exit;
        }

        $result = $this->guideModel->update($id, $data);

        if ($result) {
            $_SESSION['success'] = 'Cập nhật HDV thành công!';
            header('Location: ' . BASE_URL . '?action=guides/show&id=' . $id);
            exit;
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi cập nhật HDV!';
            header('Location: ' . BASE_URL . '?action=guides/edit&id=' . $id);
            exit;
        }
    }

    /**
     * Xóa HDV
     */
    public function delete()
    {
        $id = $_GET['id'] ?? 0;
        $guide = $this->guideModel->findById($id);

        if (!$guide) {
            $_SESSION['error'] = 'Không tìm thấy HDV!';
            header('Location: ' . BASE_URL . '?action=guides');
            exit;
        }

        $result = $this->guideModel->delete($id);

        if ($result) {
            $_SESSION['success'] = 'Xóa HDV thành công!';
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi xóa HDV!';
        }

        header('Location: ' . BASE_URL . '?action=guides');
        exit;
    }

    /**
     * Validate dữ liệu HDV
     */
    private function validateGuide($data, $excludeId = null)
    {
        $errors = [];

        if (empty($data['code'])) {
            $errors[] = 'Mã HDV không được để trống!';
        }

        if (empty($data['full_name'])) {
            $errors[] = 'Họ và tên không được để trống!';
        }

        $specializations = GuideModel::getSpecializations();
        if (!isset($specializations[$data['specialization']])) {
            $errors[] = 'Chuyên môn không hợp lệ!';
        }

        if ($data['experience_years'] < 0) {
            $errors[] = 'Số năm kinh nghiệm phải >= 0!';
        }

        if ($data['performance_rating'] !== null && ($data['performance_rating'] < 0 || $data['performance_rating'] > 5)) {
            $errors[] = 'Đánh giá năng lực phải từ 0 đến 5!';
        }

        return $errors;
    }
}
