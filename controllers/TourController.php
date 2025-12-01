<?php

class TourController
{
    private $tourModel;
    private $scheduleModel;
    private $imageModel;
    private $priceModel;
    private $policyModel;
    private $supplierModel;
    private $tourSupplierModel;

    public function __construct()
    {
        $this->tourModel = new TourModel();
        $this->scheduleModel = new TourScheduleModel();
        $this->imageModel = new TourImageModel();
        $this->priceModel = new TourPriceModel();
        $this->policyModel = new TourPolicyModel();
        $this->supplierModel = new SupplierModel();
        $this->tourSupplierModel = new TourSupplierModel();
    }

    /**
     * Danh sách tours
     */
    public function index()
    {
        $filters = [
            'category' => $_GET['category'] ?? '',
            'status' => isset($_GET['status']) ? (int)$_GET['status'] : null,
            'search' => $_GET['search'] ?? ''
        ];

        // Loại bỏ các filter rỗng
        $filters = array_filter($filters, function($value) {
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
    public function create()
    {
        $categories = TourModel::getCategories();
        $title = 'Tạo Tour Mới';
        $view = 'tour/create';
        require_once PATH_VIEW_ADMIN.'main.php';
    }

    /**
     * Xử lý tạo tour mới
     */
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?action=tours');
            exit;
        }

        $data = [
            'name' => $_POST['name'] ?? '',
            'code' => $_POST['code'] ?? '',
            'category' => $_POST['category'] ?? '',
            'description' => $_POST['description'] ?? '',
            'duration' => (int)($_POST['duration'] ?? 0),
            'price' => (float)($_POST['price'] ?? 0),
            'max_participants' => !empty($_POST['max_participants']) ? (int)$_POST['max_participants'] : null,
            'departure_location' => $_POST['departure_location'] ?? '',
            'destination' => $_POST['destination'] ?? '',
            'status' => isset($_POST['status']) ? (int)$_POST['status'] : 1,
            'created_by' => 1 // Default user ID
        ];

        // Validate
        $errors = $this->validateTour($data);

        // Xử lý upload ảnh
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            try {
                $data['image'] = upload_file('tours', $_FILES['image']);
            } catch (Exception $e) {
                $errors[] = $e->getMessage();
            }
        }

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

        $tourId = $this->tourModel->create($data);

        if ($tourId) {
            $_SESSION['success'] = 'Tạo tour thành công!';
            header('Location: ' . BASE_URL . '?action=tours');
            exit;
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi tạo tour!';
            header('Location: ' . BASE_URL . '?action=tours/create');
            exit;
        }
    }

    /**
     * Hiển thị form chỉnh sửa tour
     */
    public function edit()
    {
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
        require_once PATH_VIEW_ADMIN.'main.php';
    }

    /**
     * Xử lý cập nhật tour
     */
    public function update()
    {
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
            'duration' => (int)($_POST['duration'] ?? 0),
            'price' => (float)($_POST['price'] ?? 0),
            'max_participants' => !empty($_POST['max_participants']) ? (int)$_POST['max_participants'] : null,
            'departure_location' => $_POST['departure_location'] ?? '',
            'destination' => $_POST['destination'] ?? '',
            'status' => isset($_POST['status']) ? (int)$_POST['status'] : 1,
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
        $images = $this->imageModel->getByTourId($id);
        $prices = $this->priceModel->getByTourId($id);
        $policies = $this->policyModel->getByTourId($id);
        $suppliers = $this->supplierModel->getByTourId($id);
        
        // Nhóm chính sách theo loại
        $policiesByType = [];
        foreach ($policies as $policy) {
            $policiesByType[$policy['policy_type']][] = $policy;
        }

        // Nhóm nhà cung cấp theo loại
        $suppliersByType = [];
        foreach ($suppliers as $supplier) {
            $type = $supplier['supplier_type'] ?? 'other';
            $suppliersByType[$type][] = $supplier;
        }

        $categories = TourModel::getCategories();

        $title = 'Chi tiết Tour - ' . $tour['name'];
        $view = 'tour/show';
        require_once PATH_VIEW_ADMIN.'main.php';
    }

    /**
     * Xóa tour
     */
    public function delete()
    {
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

