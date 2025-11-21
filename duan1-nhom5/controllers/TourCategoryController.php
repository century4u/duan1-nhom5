<?php

class TourCategoryController
{
    private $tourModel;

    public function __construct()
    {
        $this->tourModel = new TourModel();
    }

    /**
     * Trang quản lý danh mục tour
     */
    public function index()
    {
        $categories = TourModel::getCategories();
        
        // Lấy thống kê số lượng tour theo từng danh mục
        $categoryStats = [];
        foreach ($categories as $key => $label) {
            $categoryStats[$key] = [
                'name' => $label,
                'count' => $this->tourModel->count(['category' => $key, 'status' => 1]),
                'description' => $this->getCategoryDescription($key)
            ];
        }

        $title = 'Quản lý Danh mục Tour';
        $view = 'tour-category/index';
        require_once PATH_VIEW_ADMIN;
    }

    /**
     * Lấy mô tả chi tiết của từng danh mục
     */
    private function getCategoryDescription($category)
    {
        $descriptions = [
            'domestic' => 'Tour tham quan, du lịch các địa điểm trong nước. Khám phá vẻ đẹp của quê hương Việt Nam với các điểm đến nổi tiếng như Hạ Long, Sapa, Đà Lạt, Phú Quốc...',
            'international' => 'Tour tham quan, du lịch các nước ngoài. Trải nghiệm văn hóa, ẩm thực và cảnh quan tuyệt đẹp của các quốc gia trên thế giới.',
            'customized' => 'Tour thiết kế riêng dựa trên yêu cầu cụ thể của từng khách hàng/đoàn khách. Lịch trình linh hoạt, phù hợp với nhu cầu và sở thích cá nhân.'
        ];

        return $descriptions[$category] ?? '';
    }

    /**
     * Xem danh sách tour theo danh mục
     */
    public function viewTours()
    {
        $category = $_GET['category'] ?? '';
        $categories = TourModel::getCategories();

        if (!isset($categories[$category])) {
            $_SESSION['error'] = 'Danh mục tour không hợp lệ!';
            header('Location: ' . BASE_URL . '?action=tour-categories');
            exit;
        }

        $filters = [
            'category' => $category,
            'status' => isset($_GET['status']) ? (int)$_GET['status'] : null,
            'search' => $_GET['search'] ?? ''
        ];

        // Loại bỏ các filter rỗng
        $filters = array_filter($filters, function($value) {
            return $value !== '' && $value !== null;
        });

        $tours = $this->tourModel->getAll($filters);
        $categoryName = $categories[$category];

        $title = 'Danh sách Tour - ' . $categoryName;
        $breadcrumb = [
            ['name' => 'Danh mục Tour', 'url' => BASE_URL . '?action=tour-categories'],
            ['name' => $categoryName, 'active' => true]
        ];
        $view = 'tour-category/tours';
        require_once PATH_VIEW_ADMIN;
    }
}

