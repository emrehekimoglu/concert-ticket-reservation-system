-- Create Tables for Concert Ticket Reservation System
-- User: ConcertSys

-- 1. VENUE Table
CREATE TABLE Venue (
    VenueID NUMBER PRIMARY KEY,
    Name VARCHAR2(100) NOT NULL,
    Address VARCHAR2(255),
    Capacity NUMBER Check (Capacity > 0)
);

-- 2. ARTIST Table
CREATE TABLE Artist (
    ArtistID NUMBER PRIMARY KEY,
    Name VARCHAR2(100) NOT NULL,
    Genre VARCHAR2(50),
    Description VARCHAR2(500)
);

-- 3. CUSTOMER Table
CREATE TABLE Customer (
    CustomerID NUMBER PRIMARY KEY,
    FirstName VARCHAR2(50) NOT NULL,
    LastName VARCHAR2(50) NOT NULL,
    Email VARCHAR2(100) UNIQUE NOT NULL,
    Phone VARCHAR2(20),
    RegistrationDate DATE DEFAULT SYSDATE
);

-- 4. EVENT Table
CREATE TABLE Event (
    EventID NUMBER PRIMARY KEY,
    VenueID NUMBER NOT NULL,
    ArtistID NUMBER NOT NULL,
    EventDate TIMESTAMP NOT NULL,
    Name VARCHAR2(100),
    CONSTRAINT fk_event_venue FOREIGN KEY (VenueID) REFERENCES Venue(VenueID),
    CONSTRAINT fk_event_artist FOREIGN KEY (ArtistID) REFERENCES Artist(ArtistID)
);

-- 5. TICKET_CATEGORY Table (e.g., VIP, Standing)
CREATE TABLE TicketCategory (
    CategoryID NUMBER PRIMARY KEY,
    EventID NUMBER NOT NULL,
    CategoryName VARCHAR2(50) NOT NULL,
    Price NUMBER(10, 2) CHECK (Price >= 0),
    TotalQuota NUMBER CHECK (TotalQuota > 0),
    RemainingQuota NUMBER CHECK (RemainingQuota >= 0),
    CONSTRAINT fk_cat_event FOREIGN KEY (EventID) REFERENCES Event(EventID),
    CONSTRAINT chk_quota CHECK (RemainingQuota <= TotalQuota)
);

-- 6. BOOKING Table
CREATE TABLE Booking (
    BookingID NUMBER PRIMARY KEY,
    CustomerID NUMBER NOT NULL,
    BookingDate TIMESTAMP DEFAULT SYSDATE,
    TotalAmount NUMBER(10, 2) DEFAULT 0,
    Status VARCHAR2(20) CHECK (Status IN ('PENDING', 'CONFIRMED', 'CANCELLED')),
    CONSTRAINT fk_booking_customer FOREIGN KEY (CustomerID) REFERENCES Customer(CustomerID)
);

-- 7. TICKET Table
CREATE TABLE Ticket (
    TicketID NUMBER PRIMARY KEY,
    BookingID NUMBER NOT NULL,
    CategoryID NUMBER NOT NULL,
    SeatNumber VARCHAR2(20),
    CONSTRAINT fk_ticket_booking FOREIGN KEY (BookingID) REFERENCES Booking(BookingID),
    CONSTRAINT fk_ticket_category FOREIGN KEY (CategoryID) REFERENCES TicketCategory(CategoryID)
);

-- 8. PAYMENT Table
CREATE TABLE Payment (
    PaymentID NUMBER PRIMARY KEY,
    BookingID NUMBER NOT NULL,
    PaymentDate TIMESTAMP DEFAULT SYSDATE,
    Amount NUMBER(10, 2) NOT NULL,
    PaymentMethod VARCHAR2(50),
    CONSTRAINT fk_payment_booking FOREIGN KEY (BookingID) REFERENCES Booking(BookingID)
);
