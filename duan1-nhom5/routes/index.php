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
    
    default             => (new HomeController)->index(),
};