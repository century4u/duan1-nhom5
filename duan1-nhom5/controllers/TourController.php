<?php

class TourController
{
    private $tourModel;
    private $scheduleModel;
    private $imageModel;
    private $priceModel;
    private $policyModel;

    public function __construct()
    {
        $this->tourModel = new TourModel();
        $this->scheduleModel = new TourScheduleModel();
        $this->imageModel = new TourImageModel();
        $this->priceModel = new TourPriceModel();
        $this->policyModel = new TourPolicyModel();
    }

    /**
     * Danh sách tours
     */
    public function index()
    {
        $filters = [
            'category' => $_GET['category'] ?? '',
            'status' => isset($_GET['status']) ? (int) $_GET['status'] : null,
            'search' => $_GET['search'] ?? ''
        ];

        // Loại bỏ các filter rỗng
        $filters = array_filter($filters, function ($value) {
            return $value !== '' && $value !== null;
        });

        $tours = $this->tourModel->getAll($filters);
        $categories = TourModel::getCategories();

        $title = 'Quản lý Tour';
        $view = 'tour/index';
        require_once PATH_VIEW_ADMIN . 'main.php';
    }

    /**
     * Hiển thị form tạo tour mới
     */
    /**
     * Hiển thị form tạo tour mới
     */
    public function create()
    {
        requireAdmin();
        $categories = TourModel::getCategories();
        $title = 'Tạo Tour Mới';
        $view = 'tour/create';
        require_once PATH_VIEW_ADMIN . 'main.php';
    }

    /**
     * Xử lý tạo tour mới
     */
    public function store()
    {
        requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?action=tours');
            exit;
        }

        $data = [
            'name' => $_POST['name'] ?? '',
            'code' => $_POST['code'] ?? '',
            'category' => $_POST['category'] ?? '',
            'description' => $_POST['description'] ?? '',
            'duration' => (int) ($_POST['duration'] ?? 0),
            'price' => (float) ($_POST['price'] ?? 0),
            'max_participants' => !empty($_POST['max_participants']) ? (int) $_POST['max_participants'] : null,
            'departure_location' => $_POST['departure_location'] ?? '',
            'destination' => $_POST['destination'] ?? '',
            'status' => isset($_POST['status']) ? (int) $_POST['status'] : 1,
            'created_by' => 1 // Default user ID
        ];

        // Validate
        $errors = $this->validateTour($data);

        // Kiểm tra code đã tồn tại chưa
        if (empty($errors)) {
            $existingTour = $this->tourModel->findByCode($data['code']);
            if ($existingTour) {
                $errors[] = 'Mã tour đã tồn tại!';
            }
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old_data'] = $data;
            header('Location: ' . BASE_URL . '?action=tours/create');
            exit;
        }

        // Tạo tour trước (không cần image trong data nữa)
        $data['image'] = null;
        $tourId = $this->tourModel->create($data);

        if (!$tourId) {
            $_SESSION['error'] = 'Có lỗi xảy ra khi tạo tour!';
            header('Location: ' . BASE_URL . '?action=tours/create');
            exit;
        }

