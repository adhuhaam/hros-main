<?php
include '../db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ticket_id = intval($_POST['ticket_id']);
    $online_price = floatval($_POST['online_price']);
    $agency_price = floatval($_POST['agency_price']);
    $destination_name = trim($_POST['destination_id']); // Destination is passed as a name, not an ID

    // Fetch the correct `destination_id` from `employee_tickets_destination`
    $destination_query = $conn->prepare("SELECT id FROM employee_tickets_destination WHERE destination_name = ?");
    $destination_query->bind_param("s", $destination_name);
    $destination_query->execute();
    $destination_result = $destination_query->get_result();

    if ($destination_result->num_rows > 0) {
        $destination_row = $destination_result->fetch_assoc();
        $destination_id = $destination_row['id'];
    } else {
        header("Location: index.php?error=Invalid Destination");
        exit();
    }

    // Check if a price record already exists for this destination
    $check_query = $conn->prepare("SELECT id FROM employee_tickets_price WHERE destination_id = ?");
    $check_query->bind_param("i", $destination_id);
    $check_query->execute();
    $result = $check_query->get_result();

    if ($result->num_rows > 0) {
        // Update existing price
        $price_row = $result->fetch_assoc();
        $price_id = $price_row['id'];

        $update_query = $conn->prepare("UPDATE employee_tickets_price SET online_price = ?, agency_price = ? WHERE id = ?");
        $update_query->bind_param("ddi", $online_price, $agency_price, $price_id);
        if ($update_query->execute()) {
            // Link the ticket to this price_id
            $link_query = $conn->prepare("UPDATE employee_tickets SET price_id = ? WHERE id = ?");
            $link_query->bind_param("ii", $price_id, $ticket_id);
            $link_query->execute();

            header("Location: index.php?success=Price Updated Successfully");
        } else {
            header("Location: index.php?error=Error Updating Price");
        }
    } else {
        // Insert new price record
        $insert_query = $conn->prepare("INSERT INTO employee_tickets_price (destination_id, online_price, agency_price) VALUES (?, ?, ?)");
        $insert_query->bind_param("idd", $destination_id, $online_price, $agency_price);
        if ($insert_query->execute()) {
            $price_id = $insert_query->insert_id;

            // Link the ticket to this price_id
            $link_query = $conn->prepare("UPDATE employee_tickets SET price_id = ? WHERE id = ?");
            $link_query->bind_param("ii", $price_id, $ticket_id);
            $link_query->execute();

            header("Location: index.php?success=Price Added Successfully");
        } else {
            header("Location: index.php?error=Error Adding Price");
        }
    }
    exit();
}
?>
