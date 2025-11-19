<div class="col-12">
    <div class="jumbotron bg-light p-5 rounded mb-4">
        <h1 class="display-4">Hệ thống Quản lý Tour Du lịch</h1>
        <p class="lead">Quản lý và phân loại các tour du lịch trong nước, quốc tế và tour theo yêu cầu.</p>
        <hr class="my-4">
        <p>Hệ thống quản lý tour du lịch đơn giản và dễ sử dụng.</p>
        <a class="btn btn-primary btn-lg" href="<?= BASE_URL ?>?action=tours" role="button">Xem danh sách Tour</a>
    </div>

    <div class="row">
        <div class="col-md-4 mb-3">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">Tour trong nước</h5>
                    <p class="card-text">Tour tham quan, du lịch các địa điểm trong nước.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">Tour quốc tế</h5>
                    <p class="card-text">Tour tham quan, du lịch các nước ngoài.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">Tour theo yêu cầu</h5>
                    <p class="card-text">Tour thiết kế riêng dựa trên yêu cầu cụ thể của từng khách hàng/đoàn khách.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="alert alert-info mt-4">
        <strong>Bắt đầu:</strong> Bạn có thể quản lý tour tại 
        <a href="<?= BASE_URL ?>?action=tours" class="alert-link">đây</a>.
    </div>
</div>