        // Xử lý upload nhiều ảnh
        if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
            foreach ($_FILES['images']['name'] as $key => $name) {
                if ($_FILES['images']['error'][$key] === UPLOAD_ERR_OK) {
                    $file = [
                        'name' => $_FILES['images']['name'][$key],
                        'type' => $_FILES['images']['type'][$key],
                        'tmp_name' => $_FILES['images']['tmp_name'][$key],
                        'error' => $_FILES['images']['error'][$key],
                        'size' => $_FILES['images']['size'][$key]
                    ];

                    try {
                        $imagePath = upload_file('tours', $file);
                        $this->imageModel->create([
                            'tour_id' => $tourId,
                            'image_url' => BASE_ASSETS_UPLOADS . $imagePath,
                            'image_path' => $imagePath,
                            'caption' => null,
                            'is_primary' => ($key === 0) ? 1 : 0, // Ảnh đầu tiên là ảnh chính
                            'sort_order' => $key
                        ]);

                        // Cập nhật ảnh chính vào tour (ảnh đầu tiên)
                        if ($key === 0) {
                            $this->tourModel->update($tourId, ['image' => $imagePath]);
                        }
                    } catch (Exception $e) {
                        // Log error nhưng không dừng quá trình
                        error_log("Failed to upload image: " . $e->getMessage());
                    }
                }
            }
        }

        $_SESSION['success'] = 'Tạo tour thành công!';
        header('Location: ' . BASE_URL . '?action=tours/show&id=' . $tourId);
        exit;
    }

    /**
     * Hiển thị form chỉnh sửa tour
     */
    public function edit()
    {
        requireAdmin();
        $id = $_GET['id'] ?? 0;
        $tour = $this->tourModel->findById($id);

        if (!$tour) {
            $_SESSION['error'] = 'Không tìm thấy tour!';
            header('Location: ' . BASE_URL . '?action=tours');
            exit;
        }

        $categories = TourModel::getCategories();
        $title = 'Chỉnh sửa Tour';
        $view = 'tour/edit';
        require_once PATH_VIEW_ADMIN . 'main.php';
    }

    /**
     * Xử lý cập nhật tour
     */
    public function update()
    {
        requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?action=tours');
            exit;
        }

        $id = $_POST['id'] ?? 0;
        $tour = $this->tourModel->findById($id);

        if (!$tour) {
            $_SESSION['error'] = 'Không tìm thấy tour!';
            header('Location: ' . BASE_URL . '?action=tours');
            exit;
        }

        $data = [
            'name' => $_POST['name'] ?? '',
            'code' => $_POST['code'] ?? '',
            'category' => $_POST['category'] ?? '',
            'description' => $_POST['description'] ?? '',
            'duration' => (int) ($_POST['duration'] ?? 0),
            'price' => (float) ($_POST['price'] ?? 0),
            'max_participants' => !empty($_POST['max_participants']) ? (int) $_POST['max_participants'] : null,
            'departure_location' => $_POST['departure_location'] ?? '',
            'destination' => $_POST['destination'] ?? '',
            'status' => isset($_POST['status']) ? (int) $_POST['status'] : 1,
            'image' => $tour['image'] // Giữ nguyên ảnh cũ nếu không upload mới
        ];

        // Validate
        $errors = $this->validateTour($data, $id);

        // Xử lý upload ảnh mới
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            try {
                // Xóa ảnh cũ nếu có
                if (!empty($tour['image']) && file_exists(PATH_ASSETS_UPLOADS . $tour['image'])) {
                    unlink(PATH_ASSETS_UPLOADS . $tour['image']);
                }
                $data['image'] = upload_file('tours', $_FILES['image']);
            } catch (Exception $e) {
                $errors[] = $e->getMessage();
            }
        }

        // Kiểm tra code đã tồn tại chưa (trừ tour hiện tại)
        if (empty($errors) && $data['code'] !== $tour['code']) {
            $existingTour = $this->tourModel->findByCode($data['code']);
            if ($existingTour) {
                $errors[] = 'Mã tour đã tồn tại!';
            }
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old_data'] = $data;
            header('Location: ' . BASE_URL . '?action=tours/edit&id=' . $id);
            exit;
        }

        $result = $this->tourModel->update($id, $data);

        if ($result) {
            $_SESSION['success'] = 'Cập nhật tour thành công!';
            header('Location: ' . BASE_URL . '?action=tours');
            exit;
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi cập nhật tour!';
            header('Location: ' . BASE_URL . '?action=tours/edit&id=' . $id);
            exit;
        }
    }

    /**
     * Xem thông tin chi tiết tour
     */
    public function show()
    {
        $id = $_GET['id'] ?? 0;
        $tour = $this->tourModel->findById($id);

        if (!$tour) {
            $_SESSION['error'] = 'Không tìm thấy tour!';
            header('Location: ' . BASE_URL . '?action=tours');
            exit;
        }

        // Lấy các thông tin chi tiết
        $schedules = $this->scheduleModel->getByTourId($id);

        // Lấy lịch khởi hành (Departure Schedules)
        $departureScheduleModel = new DepartureScheduleModel();
        $departureSchedules = $departureScheduleModel->getAll(['tour_id' => $id, 'status' => 'confirmed']); // Lấy lịch đã xác nhận

        $images = $this->imageModel->getByTourId($id);
        $prices = $this->priceModel->getByTourId($id);
        $policies = $this->policyModel->getByTourId($id);

        // Nhóm chính sách theo loại
        $policiesByType = [];
        foreach ($policies as $policy) {
            $policiesByType[$policy['policy_type']][] = $policy;
        }

        $categories = TourModel::getCategories();

        $title = 'Chi tiết Tour - ' . $tour['name'];
        $view = 'tour/show';
        require_once PATH_VIEW_ADMIN . 'main.php';
    }

    /**
     * Xóa tour
     */
    public function delete()
    {
        requireAdmin();
        $id = $_GET['id'] ?? 0;
        $tour = $this->tourModel->findById($id);

        if (!$tour) {
            $_SESSION['error'] = 'Không tìm thấy tour!';
            header('Location: ' . BASE_URL . '?action=tours');
            exit;
        }

        $result = $this->tourModel->delete($id);

        if ($result) {
            $_SESSION['success'] = 'Xóa tour thành công!';
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi xóa tour!';
        }

        header('Location: ' . BASE_URL . '?action=tours');
        exit;
    }

    /**
     * Validate dữ liệu tour
     */
    private function validateTour($data, $excludeId = null)
    {
        $errors = [];

        if (empty($data['name'])) {
            $errors[] = 'Tên tour không được để trống!';
        }

        if (empty($data['code'])) {
            $errors[] = 'Mã tour không được để trống!';
        }

        if (!in_array($data['category'], ['domestic', 'international', 'customized'])) {
            $errors[] = 'Loại tour không hợp lệ!';
        }

        if ($data['duration'] <= 0) {
            $errors[] = 'Số ngày phải lớn hơn 0!';
        }

        if ($data['price'] <= 0) {
            $errors[] = 'Giá tour phải lớn hơn 0!';
        }

        if (empty($data['departure_location'])) {
            $errors[] = 'Điểm khởi hành không được để trống!';
        }

        if (empty($data['destination'])) {
            $errors[] = 'Điểm đến không được để trống!';
        }

        return $errors;
    }
}

