<?php require_once PATH_VIEW . 'hdv/layouts/header.php'; ?>

<main class="max-w-4xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-gray-50">
            <h3 class="font-bold text-gray-800 flex items-center gap-2">
                <i class="bi bi-person-gear text-blue-600"></i> Cập nhật thông tin khách hàng
            </h3>
            <a href="<?= BASE_URL ?>?action=hvd/tours/customers&id=<?= $tour_id ?>&guide_id=<?= $guide_id ?>"
                class="px-3 py-1.5 bg-white border border-gray-300 text-gray-700 text-sm rounded-lg hover:bg-gray-50 flex items-center gap-1 transition-colors">
                <i class="bi bi-arrow-left"></i> Quay lại
            </a>
        </div>

        <div class="p-6">
            <?php if (isset($_SESSION['error'])): ?>
                <div
                    class="bg-red-100 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6 flex justify-between items-center relative alert-dismissible">
                    <span><?= htmlspecialchars($_SESSION['error']);
                    unset($_SESSION['error']); ?></span>
                    <button type="button" class="text-red-700 hover:text-red-900" data-bs-dismiss="alert"><i
                            class="bi bi-x-lg"></i></button>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['success'])): ?>
                <div
                    class="bg-green-100 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6 flex justify-between items-center relative alert-dismissible">
                    <span><?= htmlspecialchars($_SESSION['success']);
                    unset($_SESSION['success']); ?></span>
                    <button type="button" class="text-green-700 hover:text-green-900" data-bs-dismiss="alert"><i
                            class="bi bi-x-lg"></i></button>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?= BASE_URL ?>?action=hvd/customer/update" class="space-y-6">
                <input type="hidden" name="id" value="<?= $customer['id'] ?>">
                <input type="hidden" name="tour_id" value="<?= $tour_id ?>">
                <input type="hidden" name="guide_id" value="<?= $guide_id ?>">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="fullname" class="block text-sm font-medium text-gray-700 mb-1">Họ và tên <span
                                class="text-red-500">*</span></label>
                        <input type="text"
                            class="form-control block w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                            id="fullname" name="fullname" value="<?= htmlspecialchars($customer['fullname'] ?? '') ?>"
                            required>
                    </div>
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Số điện thoại</label>
                        <input type="text"
                            class="form-control block w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                            id="phone" name="phone" value="<?= htmlspecialchars($customer['phone'] ?? '') ?>">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="gender" class="block text-sm font-medium text-gray-700 mb-1">Giới tính</label>
                        <select
                            class="form-select block w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                            id="gender" name="gender">
                            <option value="">Chọn giới tính</option>
                            <option value="male" <?= ($customer['gender'] ?? '') == 'male' ? 'selected' : '' ?>>Nam
                            </option>
                            <option value="female" <?= ($customer['gender'] ?? '') == 'female' ? 'selected' : '' ?>>Nữ
                            </option>
                            <option value="other" <?= ($customer['gender'] ?? '') == 'other' ? 'selected' : '' ?>>Khác
                            </option>
                        </select>
                    </div>
                    <div>
                        <label for="birthdate" class="block text-sm font-medium text-gray-700 mb-1">Ngày sinh</label>
                        <input type="date"
                            class="form-control block w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                            id="birthdate" name="birthdate"
                            value="<?= htmlspecialchars($customer['birthdate'] ?? '') ?>">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="birthdate" class="block text-sm font-medium text-gray-700 mb-1">Ngày sinh</label>
                        <input type="date"
                            class="form-control block w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                            id="birthdate" name="birthdate"
                            value="<?= htmlspecialchars($customer['birthdate'] ?? '') ?>">
                    </div>
                    <div>
                        <label for="id_card" class="block text-sm font-medium text-gray-700 mb-1">CMND/CCCD</label>
                        <input type="text"
                            class="form-control block w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                            id="id_card" name="id_card" value="<?= htmlspecialchars($customer['id_card'] ?? '') ?>">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="passport" class="block text-sm font-medium text-gray-700 mb-1">Passport</label>
                        <input type="text"
                            class="form-control block w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                            id="passport" name="passport" value="<?= htmlspecialchars($customer['passport'] ?? '') ?>">
                    </div>
                </div>

                <div>
                    <label for="hobby" class="block text-sm font-medium text-gray-700 mb-1">Ghi chú</label>
                    <textarea
                        class="form-control block w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                        id="hobby" name="hobby" rows="3"
                        placeholder="Nhập sở thích..."><?= htmlspecialchars($customer['hobby'] ?? '') ?></textarea>
                </div>

                <div>
                    <label for="special_requirements" class="block text-sm font-medium text-gray-700 mb-1">Yêu cầu đặc
                        biệt</label>
                    <textarea
                        class="form-control block w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                        id="special_requirements" name="special_requirements" rows="3"
                        placeholder="Nhập yêu cầu đặc biệt..."><?= htmlspecialchars($customer['special_requirements'] ?? '') ?></textarea>
                </div>

                <div>
                    <label for="dietary_restrictions" class="block text-sm font-medium text-gray-700 mb-1">Hạn chế ăn
                        uống</label>
                    <textarea
                        class="form-control block w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                        id="dietary_restrictions" name="dietary_restrictions" rows="3"
                        placeholder="Nhập hạn chế ăn uống..."><?= htmlspecialchars($customer['dietary_restrictions'] ?? '') ?></textarea>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                    <a href="<?= BASE_URL ?>?action=hvd/tours/customers&id=<?= $tour_id ?>&guide_id=<?= $guide_id ?>"
                        class="px-4 py-2 bg-white border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors">Hủy</a>
                    <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 shadow-sm transition-colors">Cập
                        nhật thông tin</button>
                </div>
            </form>
        </div>
    </div>
</main>

<?php require_once PATH_VIEW . 'hdv/layouts/footer.php'; ?>