<?php
include '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['emp_no'])) {
    $emp_no = $_POST['emp_no'];
    $response = ['success' => false];

    // Fetch employee details
    $employee_query = $conn->prepare("SELECT date_of_join FROM employees WHERE emp_no = ?");
    $employee_query->bind_param("s", $emp_no);
    $employee_query->execute();
    $employee_result = $employee_query->get_result()->fetch_assoc();

    if ($employee_result) {
        $response['success'] = true;
        $response['date_of_join'] = date('d-M-Y', strtotime($employee_result['date_of_join']));

        // Fetch last leave record
        $leave_query = $conn->prepare("SELECT lt.name AS leave_type, lr.start_date, lr.end_date, lr.status
                                       FROM leave_records lr 
                                       JOIN leave_types lt ON lr.leave_type_id = lt.id
                                       WHERE lr.emp_no = ? ORDER BY lr.start_date DESC LIMIT 1");
        $leave_query->bind_param("s", $emp_no);
        $leave_query->execute();
        $leave_result = $leave_query->get_result()->fetch_assoc();

        if ($leave_result) {
            $response['last_leave'] = [
                'leave_type' => $leave_result['leave_type'],
                'start_date' => date('d-M-Y', strtotime($leave_result['start_date'])),
                'end_date' => date('d-M-Y', strtotime($leave_result['end_date'])),
                'status' => $leave_result['status']
            ];
        }
    }

    echo json_encode($response);
}
?>
