<?php

$action = $_GET['action'] ?? '/';

match ($action) {
    '/'                 => (new HomeController)->index(),
    
    // Tour management routes
    'tours'             => (new TourController)->index(),
    'tours/show'        => (new TourController)->show(),
    'tours/create'      => (new TourController)->create(),
    'tours/store'       => (new TourController)->store(),
    'tours/edit'        => (new TourController)->edit(),
    'tours/update'      => (new TourController)->update(),
    'tours/delete'      => (new TourController)->delete(),
    
    // Tour category management routes
    'tour-categories'           => (new TourCategoryController)->index(),
    'tour-categories/view-tours' => (new TourCategoryController)->viewTours(),
    
    // Booking management routes
    'bookings'             => (new BookingController)->index(),
    'bookings/create'      => (new BookingController)->create(),
    'bookings/store'       => (new BookingController)->store(),
    'bookings/show'        => (new BookingController)->show(),
    'bookings/update-status' => (new BookingController)->updateStatus(),
    'bookings/edit'        => (new BookingController)->edit(),
    'bookings/update'      => (new BookingController)->update(),
    'bookings/delete'      => (new BookingController)->delete(),
    
    // Guide management routes (HDV)
    'guides'             => (new GuideController)->index(),
    'guides/create'      => (new GuideController)->create(),
    'guides/store'       => (new GuideController)->store(),
    'guides/show'        => (new GuideController)->show(),
    'guides/edit'        => (new GuideController)->edit(),
    'guides/update'      => (new GuideController)->update(),
    'guides/delete'      => (new GuideController)->delete(),
    // HDV (Hướng dẫn viên) standalone dashboard
    'hvd'                => (new HvdController)->home(),
    'hvd/home'           => (new HvdController)->home(),
    'hvd/tours'          => (new HvdController)->tours(),
    'hvd/tours/show'     => (new HvdController)->show(),
    'hvd/customer/edit'  => (new HvdController)->customerEdit(),    
    'hvd/customer/update' => (new HvdController)->customerUpdate(),
    
    
    // Authentication routes
    'login'              => (new AuthController)->login(),
    'login/process'      => (new AuthController)->processLogin(),
    'register'           => (new AuthController)->register(),
    'register/process'   => (new AuthController)->processRegister(),
    'logout'             => (new AuthController)->logout(),
    'forgot-password'    => (new AuthController)->forgotPassword(),
    
    // Departure schedule management routes
    'departure-schedules'                => (new DepartureScheduleController)->index(),
    'departure-schedules/create'         => (new DepartureScheduleController)->create(),
    'departure-schedules/store'          => (new DepartureScheduleController)->store(),
    'departure-schedules/show'           => (new DepartureScheduleController)->show(),
    'departure-schedules/edit'           => (new DepartureScheduleController)->edit(),
    'departure-schedules/update'         => (new DepartureScheduleController)->update(),
    'departure-schedules/delete'         => (new DepartureScheduleController)->delete(),
    'departure-schedules/assign-guide'   => (new DepartureScheduleController)->assignGuide(),
    'departure-schedules/process-assign-guide' => (new DepartureScheduleController)->processAssignGuide(),
    'departure-schedules/assign-service' => (new DepartureScheduleController)->assignService(),
    'departure-schedules/process-assign-service' => (new DepartureScheduleController)->processAssignService(),
    'departure-schedules/update-assignment-status' => (new DepartureScheduleController)->updateAssignmentStatus(),
    
    // Tour customer management routes (Danh sách khách theo tour)
    'tour-customers'             => (new TourCustomerController)->index(),
    'tour-customers/show'        => (new TourCustomerController)->show(),
    'tour-customers/export'      => (new TourCustomerController)->export(),
    
    // Group list management routes (In danh sách đoàn)
    'group-lists'                => (new GroupListController)->index(),
    'group-lists/show'           => (new GroupListController)->show(),
    'group-lists/print'          => (new GroupListController)->print(),
    
    // Check-in management routes
    'checkins'                   => (new CheckinController)->index(),
    'checkins/show'              => (new CheckinController)->show(),
    'checkins/process'           => (new CheckinController)->process(),
    'checkins/update'            => (new CheckinController)->update(),
    
    // Room assignment management routes (Phân phòng khách sạn)
    'room-assignments'           => (new RoomAssignmentController)->index(),
    'room-assignments/show'      => (new RoomAssignmentController)->show(),
    'room-assignments/create'    => (new RoomAssignmentController)->create(),
    'room-assignments/store'     => (new RoomAssignmentController)->store(),
    'room-assignments/edit'      => (new RoomAssignmentController)->edit(),
    'room-assignments/update'    => (new RoomAssignmentController)->update(),
    'room-assignments/delete'    => (new RoomAssignmentController)->delete(),
    
    // Operation report routes (Báo cáo vận hành tour)
    'operation-reports'          => (new OperationReportController)->index(),
    'operation-reports/show'     => (new OperationReportController)->show(),
    'operation-reports/create'   => (new OperationReportController)->create(),
    'operation-reports/store'   => (new OperationReportController)->store(),
    'operation-reports/edit'    => (new OperationReportController)->edit(),
    'operation-reports/update'  => (new OperationReportController)->update(),
    'operation-reports/delete'  => (new OperationReportController)->destroy(),
    'operation-reports/compare' => (new OperationReportController)->compare(),
    'operation-reports/export'  => (new OperationReportController)->export(),
    
    // Statistics routes (Thống kê)
    'statistics'                 => (new StatisticsController)->index(),
    
    // Authentication routes
    'login'              => (new AuthController)->login(),
    'login/process'      => (new AuthController)->processLogin(),
    'register'           => (new AuthController)->register(),
    'register/process'   => (new AuthController)->processRegister(),
    'logout'             => (new AuthController)->logout(),
    'forgot-password'    => (new AuthController)->forgotPassword(),
    
    default             => (new HomeController)->index(),
    
};