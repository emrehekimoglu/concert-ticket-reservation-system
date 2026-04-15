-- Insert Data (Sanitized)
DELETE FROM Payment;
DELETE FROM Ticket;
DELETE FROM Booking;
DELETE FROM TicketCategory;
DELETE FROM Event;
DELETE FROM Customer;
DELETE FROM Artist;
DELETE FROM Venue;

-- 1. VENUE
INSERT INTO Venue (VenueID, Name, Address, Capacity) VALUES (1, 'City Arena', '123 Main St', 20000);
INSERT INTO Venue (VenueID, Name, Address, Capacity) VALUES (2, 'Jazz Club', '45 Blue Ave', 300);
INSERT INTO Venue (VenueID, Name, Address, Capacity) VALUES (3, 'Open Air Theater', '88 Park Ln', 5000);

-- 2. ARTIST
INSERT INTO Artist (ArtistID, Name, Genre, Description) VALUES (1, 'The Rockers', 'Rock', 'Legendary rock band');
INSERT INTO Artist (ArtistID, Name, Genre, Description) VALUES (2, 'Smooth Jazz Trio', 'Jazz', 'Relaxing evening jazz');
INSERT INTO Artist (ArtistID, Name, Genre, Description) VALUES (3, 'Pop Star Anna', 'Pop', 'Top chart singer');

-- 3. CUSTOMER
INSERT INTO Customer (CustomerID, FirstName, LastName, Email, Phone) VALUES (1, 'John', 'Doe', 'john@example.com', '555-0101');
INSERT INTO Customer (CustomerID, FirstName, LastName, Email, Phone) VALUES (2, 'Jane', 'Smith', 'jane@example.com', '555-0102');
INSERT INTO Customer (CustomerID, FirstName, LastName, Email, Phone) VALUES (3, 'Alice', 'Johnson', 'alice@example.com', '555-0103');
INSERT INTO Customer (CustomerID, FirstName, LastName, Email, Phone) VALUES (4, 'Bob', 'Brown', 'bob@example.com', '555-0104');

-- 4. EVENT
INSERT INTO Event (EventID, VenueID, ArtistID, EventDate, Name) VALUES (1, 1, 1, TO_TIMESTAMP('2025-06-15 20:00:00', 'YYYY-MM-DD HH24:MI:SS'), 'Rockers World Tour');
INSERT INTO Event (EventID, VenueID, ArtistID, EventDate, Name) VALUES (2, 2, 2, TO_TIMESTAMP('2025-06-20 21:00:00', 'YYYY-MM-DD HH24:MI:SS'), 'Jazz Night');
INSERT INTO Event (EventID, VenueID, ArtistID, EventDate, Name) VALUES (3, 3, 3, TO_TIMESTAMP('2025-07-01 19:00:00', 'YYYY-MM-DD HH24:MI:SS'), 'Summer Pop Fest');

-- 5. TICKET_CATEGORY
INSERT INTO TicketCategory (CategoryID, EventID, CategoryName, Price, TotalQuota, RemainingQuota) VALUES (1, 1, 'VIP', 500, 100, 98);
INSERT INTO TicketCategory (CategoryID, EventID, CategoryName, Price, TotalQuota, RemainingQuota) VALUES (2, 1, 'Standing', 100, 5000, 4998);
INSERT INTO TicketCategory (CategoryID, EventID, CategoryName, Price, TotalQuota, RemainingQuota) VALUES (3, 2, 'Table Seat', 150, 50, 48);
INSERT INTO TicketCategory (CategoryID, EventID, CategoryName, Price, TotalQuota, RemainingQuota) VALUES (4, 3, 'Front Row', 300, 200, 200);

-- 6. BOOKING
INSERT INTO Booking (BookingID, CustomerID, BookingDate, TotalAmount, Status) VALUES (1, 1, SYSDATE - 2, 600, 'CONFIRMED');
INSERT INTO Booking (BookingID, CustomerID, BookingDate, TotalAmount, Status) VALUES (2, 2, SYSDATE - 1, 300, 'CONFIRMED');
INSERT INTO Booking (BookingID, CustomerID, BookingDate, TotalAmount, Status) VALUES (3, 3, SYSDATE, 100, 'PENDING');

-- 7. TICKET
INSERT INTO Ticket (TicketID, BookingID, CategoryID, SeatNumber) VALUES (1, 1, 1, 'A1');
INSERT INTO Ticket (TicketID, BookingID, CategoryID, SeatNumber) VALUES (2, 1, 2, 'GEN-101');
INSERT INTO Ticket (TicketID, BookingID, CategoryID, SeatNumber) VALUES (3, 2, 3, 'T5-1');
INSERT INTO Ticket (TicketID, BookingID, CategoryID, SeatNumber) VALUES (4, 2, 3, 'T5-2');
INSERT INTO Ticket (TicketID, BookingID, CategoryID, SeatNumber) VALUES (5, 3, 2, 'GEN-105');

-- 8. PAYMENT
INSERT INTO Payment (PaymentID, BookingID, Amount, PaymentMethod) VALUES (1, 1, 600, 'Credit Card');
INSERT INTO Payment (PaymentID, BookingID, Amount, PaymentMethod) VALUES (2, 2, 300, 'PayPal');

COMMIT;
