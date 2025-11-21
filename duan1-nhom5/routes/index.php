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
    
    // Authentication routes
    'login'              => (new AuthController)->login(),
    'login/process'      => (new AuthController)->processLogin(),
    'register'           => (new AuthController)->register(),
    'register/process'   => (new AuthController)->processRegister(),
    'logout'             => (new AuthController)->logout(),
    'forgot-password'    => (new AuthController)->forgotPassword(),
    
    default             => (new HomeController)->index(),
};