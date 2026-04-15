<?php

// Schema definition for the Generic CRUD Interface
// Keys are Table Names (UPPERCASE to match Oracle default)
// Column keys are UPPERCASE

return [
    'VENUE' => [
        'label' => 'Venue',
        'pk' => 'VENUEID',
        'columns' => [
            'VENUEID' => ['label' => 'ID', 'type' => 'number', 'required' => true, 'readonly_on_edit' => true],
            'NAME' => ['label' => 'Name', 'type' => 'text', 'required' => true],
            'ADDRESS' => ['label' => 'Address', 'type' => 'text', 'required' => false],
            'CAPACITY' => ['label' => 'Capacity', 'type' => 'number', 'required' => false]
        ]
    ],
    'ARTIST' => [
        'label' => 'Artist',
        'pk' => 'ARTISTID',
        'columns' => [
            'ARTISTID' => ['label' => 'ID', 'type' => 'number', 'required' => true, 'readonly_on_edit' => true],
            'NAME' => ['label' => 'Name', 'type' => 'text', 'required' => true],
            'GENRE' => ['label' => 'Genre', 'type' => 'text', 'required' => false],
            'DESCRIPTION' => ['label' => 'Description', 'type' => 'textarea', 'required' => false]
        ]
    ],
    'CUSTOMER' => [
        'label' => 'Customer',
        'pk' => 'CUSTOMERID',
        'columns' => [
            'CUSTOMERID' => ['label' => 'ID', 'type' => 'number', 'required' => true, 'readonly_on_edit' => true],
            'FIRSTNAME' => ['label' => 'First Name', 'type' => 'text', 'required' => true],
            'LASTNAME' => ['label' => 'Last Name', 'type' => 'text', 'required' => true],
            'EMAIL' => ['label' => 'Email', 'type' => 'email', 'required' => true],
            'PHONE' => ['label' => 'Phone', 'type' => 'text', 'required' => false],
            'REGISTRATIONDATE' => ['label' => 'Reg. Date', 'type' => 'date', 'required' => false, 'default' => 'SYSDATE']
            // 'default' => 'SYSDATE' implies we might skip sending it on create if empty, handling in logic
        ]
    ],
    'EVENT' => [
        'label' => 'Event',
        'pk' => 'EVENTID',
        'columns' => [
            'EVENTID' => ['label' => 'ID', 'type' => 'number', 'required' => true, 'readonly_on_edit' => true],
            'VENUEID' => ['label' => 'Venue', 'type' => 'fk', 'table' => 'VENUE', 'display' => 'NAME', 'required' => true],
            'ARTISTID' => ['label' => 'Artist', 'type' => 'fk', 'table' => 'ARTIST', 'display' => 'NAME', 'required' => true],
            'EVENTDATE' => ['label' => 'Event Date', 'type' => 'datetime-local', 'required' => true],
            'NAME' => ['label' => 'Event Name', 'type' => 'text', 'required' => false]
        ]
    ],
    'TICKETCATEGORY' => [
        'label' => 'Ticket Category',
        'pk' => 'CATEGORYID',
        'columns' => [
            'CATEGORYID' => ['label' => 'ID', 'type' => 'number', 'required' => true, 'readonly_on_edit' => true],
            'EVENTID' => ['label' => 'Event', 'type' => 'fk', 'table' => 'EVENT', 'display' => 'NAME', 'required' => true],
            'CATEGORYNAME' => ['label' => 'Category Name', 'type' => 'text', 'required' => true],
            'PRICE' => ['label' => 'Price', 'type' => 'number', 'step' => '0.01', 'required' => false],
            'TOTALQUOTA' => ['label' => 'Total Quota', 'type' => 'number', 'required' => false],
            'REMAININGQUOTA' => ['label' => 'Remaining', 'type' => 'number', 'required' => false]
        ]
    ],
    'BOOKING' => [
        'label' => 'Booking',
        'pk' => 'BOOKINGID',
        'columns' => [
            'BOOKINGID' => ['label' => 'ID', 'type' => 'number', 'required' => true, 'readonly_on_edit' => true],
            'CUSTOMERID' => ['label' => 'Customer', 'type' => 'fk', 'table' => 'CUSTOMER', 'display' => 'EMAIL', 'required' => true],
            'BOOKINGDATE' => ['label' => 'Booking Date', 'type' => 'datetime-local', 'required' => false],
            'TOTALAMOUNT' => ['label' => 'Total Amount', 'type' => 'number', 'step' => '0.01', 'required' => false],
            'STATUS' => ['label' => 'Status', 'type' => 'select', 'options' => ['PENDING', 'CONFIRMED', 'CANCELLED'], 'required' => false]
        ]
    ],
    'TICKET' => [
        'label' => 'Ticket',
        'pk' => 'TICKETID',
        'columns' => [
            'TICKETID' => ['label' => 'ID', 'type' => 'number', 'required' => true, 'readonly_on_edit' => true],
            'BOOKINGID' => ['label' => 'Booking ID', 'type' => 'fk', 'table' => 'BOOKING', 'display' => 'BOOKINGID', 'required' => true],
            'CATEGORYID' => ['label' => 'Category', 'type' => 'fk', 'table' => 'TICKETCATEGORY', 'display' => 'CATEGORYNAME', 'required' => true],
            'SEATNUMBER' => ['label' => 'Seat Number', 'type' => 'text', 'required' => false]
        ]
    ],
    'PAYMENT' => [
        'label' => 'Payment',
        'pk' => 'PAYMENTID',
        'columns' => [
            'PAYMENTID' => ['label' => 'ID', 'type' => 'number', 'required' => true, 'readonly_on_edit' => true],
            'BOOKINGID' => ['label' => 'Booking ID', 'type' => 'fk', 'table' => 'BOOKING', 'display' => 'BOOKINGID', 'required' => true],
            'PAYMENTDATE' => ['label' => 'Payment Date', 'type' => 'datetime-local', 'required' => false],
            'AMOUNT' => ['label' => 'Amount', 'type' => 'number', 'step' => '0.01', 'required' => true],
            'PAYMENTMETHOD' => ['label' => 'Method', 'type' => 'text', 'required' => false]
        ]
    ]
];
