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
    'bookings/edit'        => (new BookingController)->edit(),
    'bookings/update'      => (new BookingController)->update(),
    'bookings/delete'      => (new BookingController)->delete(),
    
    default             => (new HomeController)->index(),
};