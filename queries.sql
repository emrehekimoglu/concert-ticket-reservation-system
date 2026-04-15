-- Complex Queries for Project Requirements

-- Query 1: Display data from multiple tables (JOIN)
-- Requirement: Display Customer Name, Event Name, Ticket Category, and Seat Number for all confirmed bookings.
SELECT 
    c.FirstName || ' ' || c.LastName AS CustomerName,
    e.Name AS EventName,
    tc.CategoryName,
    t.SeatNumber,
    v.Name AS VenueName
FROM Booking b
JOIN Customer c ON b.CustomerID = c.CustomerID
JOIN Ticket t ON b.BookingID = t.BookingID
JOIN TicketCategory tc ON t.CategoryID = tc.CategoryID
JOIN Event e ON tc.EventID = e.EventID
JOIN Venue v ON e.VenueID = v.VenueID
WHERE b.Status = 'CONFIRMED';


-- Query 2: Group-by statement, order-by statement
-- Requirement: Calculate total revenue generated per event, ordered by revenue descending.
SELECT 
    e.Name AS EventName,
    COUNT(t.TicketID) AS TotalTicketsSold,
    SUM(tc.Price) AS TotalRevenue
FROM Ticket t
JOIN TicketCategory tc ON t.CategoryID = tc.CategoryID
JOIN Event e ON tc.EventID = e.EventID
JOIN Booking b ON t.BookingID = b.BookingID
WHERE b.Status = 'CONFIRMED'
GROUP BY e.Name
ORDER BY TotalRevenue DESC;


-- Query 3: Subquery
-- Requirement: Find customers who have made bookings for events where the ticket price was higher than the average ticket price of all categories.
SELECT DISTINCT 
    c.FirstName, 
    c.LastName, 
    c.Email
FROM Customer c
JOIN Booking b ON c.CustomerID = b.CustomerID
JOIN Ticket t ON b.BookingID = t.BookingID
JOIN TicketCategory tc ON t.CategoryID = tc.CategoryID
WHERE tc.Price > (
    SELECT AVG(Price) FROM TicketCategory
);
