<?php

class AdditionalServiceController
{
    private $serviceModel;

    public function __construct()
    {
        $this->serviceModel = new AdditionalServiceModel();
    }

    /**
     * Danh sách dịch vụ
     */
    public function index()
    {
        $filters = [
            'search' => $_GET['search'] ?? '',
            'status' => $_GET['status'] ?? ''
        ];

        $services = $this->serviceModel->getAll($filters);

        $title = 'Quản lý Dịch vụ thêm';
        $view = 'additional-services/index';
        require_once PATH_VIEW_ADMIN . 'main.php';
    }

    /**
     * Form tạo mới
     */
    public function create()
    {
        $title = 'Thêm Dịch vụ mới';
        $view = 'additional-services/create';
        require_once PATH_VIEW_ADMIN . 'main.php';
    }

    /**
     * Xử lý tạo mới
     */
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?action=additional-services');
            exit;
        }

        $data = [
            'name' => $_POST['name'] ?? '',
            'description' => $_POST['description'] ?? '',
            'price' => $_POST['price'] ?? 0,
            'status' => $_POST['status'] ?? 1
        ];

        if (empty($data['name'])) {
            $_SESSION['error'] = 'Tên dịch vụ không được để trống!';
            header('Location: ' . BASE_URL . '?action=additional-services/create');
            exit;
        }

        $result = $this->serviceModel->create($data);

        if ($result) {
            $_SESSION['success'] = 'Thêm dịch vụ thành công!';
            header('Location: ' . BASE_URL . '?action=additional-services');
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra!';
            header('Location: ' . BASE_URL . '?action=additional-services/create');
        }
    }

    /**
     * Form chỉnh sửa
     */
    public function edit()
    {
        $id = $_GET['id'] ?? 0;
        $service = $this->serviceModel->findById($id);

        if (!$service) {
            $_SESSION['error'] = 'Không tìm thấy dịch vụ!';
            header('Location: ' . BASE_URL . '?action=additional-services');
            exit;
        }

        $title = 'Sửa Dịch vụ';
        $view = 'additional-services/edit';
        require_once PATH_VIEW_ADMIN . 'main.php';
    }

    /**
     * Xử lý cập nhật
     */
    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?action=additional-services');
            exit;
        }

        $id = $_POST['id'] ?? 0;
        $data = [
            'name' => $_POST['name'] ?? '',
            'description' => $_POST['description'] ?? '',
            'price' => $_POST['price'] ?? 0,
            'status' => $_POST['status'] ?? 1
        ];

        $result = $this->serviceModel->update($id, $data);

        if ($result) {
            $_SESSION['success'] = 'Cập nhật thành công!';
            header('Location: ' . BASE_URL . '?action=additional-services');
        } else {
            $_SESSION['error'] = 'Lỗi cập nhật!';
            header('Location: ' . BASE_URL . '?action=additional-services/edit&id=' . $id);
        }
    }

    /**
     * Xóa
     */
    public function delete()
    {
        $id = $_GET['id'] ?? 0;
        $this->serviceModel->delete($id);
        $_SESSION['success'] = 'Đã xóa dịch vụ!';
        header('Location: ' . BASE_URL . '?action=additional-services');
    }
}
