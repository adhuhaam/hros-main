Joined Date<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../db.php';
include '../session.php';

$record_id = filter_input(INPUT_GET, 'record_id', FILTER_VALIDATE_INT);
if ($record_id === null || $record_id === false) {
    die("Invalid record ID.");
}

// Fetch the main record from bank_account_records and employees tables
$query = "SELECT b.*, e.name AS employee_name, e.xpat_designation, e.dob, e.company, e.passport_nic_no_expires, e.contact_number, e.emp_email, e.nationality, e.xpat_join_date, e.permanent_address, e.basic_salary, e.salary_currency, e.work_site, e.wp_no, e.passport_nic_no 
          FROM bank_account_records b 
          JOIN employees e ON b.emp_no = e.emp_no 
          WHERE b.id = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $record_id);
$stmt->execute();
$result = $stmt->get_result();
$record = $result->fetch_assoc();

if (!$record) {
    die("Record not found.");
}

// Fetch the expiry_date from work_permit_fees based on emp_no
$emp_no = $record['emp_no'];
$expiryDateQuery = "SELECT expiry_date FROM work_permit_fees WHERE emp_no = ? LIMIT 1";
$expiryStmt = $conn->prepare($expiryDateQuery);
$expiryStmt->bind_param("s", $emp_no);
$expiryStmt->execute();
$expiryResult = $expiryStmt->get_result();
$expiryRecord = $expiryResult->fetch_assoc();

$expiry_date = $expiryRecord['expiry_date'] ?? 'N/A'; // Use 'N/A' if no expiry date is found

$food_allowance = 2000;
$basic_salary = (float)$record['basic_salary'];
$currency = filter_var($record['salary_currency'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$annual_gross_salary = ($currency === 'USD') ? ($basic_salary * 12) : (($basic_salary + $food_allowance) * 12);
?>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<html lang="en" style="font-size: 10.0pt;">

<head>
    <title>result</title>
    <style>
        @font-face {
            font-family: 'pro';
            font-style: normal;
            font-weight: normal;
            src: local('Myriad Web Pro'), url('pro.woff') format('woff');
        }

        p {
            margin: 0;
        }

        .page {
            margin: 10pt auto;
            position: relative;
        }

        span.position,
        p.paragraph {
            position: absolute;
            display: block;
        }

        span.position {
            transform-origin: left;
            text-align: left;
            white-space: nowrap;
        }

        span.style {
            white-space: nowrap;
        }

        td {
            padding: 0px;
        }

        a.link {
            color: inherit;
            text-decoration: inherit;
        }

        div.group,
        div.textbox,
        svg.graphic,
        img.image,
        div.rotation {
            position: absolute;
        }

        .noselect {
            -webkit-touch-callout: none;
            -webkit-user-select: none;
            -khtml-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }

        @media print {
            @page {
                size: 595.0pt 842.0pt;
                margin: 0pt 0pt 0pt 0pt;
            }

            html,
            body {
                font-family: 'pro', serif;
                width: 595.0pt;
                height: 842.0pt;
                font-size: 10pt;
            }
        }

        @media print {
            .page {
                margin: 0pt;
            }
        }

        p.body-text {
            font-family: 'pro', serif;
            font-size: 0.85rem;
            color: #000000;
        }

        p.heading-1 {
            font-family: 'pro', serif;
            font-size: 1.60rem;
            color: #000000;
        }

        p.heading-2 {
            font-family: 'pro', serif;
            font-size: 1.00rem;
            color: #000000;
        }

        p.list-paragraph {
            font-family: 'pro', serif;
            font-size: 1.10rem;
            color: #000000;
        }

        p.table-paragraph {
            font-family: 'pro', serif;
            font-size: 1.10rem;
            color: #000000;
        }
    </style>
</head>

<body style="margin: 0; background: gainsboro;">
    <div style="position: relative; margin: auto; width: 59.50rem;">
        <div class="page" style="background: #ffffff; width: 59.55rem; height: 84.20rem; z-index: 0;">
            <img src="data:image/png;base64,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"
                class="image"
                style="width: 2.89rem; height: 2.12rem; display: block; z-index: 0; left: 3.63rem; top: 4.41rem;" />
            <p class="paragraph body-text"
                style="width: 15.09rem; height: 3.55rem; font-size: 1.10rem; left: 3.62rem; top: 3.29rem; text-align: left; font-family: pro, serif;">

                <span class="position" style="width: 1.04rem; height: 1.55rem; left: 3.20rem; top: 1.99rem;">
                    <span class="style"></span>
                    <span class="style" style="font-family: 'pro', serif; font-size: 1.00rem;"> </span>
                    <span id="view-8.pdf" class="style"
                        style="font-family:'pro'; font-size: 1.30rem; color: #292929;">Maldives Islamic Bank</span>
                </span>

            </p>
            <p class="paragraph heading-1"
                style="width: 15.24rem; height: 1.88rem; left: 3.47rem; top: 6.84rem; text-align: left; color: #222e65; font-weight: 600;">
                <span class="position style"
                    style="font-family:'pro';width: 9.88rem; height: 2.07rem; left: 0.00rem; top: -0.02rem;">INFORMATION
                    FORM</span>

            </p>
            <p class="paragraph heading-2"
                style="width: 15.09rem; height: 1.14rem; left: 3.62rem; top: 8.71rem; text-align: left; font-family: 'pro', serif; color: #2a9a47; font-weight: 400;">
                <span class="position style"
                    style="font-family:'pro';width: 3.83rem; height: 1.26rem; left: 0.00rem; top: -0.11rem;">INDIVIDUAL</span>

            </p>
            <p class="paragraph body-text"
                style="width: 2.86rem; height: 1.26rem; font-size: 1.10rem; left: 54.38rem; top: 3.29rem; text-align: left; font-family: pro, serif; font-weight: 300;">
                <span class="position" style="width: 0.41rem; height: 0.91rem; left: 0.00rem; top: 0.35rem;">
                    <span class="style"></span>
                    <span class="style" style="font-family: 'pro', serif; font-size: 0.80rem; color: #a7a9ac;">V</span>
                </span>
                <span class="position style"
                    style="width: 1.06rem; height: 0.91rem; font-size: 0.80rem; left: 0.40rem; top: 0.35rem; font-family: 'pro', serif; color: #a7a9ac;">:2.0</span>
            </p>
            <table class="table"
                style="width: 10.24rem; height: 1.25rem; table-layout: fixed; z-index: 0; position: absolute; left: 45.01rem; top: 6.55rem; border-collapse: collapse;">
                <tbody>
                    <tr>
                        <td style="border: none; height: 0.0rem; width: 1.280rem;" />
                        <td style="border: none; height: 0.0rem; width: 1.280rem;" />
                        <td style="border: none; height: 0.0rem; width: 1.280rem;" />
                        <td style="border: none; height: 0.0rem; width: 1.280rem;" />
                        <td style="border: none; height: 0.0rem; width: 1.280rem;" />
                        <td style="border: none; height: 0.0rem; width: 1.280rem;" />
                        <td style="border: none; height: 0.0rem; width: 1.280rem;" />
                        <td style="border: none; height: 0.0rem; width: 1.280rem;" />
                    </tr>
                    <tr>
                        <td rowspan="1" colspan="1" class="cell"
                            style="border-bottom: 0.03rem solid#616262; background: #f1f0f0; width: 1.280rem; height: 1.240rem; border-top: 0.03rem solid#616262; border-left: 0.03rem solid#616262; vertical-align: top; border-right: 0.03rem solid#616262;">
                        </td>
                        <td rowspan="1" colspan="1" class="cell"
                            style="border-bottom: 0.03rem solid#616262; background: #f1f0f0; width: 1.280rem; height: 1.240rem; border-top: 0.03rem solid#616262; border-left: 0.03rem solid#616262; vertical-align: top; border-right: 0.03rem solid#616262;">
                        </td>
                        <td rowspan="1" colspan="1" class="cell"
                            style="border-bottom: 0.03rem solid#616262; background: #f1f0f0; width: 1.280rem; height: 1.240rem; border-top: 0.03rem solid#616262; border-left: 0.03rem solid#616262; vertical-align: top; border-right: 0.03rem solid#616262;">
                        </td>
                        <td rowspan="1" colspan="1" class="cell"
                            style="border-bottom: 0.03rem solid#616262; background: #f1f0f0; width: 1.280rem; height: 1.240rem; border-top: 0.03rem solid#616262; border-left: 0.03rem solid#616262; vertical-align: top; border-right: 0.03rem solid#616262;">
                        </td>
                        <td rowspan="1" colspan="1" class="cell"
                            style="border-bottom: 0.03rem solid#616262; background: #f1f0f0; width: 1.280rem; height: 1.240rem; border-top: 0.03rem solid#616262; border-left: 0.03rem solid#616262; vertical-align: top; border-right: 0.03rem solid#616262;">
                        </td>
                        <td rowspan="1" colspan="1" class="cell"
                            style="border-bottom: 0.03rem solid#616262; background: #f1f0f0; width: 1.280rem; height: 1.240rem; border-top: 0.03rem solid#616262; border-left: 0.03rem solid#616262; vertical-align: top; border-right: 0.03rem solid#616262;">
                        </td>
                        <td rowspan="1" colspan="1" class="cell"
                            style="border-bottom: 0.03rem solid#616262; background: #f1f0f0; width: 1.280rem; height: 1.240rem; border-top: 0.03rem solid#616262; border-left: 0.03rem solid#616262; vertical-align: top; border-right: 0.03rem solid#616262;">
                        </td>
                        <td rowspan="1" colspan="1" class="cell"
                            style="border-bottom: 0.03rem solid#616262; background: #f1f0f0; width: 1.280rem; height: 1.240rem; border-top: 0.03rem solid#616262; border-left: 0.03rem solid#616262; vertical-align: top; border-right: 0.03rem solid#616262;">
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="group" style="width: 52.23rem; height: 2.12rem; display: block; left: 3.60rem; top: 11.59rem;">
                <div class="textbox"
                    style="background: #2a9a47; width: 46.03rem; height: 2.12rem; display: block; z-index: -10; left: 6.21rem; top: 0.00rem;">
                    <p class="paragraph"
                        style="width: 45.15rem; height: 1.74rem; z-index: -10; font-size: 1.00rem; left: 0.88rem; top: -0.00rem; text-align: left; font-family: 'pro', serif; color: #ffffff; font-weight: 400;">
                        <span class="position style"
                            style="width: 1.66rem; height: 1.26rem; left: 0.00rem; top: 0.50rem;">PERSONAL INFORMATION</span>

                    </p>
                </div>
                <div class="textbox"
                    style="background: #222e65; width: 6.21rem; height: 2.12rem; display: block; z-index: -10; left: 0.00rem; top: 0.00rem;">
                    <p class="paragraph"
                        style="width: 5.61rem; height: 1.64rem; z-index: -10; font-size: 1.00rem; left: 0.60rem; top: -0.00rem; text-align: left; font-family: 'pro', serif; color: #ffffff; font-weight: 600;">
                        <span class="position style"
                            style="width: 1.66rem; height: 1.29rem; left: 0.00rem; top: 0.38rem;">SECTION A</span>
                    </p>
                </div>
            </div>
            <div class="group" style="width: 13.95rem; height: 5.41rem; display: block; left: 41.96rem; top: 4.47rem;">
                <svg viewbox="0.000000, 0.000000, 139.550000, 36.750000" class="graphic"
                    style="width: 13.96rem; height: 3.67rem; display: block; z-index: -10; left: 0.00rem; top: 1.74rem;">
                    <path fill="#f1f0f0" fill-opacity="1.000000"
                        d="M 0 36.727 L 139.549 36.727 L 139.549 0 L 0 0 L 0 36.727 Z" stroke="none" />
                </svg>
                <svg viewbox="0.000000, 0.000000, 104.550000, 12.450000" class="graphic"
                    style="width: 10.46rem; height: 1.24rem; display: block; z-index: -10; left: 3.06rem; top: 3.84rem;">
                    <path stroke-width="0.190000" fill="none" d="M 0 0 L 12.788 0 L 12.788 12.425 L 0 12.425 L 0 0 Z"
                        stroke="#808285" stroke-opacity="1.000000" />
                    <path stroke-width="0.190000" fill="none"
                        d="M 47.493 0 L 60.281 0 L 60.281 12.425 L 47.493 12.425 L 47.493 0 Z" stroke="#808285"
                        stroke-opacity="1.000000" />
                    <path stroke-width="0.190000" fill="none"
                        d="M 91.749 0 L 104.537 0 L 104.537 12.425 L 91.749 12.425 L 91.749 0 Z" stroke="#808285"
                        stroke-opacity="1.000000" />
                </svg>
                <div class="textbox"
                    style="width: 2.53rem; height: 2.63rem; display: block; z-index: -10; left: 0.24rem; top: 2.22rem;">
                    <p class="paragraph body-text"
                        style="width: 2.54rem; height: 0.86rem; z-index: -10; font-size: 0.70rem; left: 0.00rem; top: -0.00rem; text-align: left; color: #6d6e71; font-weight: 300;">
                        <span class="position style"
                            style="width: 0.90rem; height: 0.86rem; left: 0.00rem; top: 0.00rem;">CIF NO.</span>
                        <span class="position style"
                            style="width: 1.05rem; height: 0.86rem; left: 1.04rem; top: 0.00rem;"> </span>
                    </p>
                    <p class="paragraph body-text"
                        style="width: 2.50rem; height: 0.88rem; z-index: -10; font-size: 0.70rem; left: 0.04rem; top: 1.74rem; text-align: left; color: #6d6e71; font-weight: 300;">
                        <span class="position style"
                            style="width: 0.78rem; height: 0.86rem; left: 0.00rem; top: 0.03rem;">New Acc</span>
                        <span class="position style"
                            style="width: 0.68rem; height: 0.86rem; left: 1.41rem; top: 0.03rem;"> </span>
                    </p>
                </div>
                <div class="textbox"
                    style="width: 2.62rem; height: 0.86rem; display: block; z-index: -10; left: 5.03rem; top: 3.99rem;">
                    <p class="paragraph body-text"
                        style="width: 2.62rem; height: 0.86rem; z-index: -10; font-size: 0.70rem; left: 0.00rem; top: -0.00rem; text-align: left; color: #6d6e71; font-weight: 300;">
                        <span class="position style"
                            style="width: 2.52rem; height: 0.86rem; left: 0.00rem; top: 0.00rem;">Dormant</span>
                    </p>
                </div>
                <div class="textbox"
                    style="width: 2.19rem; height: 0.86rem; display: block; z-index: -10; left: 9.85rem; top: 3.99rem;">
                    <p class="paragraph body-text"
                        style="width: 2.20rem; height: 0.86rem; z-index: -10; font-size: 0.70rem; left: 0.00rem; top: -0.00rem; text-align: left; color: #6d6e71; font-weight: 300;">
                        <span class="position style"
                            style="width: 1.55rem; height: 0.86rem; left: 0.00rem; top: 0.00rem;">Update</span>
                    </p>
                </div>
                <div class="textbox"
                    style="background: #2a9a47; width: 13.95rem; height: 1.74rem; display: block; z-index: -10; left: 0.00rem; top: -0.00rem;">
                    <p class="paragraph"
                        style="width: 13.76rem; height: 1.39rem; z-index: -10; font-size: 0.80rem; left: 0.20rem; top: 0.00rem; text-align: left; font-family: 'pro', serif; color: #ffffff; font-weight: 400;">
                        <span class="position style"
                            style="width: 1.38rem; height: 1.01rem; left: 0.00rem; top: 0.39rem;">FOR BANK USE
                            ONLY</span>
                    </p>
                </div>
            </div>
            <p class="paragraph body-text"
                style="width: 3.67rem; height: 1.45rem; left: 3.65rem; top: 14.11rem; text-align: left; font-family: 'pro', serif; color: #6d6e71; font-weight: 600;">
                <span class="position style"
                    style="width: 1.65rem; height: 1.10rem; left: 0.00rem; top: 0.38rem;">Title</span>
            </p>
            <div class="group" style="width: 1.68rem; height: 1.68rem; display: block; left: 12.14rem; top: 14.27rem;">
                <svg viewbox="0.000000, 0.000000, 16.600000, 16.600000" class="graphic"
                    style="width: 1.66rem; height: 1.66rem; display: block; z-index: -10; left: 0.01rem; top: 0.01rem;">
                    <path stroke-width="0.250000" fill="none" d="M 0 0 L 16.567 0 L 16.567 16.567 L 0 16.567 L 0 0 Z"
                        stroke="#808285" stroke-opacity="1.000000" />
                </svg>
                <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACUAAAAkCAYAAAAOwvOmAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAAqklEQVRYhe3YoQ3DMBSE4WvkQi9geZCyeoGggozjidIdgtoB3g4lRuZWCypHgccccD+yjD7psbvknL84USmlbRqNOJZS2gDcXf/w3n9ijK9RoBDCtbU2A8COijG+l2V5jEKZ2bOUAgA41fl6QrEJxSYUm1BsQrEJxSYUm1BsQrEJxSYUm1BsQrEJxbavLiEEZ2brKEit9dbfDviPVa21uU8xo5v6ejYacuwHIQQkAbS3THYAAAAASUVORK5CYII="
                    class="image"
                    style="width: 1.38rem; height: 1.35rem; display: block; z-index: -10; left: 0.15rem; top: 0.17rem;" />
                <div class="textbox"
                    style="width: 1.68rem; height: 1.68rem; display: block; z-index: -10; left: 0.00rem; top: 0.00rem;">
                    <p class="paragraph body-text"
                        style="width: 1.23rem; height: 1.39rem; z-index: -10; font-size: 0.90rem; left: 0.45rem; top: 0.00rem; text-align: left; font-family: 'pro', serif; font-weight: 400;">
                        <span class="position style"
                            style="width: 0.90rem; height: 0.90rem; left: 0.00rem; top: 0.37rem;">✔</span>
                    </p>
                </div>
            </div>
            <div class="group" style="width: 1.68rem; height: 1.68rem; display: block; left: 17.22rem; top: 14.27rem;">
                <svg viewbox="0.000000, 0.000000, 16.600000, 16.600000" class="graphic"
                    style="width: 1.66rem; height: 1.66rem; display: block; z-index: -10; left: 0.01rem; top: 0.01rem;">
                    <path stroke-width="0.250000" fill="none" d="M 0 0 L 16.567 0 L 16.567 16.567 L 0 16.567 L 0 0 Z"
                        stroke="#808285" stroke-opacity="1.000000" />
                </svg>
                <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACUAAAAlCAYAAADFniADAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAAt0lEQVRYhe3YIQ6DQBCF4beEpIoDbEK4RuXsBVpHr8N9KnqHFRX1cIUaUGhCUUuoe24R71eTUV8yblzXdT+cqBBCLHIjjoUQIgAr06Kqqm/TNO9cIO/9ZVmWOwDsqLquP23bPnKh+r5/TdMEADjV+VJCsQnFJhSbUGxCsQnFJhSbUGxCsQnFJhSbUGxCsQnFdkrU/gry3pfDMDxzQeZ5vqa5BAAzi+u63sZxzGX6qzCz6Jyz3JBjG7AeI/rfc/bvAAAAAElFTkSuQmCC"
                    class="image"
                    style="width: 1.38rem; height: 1.39rem; display: block; z-index: -10; left: 0.15rem; top: 0.14rem;" />
            </div>
            <div class="group" style="width: 1.68rem; height: 1.68rem; display: block; left: 22.30rem; top: 14.27rem;">
                <svg viewbox="0.000000, 0.000000, 16.600000, 16.600000" class="graphic"
                    style="width: 1.66rem; height: 1.66rem; display: block; z-index: -10; left: 0.01rem; top: 0.01rem;">
                    <path stroke-width="0.250000" fill="none" d="M 0 0 L 16.567 0 L 16.567 16.567 L 0 16.567 L 0 0 Z"
                        stroke="#808285" stroke-opacity="1.000000" />
                </svg>
                <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACUAAAAlCAYAAADFniADAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAAuElEQVRYhe3YIQ6DQBCF4beE1FQg1xDOUTd7AwSyh+E0Nb3FpqYXgAvUgUMTWrWEuucW8X41GfUl48b1ff/FiQohxCI34lgIIQKwMi2qqvo0TfPKBfLeX9d1bQFgR9V1/e667p4LNQzDc55nAMCpzpcSik0oNqHYhGITik0oNqHYhGITik0oNqHYhGITik0otlOi9leQ9/4yjuMjF2RZlluaSwAws7htWztNUy7TX4WZReec5YYc+wEyHSPuMe/cxwAAAABJRU5ErkJggg=="
                    class="image"
                    style="width: 1.39rem; height: 1.40rem; display: block; z-index: -10; left: 0.14rem; top: 0.14rem;" />
            </div>
            <div class="group" style="width: 1.68rem; height: 1.68rem; display: block; left: 27.38rem; top: 14.27rem;">
                <svg viewbox="0.000000, 0.000000, 16.600000, 16.600000" class="graphic"
                    style="width: 1.66rem; height: 1.66rem; display: block; z-index: -10; left: 0.01rem; top: 0.01rem;">
                    <path stroke-width="0.250000" fill="none" d="M 0 0 L 16.567 0 L 16.567 16.567 L 0 16.567 L 0 0 Z"
                        stroke="#808285" stroke-opacity="1.000000" />
                </svg>
                <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACUAAAAmCAYAAABDClKtAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAAuklEQVRYhe3YIQ6DQBCF4bcb/IomBCxHqF9IuMAeogfhJD1DL9CE7gWqEOgqPGpVqyDUPbeI96vJqC8ZN2YYhi9OVNu20eZGHPPeR2ttV2wL59ynrut3LlBVVQZAAIAd1TTNM4Rwy4Wa5/m+LAsA4FTn2xKKTSg2odiEYhOKTSg2odiEYhOKTSg2odiEYhOKTSg2odj2/1RZlpdpmh65IOu6Xre5AIC+72NKKaSUcpn+MuM4vgB0uSHHfjUyIonn2zbLAAAAAElFTkSuQmCC"
                    class="image"
                    style="width: 1.42rem; height: 1.42rem; display: block; z-index: -10; left: 0.13rem; top: 0.13rem;" />
            </div>
            <div class="group" style="width: 1.68rem; height: 1.68rem; display: block; left: 32.46rem; top: 14.27rem;">
                <svg viewbox="0.000000, 0.000000, 16.600000, 16.600000" class="graphic"
                    style="width: 1.66rem; height: 1.66rem; display: block; z-index: -10; left: 0.01rem; top: 0.01rem;">
                    <path stroke-width="0.250000" fill="none" d="M 0 0 L 16.567 0 L 16.567 16.567 L 0 16.567 L 0 0 Z"
                        stroke="#808285" stroke-opacity="1.000000" />
                </svg>
                <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACUAAAAlCAYAAADFniADAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAAt0lEQVRYhe3YIQ6DQBCF4beEpIoDbEK4RuXsBVpHr8N9KnqHFRX1cIUaUGhCUUuoe24R71eTUV8yblzXdT+cqBBCLHIjjoUQIgAr06Kqqm/TNO9cIO/9ZVmWOwDsqLquP23bPnKh+r5/TdMEADjV+VJCsQnFJhSbUGxCsQnFJhSbUGxCsQnFJhSbUGxCsQnFdkrU/gry3pfDMDxzQeZ5vqa5BAAzi+u63sZxzGX6qzCz6Jyz3JBjG7AeI/rfc/bvAAAAAElFTkSuQmCC"
                    class="image"
                    style="width: 1.38rem; height: 1.39rem; display: block; z-index: -10; left: 0.16rem; top: 0.14rem;" />
            </div>
            <p class="paragraph body-text"
                style="width: 3.67rem; height: 1.77rem; left: 3.65rem; top: 15.56rem; text-align: left; color: #6d6e71; font-weight: 300;">
                <span class="position style" style="width: 0.41rem; height: 1.04rem; left: 0.00rem; top: 0.81rem;">Full Name</span>
                    <span class="position style" style="color: black; width: 1.30rem; height: 5.50rem; left: 9.00rem; top: 0.93rem;"><?= filter_var($record['employee_name'], FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?></span>
            </p>
            <p class="paragraph body-text"
                style="width: 42.97rem; height: 1.64rem; font-size: 1.10rem; left: 14.28rem; top: 14.11rem; text-align: left; font-family: pro, serif; font-weight: 300;">
                <span class="position" style="width: 0.82rem; height: 0.98rem; left: 0.00rem; top: 0.46rem;">
                    <span class="style"></span>
                    <span class="style" style="font-family: 'pro', serif; font-size: 0.80rem; color: #58595b;">Mr</span>
                </span>
                <span class="position style"
                    style="width: 0.89rem; height: 0.98rem; font-size: 0.80rem; left: 5.05rem; top: 0.46rem; font-family: 'pro', serif; color: #58595b;">
                    Ms</span>
                <span class="position style"
                    style="width: 1.14rem; height: 0.98rem; font-size: 0.80rem; left: 10.03rem; top: 0.46rem; font-family: 'pro', serif; color: #58595b;">
                    Mrs</span>
                <span class="position style"
                    style="width: 0.74rem; height: 0.98rem; font-size: 0.80rem; left: 15.24rem; top: 0.46rem; font-family: 'pro', serif; color: #58595b;">
                    Dr</span>
                <span class="position style"
                    style="width: 1.83rem; height: 0.98rem; font-size: 0.80rem; left: 20.38rem; top: 0.66rem; font-family: 'pro', serif; color: #58595b;">
                    Other</span>
                <span class="position style"
                    style="width: 0.18rem; height: 0.98rem; font-size: 0.80rem; left: 22.38rem; top: 0.66rem; font-family: 'pro', serif; color: #58595b;">
                    ,</span>
                <span class="position style"
                    style="width: 1.02rem; height: 0.98rem; font-size: 0.80rem; left: 22.71rem; top: 0.66rem; font-family: 'pro', serif; color: #58595b;">
                    Ple</span>
                <span class="position style"
                    style="width: 1.10rem; height: 0.98rem; font-size: 0.80rem; left: 23.72rem; top: 0.66rem; font-family: 'pro', serif; color: #58595b;">ase</span>
                <span class="position style"
                    style="width: 2.25rem; height: 0.98rem; font-size: 0.80rem; left: 24.98rem; top: 0.66rem; font-family: 'pro', serif; color: #58595b;">
                    specify</span>
                <span class="position" style="width: 13.69rem; height: 0.98rem; left: 27.83rem; top: 0.66rem;">
                    <span class="style" style="font-family: 'pro', serif; font-size: 0.80rem; color: #58595b;">
                    </span>
                    <span class="style"
                        style="font-family: 'pro', serif; font-size: 0.80rem; color: #58595b; text-decoration: underline;">
                    </span>
                </span>
            </p>

            <p class="paragraph body-text"
                style="width: 52.20rem; height: 1.44rem; font-size: 0.60rem; left: 3.65rem; top: 17.33rem; text-align: left; color: #6d6e71; font-weight: 300;">
                <span class="position style" style="width: 0.71rem; height: 0.74rem; left: 0.00rem; top: -0.00rem;">(as in ID card/as in<br> PP for foreigners)</span>
            </p>
            <div class="group" style="width: 15.83rem; height: 0.03rem; display: block; left: 12.21rem; top: 20.57rem;">
                <svg viewbox="0.000000, -0.125000, 158.300000, 1.000000" class="graphic"
                    style="width: 15.83rem; height: 0.10rem; display: block; z-index: -10; left: 0.00rem; top: -0.00rem;">
                    <path stroke-width="0.250000" fill="none" d="M 0 0 L 158.291 0" stroke="#a7a9ac"
                        stroke-opacity="1.000000" />
                </svg>
            </div>

            <p class="paragraph body-text"
                style="width: 24.59rem; height: 1.65rem; left: 3.65rem; top: 18.77rem; text-align: left; color: #6d6e71; font-weight: 300;">
                <span class="position style" style="width: 0.72rem; height: 1.04rem; left: 0.00rem; top: 0.68rem;">ID
                    Card/ Passport No</span>
                    <span class="position style" style="color: black; width: 1.30rem; height: 5.50rem; left: 9.00rem; top: 0.93rem;"><?= filter_var($record['passport_nic_no'], FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?></span>
            </p>
            <div class="group" style="width: 13.00rem; height: 0.02rem; display: block; left: 42.81rem; top: 22.80rem;">
                <svg viewbox="0.000000, -0.124500, 130.050000, 1.000000" class="graphic"
                    style="width: 13.01rem; height: 0.10rem; display: block; z-index: -10; left: 0.00rem; top: 0.00rem;">
                    <path stroke-width="0.249000" fill="none" d="M 0 0 L 130.009 0" stroke="#a7a9ac"
                        stroke-opacity="1.000000" />
                </svg>
            </div>
            <p class="paragraph body-text"
                style="width: 24.59rem; height: 0.44rem; font-size: 0.60rem; left: 3.65rem; top: 20.68rem; text-align: left; color: #6d6e71; font-weight: 300;">
                <span class="position style"
                    style="width: 0.50rem; height: 0.74rem; left: 0.00rem; top: -0.30rem;">(Passport No. for foreigners only)</span>
            </p>
            <p class="paragraph body-text"
                style="width: 24.59rem; height: 1.59rem; left: 3.65rem; top: 21.12rem; text-align: left; color: #6d6e71; font-weight: 300;">
                <span class="position style" style="width: 1.77rem; height: 1.04rem; left: 0.00rem; top: 0.60rem;">Work Permit/Visa</span>
                <span class="position" style="width: 15.83rem; height: 1.04rem; left: 8.57rem; top: 0.60rem;">
                    <span class="style"> </span>
                    <span class="position style" style="color: black; width: 1.30rem; height: 5.50rem; left: 0.50rem; top: -0.10rem;"><?= filter_var($record['wp_no'], FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?></span>
                </span>
            </p>
            <p class="paragraph body-text"
                style="width: 24.48rem; height: 0.71rem; font-size: 0.60rem; left: 3.77rem; top: 22.70rem; text-align: left; color: #6d6e71; font-weight: 300;">
                <span class="position style" style="width: 0.33rem; height: 0.74rem; left: 0.00rem; top: -0.03rem;">(for foreigners)</span>
            </p>
            <p class="paragraph body-text"
                style="width: 24.59rem; height: 1.69rem; left: 3.65rem; top: 23.40rem; text-align: left; color: #6d6e71; font-weight: 300;">
                <span class="position style" style="width: 0.93rem; height: 1.04rem; left: 0.00rem; top: 0.66rem;">Date of Birth</span>
                <span class="position" style="width: 15.83rem; height: 1.04rem; left: 9.00rem; top: 0.66rem;">
                    <span class="style"> </span>
                    <span class="style" style="color: black; width: 1.30rem; height: 5.50rem; left: 0.50rem; top: 0.93rem;"> <?= date('d-M-Y', strtotime($record['dob'])) ?></span>
                </span>
            </p>
            <p class="paragraph body-text"
                style="width: 9.82rem; height: 1.70rem; font-size: 1.10rem; left: 32.35rem; top: 18.77rem; text-align: left; font-family: pro, serif; font-weight: 300;">
                <span class="position" style="width: 0.72rem; height: 1.04rem; left: 0.00rem; top: 0.68rem;">
                    <span class="style"></span>
                    <span class="style" style="font-family: 'pro', serif; font-size: 0.85rem; color: #6d6e71;">ID Card/Passport Expiry Date</span>
            </p>

            <p class="paragraph body-text" style="width: 9.82rem; height: 0.74rem; font-size: 0.60rem; left: 32.35rem; top: 20.47rem; text-align: left; color: #6d6e71; font-weight: 300;">
                <span class="position style"  style="width: 0.50rem; height: 0.74rem; left: 0.00rem; top: 0.01rem;">(Passport Expiry for foreigners only)</span>
            </p>

            <p class="paragraph body-text" style="width: 9.82rem; height: 1.48rem; left: 32.35rem; top: 21.21rem; text-align: left; color: #6d6e71; font-weight: 300;">
                <span class="position style" style="width: 1.77rem; height: 1.04rem; left: 0.00rem; top: 0.51rem;">Work Permit/Visa Expiry</span>
                
            </p>

            <p class="paragraph body-text" style="width: 9.81rem; height: 0.69rem; font-size: 0.60rem; left: 32.36rem; top: 22.69rem; text-align: left; color: #6d6e71; font-weight: 300;">
                <span class="position style"   style="width: 0.33rem; height: 0.74rem; left: 0.01rem; top: -0.04rem;">(Passport Expiry for foreigners only)</span>
            </p>

            <p class="paragraph body-text"
                style="width: 9.82rem; height: 1.71rem; left: 32.35rem; top: 23.39rem; text-align: left; color: #6d6e71; font-weight: 300;">
                <span class="position style"
                    style="width: 0.96rem; height: 1.04rem; left: 0.00rem; top: 0.67rem;">Nationality</span>
                    
            </p>

            <p class="paragraph body-text"
                style="width: 14.88rem; height: 0.56rem; font-size: 1.10rem; left: 42.37rem; top: 20.02rem; text-align: left; font-family: pro, serif; font-weight: 300;">
            </p>

            <div class="group" style="width: 13.00rem; height: 0.02rem; display: block; left: 42.81rem; top: 20.57rem;">
                <svg viewbox="0.000000, -0.124500, 130.050000, 1.000000" class="graphic"
                    style="width: 13.01rem; height: 0.10rem; display: block; z-index: -10; left: 0.00rem; top: 0.00rem;">
                    <path stroke-width="0.249000" fill="none" d="M 0 0 L 130.009 0" stroke="#a7a9ac"
                        stroke-opacity="1.000000" />
                </svg>
            </div>

            <p class="paragraph body-text" style="width: 14.35rem; height: 1.94rem; font-size: 0.90rem; left: 42.90rem; top: 20.68rem; text-align: left; font-family: pro, serif; font-weight: 400;">
                <span class="position style" style="width: 1.30rem; height: 1.01rem; left: 0.50rem; top: -1.15rem;"><?= date('d-M-Y', strtotime($record['passport_nic_no_expires'])) ?></span><!------ pp expirey date from wp table--->
            </p>
            <p class="paragraph body-text" style="width: 14.35rem; height: 1.94rem; font-size: 0.90rem; left: 42.90rem; top: 20.68rem; text-align: left; font-family: pro, serif; font-weight: 400;">
                <span class="position style" style="width: 1.30rem; height: 1.01rem; left: 0.20rem; top: 0.93rem;"><td><?= $expiry_date !== 'N/A' ? date('d-M-Y', strtotime($expiry_date)) : 'N/A' ?></td></span><!------ wp expirey dateb<?= $expiry_date ?> from wp table--->
            </p>
            <p class="paragraph body-text" style="width: 14.35rem; height: 1.94rem; font-size: 0.90rem; left: 42.90rem; top: 20.68rem; text-align: left; font-family: pro, serif; font-weight: 400;">
                <span class="position style" style="width: 1.30rem; height: 1.01rem; left: 0.00rem; top: 3.50rem;"><?= filter_var($record['nationality'], FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?></span>
            </p>
            <div class="group" style="width: 13.00rem; height: 0.02rem; display: block; left: 42.81rem; top: 22.80rem;">
                <svg viewbox="0.000000, -0.124500, 130.050000, 1.000000" class="graphic"
                    style="width: 13.01rem; height: 0.10rem; display: block; z-index: -10; left: 0.00rem; top: 0.00rem;">
                    <path stroke-width="0.249000" fill="none" d="M 0 0 L 130.009 0" stroke="#a7a9ac"
                        stroke-opacity="1.000000" />
                </svg>
            </div>
            <div class="group" style="width: 13.00rem; height: 0.02rem; display: block; left: 42.81rem; top: 25.04rem;">
                <svg viewbox="0.000000, -0.124500, 130.050000, 1.000000" class="graphic"
                    style="width: 13.01rem; height: 0.10rem; display: block; z-index: -10; left: 0.00rem; top: 0.00rem;">
                    <path stroke-width="0.249000" fill="none" d="M 0 0 L 130.009 0" stroke="#a7a9ac"
                        stroke-opacity="1.000000" />
                </svg>
            </div>
            <div class="group" style="width: 1.68rem; height: 1.68rem; display: block; left: 12.39rem; top: 26.83rem;">
                <svg viewbox="0.000000, 0.000000, 16.600000, 16.600000" class="graphic"
                    style="width: 1.66rem; height: 1.66rem; display: block; z-index: -10; left: 0.01rem; top: 0.01rem;">
                    <path stroke-width="0.250000" fill="none" d="M 0 0 L 16.567 0 L 16.567 16.567 L 0 16.567 L 0 0 Z"
                        stroke="#808285" stroke-opacity="1.000000" />
                </svg>
                <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACYAAAAlCAYAAAAuqZsAAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAAvklEQVRYhe3YIQ6DQBCF4beblaQaguIEvQABgm0dsofhNL1DVZM9QhGcoUGgUISkVSDWPbWI96vJqC8ZN6bv+x9OVtM03sZGhLVt6wHUbl8kSfItiuId0YQsyy7rut4B4IClafrpuu4RjwUMw/Ca5xkAcLpT7gnGJhibYGyCsQnGJhibYGyCsQnGJhibYGyCsQnGJhjbaWHHGyrP820cx2dMzLIs1312AFCWpTfG3KZpiqcKclVVeWttHRsS9gcUvyH8gvGCjgAAAABJRU5ErkJggg=="
                    class="image"
                    style="width: 1.40rem; height: 1.40rem; display: block; z-index: -10; left: 0.15rem; top: 0.13rem;" />
                <div class="textbox"
                    style="width: 1.68rem; height: 1.68rem; display: block; z-index: -10; left: 0.00rem; top: 0.00rem;">
                    <p class="paragraph body-text"
                        style="width: 1.21rem; height: 1.39rem; z-index: -10; font-size: 0.90rem; left: 0.47rem; top: -0.00rem; text-align: left; font-family: 'pro', serif; font-weight: 400;">
                        <span class="position style"
                            style="width: 0.90rem; height: 0.90rem; left: 0.00rem; top: 0.37rem;">✔</span>
                    </p>
                </div>
            </div>
            <div class="group" style="width: 1.68rem; height: 1.68rem; display: block; left: 18.84rem; top: 26.83rem;">
                <svg viewbox="0.000000, 0.000000, 16.600000, 16.600000" class="graphic"
                    style="width: 1.66rem; height: 1.66rem; display: block; z-index: -10; left: 0.01rem; top: 0.01rem;">
                    <path stroke-width="0.250000" fill="none" d="M 0 0 L 16.567 0 L 16.567 16.567 L 0 16.567 L 0 0 Z"
                        stroke="#808285" stroke-opacity="1.000000" />
                </svg>
                <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACUAAAAlCAYAAADFniADAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAAtklEQVRYhe3YIQ6DQBCF4bcEUcMBNtlwjcrZCyDpdbhPRe+woqIerlADCk1oFQTcc4t4v5qM+pJx47qu++FCmVkqciOOmVlyzlm5Laqq+oYQPrlA3vtyXdcGAHZUXdfvtm0fuVDDMDzHcQQAXOp8W0KxCcUmFJtQbEKxCcUmFJtQbEKxCcUmFJtQbEKxCcV2SdT+CvLe3/q+f+WCzPN83+YSAGKMaVmWZpqmXKZTRYwxAbDckGN/Uz4j+qCws/MAAAAASUVORK5CYII="
                    class="image"
                    style="width: 1.39rem; height: 1.38rem; display: block; z-index: -10; left: 0.15rem; top: 0.14rem;" />
            </div>
            <p class="paragraph body-text"
                style="width: 20.09rem; height: 1.54rem; left: 3.65rem; top: 26.60rem; text-align: left; color: #6d6e71; font-weight: 300;">
                <span class="position style"
                    style="width: 2.52rem; height: 1.04rem; left: 0.00rem; top: 0.50rem;">Gender</span>
                <span class="position" style="width: 1.53rem; height: 0.98rem; left: 11.05rem; top: 0.54rem;">
                    <span class="style"> </span>
                    <span class="style" style="font-size: 0.80rem; color: #58595b;">Male</span>
                </span>
                <span class="position style"
                    style="width: 0.38rem; height: 0.98rem; font-size: 0.80rem; left: 17.51rem; top: 0.54rem; color: #58595b;">
                    Female</span>
            </p>
            <p class="paragraph body-text"
                style="width: 4.86rem; height: 1.54rem; font-size: 1.10rem; left: 32.36rem; top: 26.60rem; text-align: left; font-family: pro, serif; font-weight: 300;">
                <span class="position" style="width: 1.75rem; height: 1.04rem; left: 0.00rem; top: 0.50rem;">
                    <span class="style"></span>
                    <span class="style" style="font-family: 'pro', serif; font-size: 0.85rem; color: #6d6e71;">Marital
                        Status</span>
            </p>
            <p class="paragraph body-text"
                style="width: 12.12rem; height: 1.54rem; font-size: 1.10rem; left: 45.14rem; top: 26.60rem; text-align: left; font-family: pro, serif; font-weight: 300;">
                <span class="position" style="width: 2.12rem; height: 1.04rem; left: -0.00rem; top: 0.50rem;">
                    <span class="style"></span>
                    <span class="style"
                        style="font-family: 'pro', serif; font-size: 0.85rem; color: #58595b;">Single</span>
                </span>
                <span class="position style"
                    style="width: 2.62rem; height: 1.04rem; font-size: 0.85rem; left: 5.56rem; top: 0.50rem; font-family: 'pro', serif; color: #58595b;">
                    Married</span>
            </p>
            <div class="group" style="width: 1.68rem; height: 1.68rem; display: block; left: 42.87rem; top: 26.83rem;">
                <svg viewbox="0.000000, 0.000000, 16.600000, 16.600000" class="graphic"
                    style="width: 1.66rem; height: 1.66rem; display: block; z-index: -10; left: 0.01rem; top: 0.01rem;">
                    <path stroke-width="0.250000" fill="none" d="M 0 0 L 16.567 0 L 16.567 16.567 L 0 16.567 L 0 0 Z"
                        stroke="#808285" stroke-opacity="1.000000" />
                </svg>
                <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACsAAAArCAYAAADhXXHAAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAAuUlEQVRYhe3ZIRKDMBSE4S0guUFyj8q+AyAqc5tciSsg8TlEBQqfaUUnKARmJ2RmP5WJ+sVz+4gxftEAM1u62hFXmNkC4DWUj3EcP977tWLTKedcn3OeAOCI9d6vIYR3vaxzKaV52zYAQBNnUCiWRbEsimVRLItiWRTLolgWxbIolkWxLIplUSyLYlkUy6JYFsWyKJalqdhjAHHO9SmluWbMmX3fn+U9AP+dKec8lVXkrroyiNUOueIH/nIkFXZVOjYAAAAASUVORK5CYII="
                    class="image"
                    style="width: 1.60rem; height: 1.60rem; display: block; z-index: -10; left: 0.04rem; top: 0.07rem;" />
            </div>
            <div class="group" style="width: 1.68rem; height: 1.70rem; display: block; left: 48.48rem; top: 26.81rem;">
                <svg viewbox="0.000000, 0.000000, 16.600000, 16.600000" class="graphic"
                    style="width: 1.66rem; height: 1.66rem; display: block; z-index: -10; left: 0.01rem; top: 0.03rem;">
                    <path stroke-width="0.250000" fill="none" d="M 0 0 L 16.567 0 L 16.567 16.567 L 0 16.567 L 0 0 Z"
                        stroke="#808285" stroke-opacity="1.000000" />
                </svg>
                <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACsAAAArCAYAAADhXXHAAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAAuUlEQVRYhe3ZIRKDMBSE4S0guUFyj8q+AyAqc5tciSsg8TlEBQqfaUUnKARmJ2RmP5WJ+sVz+4gxftEAM1u62hFXmNkC4DWUj3EcP977tWLTKedcn3OeAOCI9d6vIYR3vaxzKaV52zYAQBNnUCiWRbEsimVRLItiWRTLolgWxbIolkWxLIplUSyLYlkUy6JYFsWyKJalqdhjAHHO9SmluWbMmX3fn+U9AP+dKec8lVXkrroyiNUOueIH/nIkFXZVOjYAAAAASUVORK5CYII="
                    class="image"
                    style="width: 1.60rem; height: 1.60rem; display: block; z-index: -10; left: 0.05rem; top: 0.00rem;" />
                <div class="textbox"
                    style="width: 1.68rem; height: 1.70rem; display: block; z-index: -10; left: 0.00rem; top: 0.00rem;">
                    <p class="paragraph body-text"
                        style="width: 1.22rem; height: 1.36rem; z-index: -10; font-size: 0.90rem; left: 0.46rem; top: 0.00rem; text-align: left; font-family: 'pro', serif; font-weight: 400;">
                        <span class="position style"
                            style="width: 0.90rem; height: 0.90rem; left: 0.00rem; top: 0.33rem;">✔</span>
                    </p>
                </div>
            </div>
            <p class="paragraph body-text"
                style="width: 24.59rem; height: 1.54rem; left: 3.65rem; top: 29.40rem; text-align: left; color: #6d6e71; font-weight: 300;">
                <span class="position style" style="width: 1.00rem; height: 1.04rem; left: 0.00rem; top: 0.50rem;">No of Dependents</span>
                <span class="position" style=" color: black;width: 15.83rem; height: 1.04rem; left: 8.57rem; top: 0.50rem;">
                    <span class="style"> </span>
                    <span class="style" style="font-weight: 500; text-decoration: bold;"> </span>
                </span>
            </p>
            <p class="paragraph body-text"
                style="width: 24.89rem; height: 1.52rem; font-size: 1.10rem; left: 32.35rem; top: 29.40rem; text-align: left; font-family: pro, serif; font-weight: 300;">
                <span class="position" style="width: 0.47rem; height: 1.04rem; left: 0.01rem; top: 0.50rem;">
                    <span class="style"></span>
                    <span class="style"
                        style="font-family: 'pro', serif; font-size: 0.85rem; color: #6d6e71;">Passphrase</span>
                        <span class="position style"  style="width: 1.30rem; height: 1.01rem; left: 12.00rem; top: 0.93rem;"><?= filter_var($record['emp_no'], FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?></span>
                    <span class="position" style="width: 13.00rem; height: 1.04rem; left: 10.45rem; top: 0.50rem;">
                        <span class="style" style="font-family: 'pro', serif; font-size: 0.85rem; color: #6d6e71;">
                        </span>
                        <span class="style"
                            style="font-family: 'pro', serif; font-size: 0.85rem; color: #6d6e71; text-decoration: underline;">
                        </span>
                    </span>
            </p>
            <p class="paragraph body-text"
                style="width: 24.89rem; height: 0.74rem; font-size: 0.60rem; left: 32.35rem; top: 30.93rem; text-align: left; color: #6d6e71; font-weight: 300;">
                <span class="position style" style="width: 1.28rem; height: 0.74rem; left: 0.00rem; top: 0.01rem;">*4-10
                    characters, letters and numbers only</span>
            </p>
            <div class="group" style="width: 52.43rem; height: 0.05rem; display: block; left: 3.40rem; top: 32.64rem;">
                <svg viewbox="0.000000, -0.250000, 524.350000, 1.000000" class="graphic"
                    style="width: 52.44rem; height: 0.10rem; display: block; z-index: -10; left: 0.00rem; top: -0.00rem;">
                    <path stroke-width="0.500000" fill="none" d="M 524.332 0 L 0 0" stroke="#939598"
                        stroke-opacity="1.000000" />
                </svg>
            </div>
            <p class="paragraph body-text"
                style="width: 4.57rem; height: 2.56rem; left: 3.50rem; top: 33.44rem; text-align: left; font-family: 'pro', serif; color: #808285; font-weight: 400;">
                <span class="position style"
                    style="width: 0.45rem; height: 1.07rem; left: 0.00rem; top: 0.50rem;">Educational
                    <br>Qualification</span>
            </p>
            <p class="paragraph body-text"
                style="width: 2.04rem; height: 2.07rem; font-size: 1.10rem; left: 14.20rem; top: 33.44rem; text-align: left; font-family: pro, serif; font-weight: 300;">
                <span class="position" style="width: 1.83rem; height: 1.04rem; left: 0.00rem; top: 1.03rem;">
                    <span class="style"></span>
                    <span class="style"
                        style="font-family: 'pro', serif; font-size: 0.85rem; color: #58595b;">Basic</span>
                </span>
            </p>
            <p class="paragraph body-text"
                style="width: 1.44rem; height: 2.07rem; font-size: 1.10rem; left: 20.78rem; top: 33.44rem; text-align: left; font-family: pro, serif; font-weight: 300;">
                <span class="position" style="width: 0.82rem; height: 1.04rem; left: -0.00rem; top: 1.03rem;">
                    <span class="style"></span>
                    <span class="style"
                        style="font-family: 'pro', serif; font-size: 0.85rem; color: #58595b;">O/L</span>
                </span>
            </p>
            <p class="paragraph body-text"
                style="width: 1.36rem; height: 2.07rem; font-size: 1.10rem; left: 27.20rem; top: 33.44rem; text-align: left; font-family: pro, serif; font-weight: 300;">
                <span class="position" style="width: 1.15rem; height: 1.04rem; left: 0.00rem; top: 1.03rem;">
                    <span class="style"></span>
                    <span class="style"
                        style="font-family: 'pro', serif; font-size: 0.85rem; color: #58595b;">A/L</span>
                </span>
            </p>
            <p class="paragraph body-text"
                style="width: 3.14rem; height: 2.07rem; font-size: 1.10rem; left: 33.58rem; top: 33.44rem; text-align: left; font-family: pro, serif; font-weight: 300;">
                <span class="position" style="width: 2.93rem; height: 1.04rem; left: 0.00rem; top: 1.03rem;">
                    <span class="style"></span>
                    <span class="style"
                        style="font-family: 'pro', serif; font-size: 0.85rem; color: #58595b;">Diploma</span>
                </span>
            </p>
            <p class="paragraph body-text"
                style="width: 2.61rem; height: 2.07rem; font-size: 1.10rem; left: 40.10rem; top: 33.44rem; text-align: left; font-family: pro, serif; font-weight: 300;">
                <span class="position" style="width: 0.92rem; height: 1.04rem; left: 0.00rem; top: 1.03rem;">
                    <span class="style"></span>
                    <span class="style"
                        style="font-family: 'pro', serif; font-size: 0.85rem; color: #58595b;">Degree</span>
                </span>
            </p>
            <p class="paragraph body-text"
                style="width: 10.63rem; height: 2.07rem; font-size: 1.10rem; left: 46.61rem; top: 33.44rem; text-align: left; font-family: pro, serif; font-weight: 300;">
                <span class="position" style="width: 1.36rem; height: 1.04rem; left: 0.00rem; top: 1.03rem;">
                    <span class="style"></span>
                    <span class="style"
                        style="font-family: 'pro', serif; font-size: 0.85rem; color: #58595b;">Masters</span>
                </span>
                <span class="position style"
                    style="width: 1.52rem; height: 1.04rem; font-size: 0.85rem; left: 6.39rem; top: 1.03rem; font-family: 'pro', serif; color: #58595b;">PHD</span>
            </p>
            <div class="group" style="width: 1.68rem; height: 1.68rem; display: block; left: 44.43rem; top: 34.20rem;">
                <svg viewbox="0.000000, 0.000000, 16.600000, 16.600000" class="graphic"
                    style="width: 1.66rem; height: 1.66rem; display: block; z-index: -10; left: 0.01rem; top: 0.01rem;">
                    <path stroke-width="0.250000" fill="none" d="M 0 0 L 16.567 0 L 16.567 16.567 L 0 16.567 L 0 0 Z"
                        stroke="#808285" stroke-opacity="1.000000" />
                </svg>
                <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACUAAAAlCAYAAADFniADAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAAtUlEQVRYhe3YsQnDMBCF4ZfgMgRcqLUXCUgjCG+QSTRKyCgO2UGuUlulaxmSSsbpXicX76+Oqz647k4hhC8OlHNuPNdG7HPOjQBsUxZt2376vn/VAhljrjnnAQA2VNd1b+/9vRYqxvhMKQEADnW+klBsQrEJxSYUm1BsQrEJxSYUm1BsQrEJxSYUm1BsQrEdErW9gowxl2maHrUgy7LcytwAgLV2XNd1mOe5lumvprz0akP2/QBrDSPe2onJ/gAAAABJRU5ErkJggg=="
                    class="image"
                    style="width: 1.41rem; height: 1.41rem; display: block; z-index: -10; left: 0.15rem; top: 0.14rem;" />
            </div>
            <div class="group" style="width: 1.68rem; height: 1.68rem; display: block; left: 50.88rem; top: 34.20rem;">
                <svg viewbox="0.000000, 0.000000, 16.600000, 16.600000" class="graphic"
                    style="width: 1.66rem; height: 1.66rem; display: block; z-index: -10; left: 0.01rem; top: 0.01rem;">
                    <path stroke-width="0.250000" fill="none" d="M 0 0 L 16.567 0 L 16.567 16.567 L 0 16.567 L 0 0 Z"
                        stroke="#808285" stroke-opacity="1.000000" />
                </svg>
                <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACYAAAAmCAYAAACoPemuAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAAtElEQVRYhe3YoQ3DMBSE4WsUVUXhlpxBiuwJwqKM43mqTmFQdYI3QlFYcKIWVA4IO+SA+5Fl9EmP3SWl9MXJijHmpjbiWAghAwht+ei67uO9f1c0wTl33bZtAIAd1vf9axzHqR4LMLPHPM8AgNOdsiQYm2BsgrEJxiYYm2BsgrEJxiYYm2BsgrEJxiYYm2BsgrHt+5hz7mZmz5qYZVnu5d0C/81zXdehjGZnqIkxZgChNuTYD26pI/lOuC8QAAAAAElFTkSuQmCC"
                    class="image"
                    style="width: 1.43rem; height: 1.42rem; display: block; z-index: -10; left: 0.13rem; top: 0.12rem;" />
            </div>
            <div class="group" style="width: 1.68rem; height: 1.68rem; display: block; left: 37.98rem; top: 34.20rem;">
                <svg viewbox="0.000000, 0.000000, 16.600000, 16.600000" class="graphic"
                    style="width: 1.66rem; height: 1.66rem; display: block; z-index: -10; left: 0.01rem; top: 0.01rem;">
                    <path stroke-width="0.250000" fill="none" d="M 0 0 L 16.567 0 L 16.567 16.567 L 0 16.567 L 0 0 Z"
                        stroke="#808285" stroke-opacity="1.000000" />
                </svg>
                <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACYAAAAlCAYAAAAuqZsAAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAAvElEQVRYhe3YrQ3DMBDF8VcrPGVRWHYoztcCkTJBR/EsnaAjVB6gzJIHKIoUFhbiolRV2EM2eH90OvSTjt3FWhuRWcMwOJMacW4cRwegL45FWZafpmleCU2oquq67/sEAD9YXdfveZ7v6ViA9/65risAILtTHgnGJhibYGyCsQnGJhibYGyCsQnGJhibYGyCsQnGJhhbtrD/N5QJITxSYrZtux1zAQBt27oY47QsSzrVqaLrOmeM6VNDzn0BnXUj6opvgnMAAAAASUVORK5CYII="
                    class="image"
                    style="width: 1.42rem; height: 1.41rem; display: block; z-index: -10; left: 0.14rem; top: 0.14rem;" />
            </div>
            <div class="group" style="width: 1.68rem; height: 1.68rem; display: block; left: 31.53rem; top: 34.20rem;">
                <svg viewbox="0.000000, 0.000000, 16.600000, 16.600000" class="graphic"
                    style="width: 1.66rem; height: 1.66rem; display: block; z-index: -10; left: 0.01rem; top: 0.01rem;">
                    <path stroke-width="0.250000" fill="none" d="M 0 0 L 16.567 0 L 16.567 16.567 L 0 16.567 L 0 0 Z"
                        stroke="#808285" stroke-opacity="1.000000" />
                </svg>
                <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACYAAAAlCAYAAAAuqZsAAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAAu0lEQVRYhe3YsQ2DMBCF4efIXcQAiMJDpEaGAejcZBrGyQCZwAOkQmKFSEhUlBROBYroXmUX769OV33SdWfGcUworK7r4i034lrf9xGAt8eiqqqvcy5mNKGu6/u+7wMA2L/lJ4TwzMcCpml6r+sKACjulEeCsQnGJhibYGyCsQnGJhibYGyCsQnGJhibYGyCsQnGVizsfEM1TZPmeX7lxGzb9jhmCwBt28aU0rAsSz7VJeu9j8YYnxty7QfznST5yIicMAAAAABJRU5ErkJggg=="
                    class="image"
                    style="width: 1.41rem; height: 1.39rem; display: block; z-index: -10; left: 0.14rem; top: 0.14rem;" />
            </div>
            <div class="group" style="width: 1.68rem; height: 1.68rem; display: block; left: 25.08rem; top: 34.20rem;">
                <svg viewbox="0.000000, 0.000000, 16.600000, 16.600000" class="graphic"
                    style="width: 1.66rem; height: 1.66rem; display: block; z-index: -10; left: 0.01rem; top: 0.01rem;">
                    <path stroke-width="0.250000" fill="none" d="M 0 0 L 16.567 0 L 16.567 16.567 L 0 16.567 L 0 0 Z"
                        stroke="#808285" stroke-opacity="1.000000" />
                </svg>
                <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACUAAAAlCAYAAADFniADAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAAuklEQVRYhe3YIQ6DQBCF4bcNoqpBgSHhGlXsHgFO0KNwlqqeY6t6ArC1uxJLCFUQ6p5bxPvVZNSXjBvT9/2KE+Wc85fUiGPOOQ/AZtsiz/NvXdfvVKCiKG7zPLcAsKOqqvp0XfdIhRqG4RVjBACc6nxbQrEJxSYUm1BsQrEJxSYUm1BsQrEJxSYUm1BsQrEJxXZK1P4KKsvyOo7jMxVkmqb7NmcA0DSNX5alDSGkMv2VWWu9Mcamhhz7AcvFI+T1PLdzAAAAAElFTkSuQmCC"
                    class="image"
                    style="width: 1.39rem; height: 1.41rem; display: block; z-index: -10; left: 0.14rem; top: 0.14rem;" />
            </div>
            <div class="group" style="width: 1.68rem; height: 1.68rem; display: block; left: 18.63rem; top: 34.20rem;">
                <svg viewbox="0.000000, 0.000000, 16.600000, 16.600000" class="graphic"
                    style="width: 1.66rem; height: 1.66rem; display: block; z-index: -10; left: 0.01rem; top: 0.01rem;">
                    <path stroke-width="0.250000" fill="none" d="M 0 0 L 16.567 0 L 16.567 16.567 L 0 16.567 L 0 0 Z"
                        stroke="#808285" stroke-opacity="1.000000" />
                </svg>
                <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACYAAAAlCAYAAAAuqZsAAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAAv0lEQVRYhe3YIQ6DQBCF4beb1ZgmhLWcoAcgQNaTcIWehKP0ELU19ARNMBygAoNDYagCgXtqEe9Xk1FfMm5M13UbLlZd172NjTgXQvgAqNy+SJLkl+f5O6IJaZre1nVtAOCAZVn2bdv2EY8FDMPwmucZAHC5U+4JxiYYm2BsgrEJxiYYm2BsgrEJxiYYm2BsgrEJxiYY22VhxxvKe7+N4/iMiVmW5b7PDgCKouiNMc00TfFUp1xZlr21tooNOfcHbroh7vM73AcAAAAASUVORK5CYII="
                    class="image"
                    style="width: 1.41rem; height: 1.41rem; display: block; z-index: -10; left: 0.13rem; top: 0.14rem;" />
            </div>
            <div class="group" style="width: 1.68rem; height: 1.68rem; display: block; left: 12.18rem; top: 34.20rem;">
                <svg viewbox="0.000000, 0.000000, 16.600000, 16.600000" class="graphic"
                    style="width: 1.66rem; height: 1.66rem; display: block; z-index: -10; left: 0.01rem; top: 0.01rem;">
                    <path stroke-width="0.250000" fill="none" d="M 0 0 L 16.567 0 L 16.567 16.567 L 0 16.567 L 0 0 Z"
                        stroke="#808285" stroke-opacity="1.000000" />
                </svg>
                <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACYAAAAmCAYAAACoPemuAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAAtUlEQVRYhe3YIRKDMBCF4RcGyQkg16je+NbUcRuOhOgJoioq4RKdAYWGVgUR91QQ71c7q76ZdeuGYfjhYoUQYlUakRdCiACsToumab7e+3dBE7quq/d9fwDACWvb9tP3/bMcC5im6bWuKwDgcqdMCcYmGJtgbIKxCcYmGJtgbIKxCcYmGJtgbIKxCcYmGJtgbOd/zHvv5nkeS2K2bbuluQYAM4vHcdyXZSmnyqrMLDrnrDQk7w+BLSQL6166sQAAAABJRU5ErkJggg=="
                    class="image"
                    style="width: 1.41rem; height: 1.42rem; display: block; z-index: -10; left: 0.13rem; top: 0.13rem;" />
                <div class="textbox"
                    style="width: 1.68rem; height: 1.68rem; display: block; z-index: -10; left: 0.00rem; top: -0.00rem;">
                    <p class="paragraph body-text"
                        style="width: 1.24rem; height: 1.39rem; z-index: -10; font-size: 0.90rem; left: 0.45rem; top: -0.00rem; text-align: left; font-family: 'pro', serif; font-weight: 400;">
                        <span class="position style"
                            style="width: 0.90rem; height: 0.90rem; left: 0.00rem; top: 0.37rem;">✔</span>
                    </p>
                </div>
            </div>
            <div class="group" style="width: 1.68rem; height: 1.68rem; display: block; left: 12.09rem; top: 37.20rem;">
                <svg viewbox="0.000000, 0.000000, 16.600000, 16.600000" class="graphic"
                    style="width: 1.66rem; height: 1.66rem; display: block; z-index: -10; left: 0.01rem; top: 0.01rem;">
                    <path stroke-width="0.250000" fill="none" d="M 0 0 L 16.567 0 L 16.567 16.567 L 0 16.567 L 0 0 Z"
                        stroke="#808285" stroke-opacity="1.000000" />
                </svg>
                <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACsAAAArCAYAAADhXXHAAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAAuUlEQVRYhe3ZIRKDMBSE4S0guUFyj8q+AyAqc5tciSsg8TlEBQqfaUUnKARmJ2RmP5WJ+sVz+4gxftEAM1u62hFXmNkC4DWUj3EcP977tWLTKedcn3OeAOCI9d6vIYR3vaxzKaV52zYAQBNnUCiWRbEsimVRLItiWRTLolgWxbIolkWxLIplUSyLYlkUy6JYFsWyKJalqdhjAHHO9SmluWbMmX3fn+U9AP+dKec8lVXkrroyiNUOueIH/nIkFXZVOjYAAAAASUVORK5CYII="
                    class="image"
                    style="width: 1.60rem; height: 1.60rem; display: block; z-index: -10; left: 0.06rem; top: 0.02rem;" />
            </div>
            <p class="paragraph body-text"
                style="width: 42.90rem; height: 1.15rem; left: 14.36rem; top: 37.35rem; text-align: left; font-family: 'pro', serif; color: #58595b; font-weight: 300;">
                <span class="position style"
                    style="width: 0.57rem; height: 0.96rem; left: 0.00rem; top: 0.19rem;">Other, specify</span>
            </p>
            <div class="group" style="width: 36.31rem; height: 0.03rem; display: block; left: 19.50rem; top: 38.62rem;">
                <svg viewbox="0.000000, -0.125000, 363.100000, 1.000000" class="graphic"
                    style="width: 36.31rem; height: 0.10rem; display: block; z-index: -10; left: 0.00rem; top: 0.00rem;">
                    <path stroke-width="0.250000" fill="none" d="M 363.066 0 L 0 0" stroke="#a7a9ac"
                        stroke-opacity="1.000000" />
                </svg>
            </div>
            <div class="group" style="width: 52.33rem; height: 2.12rem; display: block; left: 3.59rem; top: 40.29rem;">
                <div class="textbox"
                    style="background: #2a9a47; width: 46.12rem; height: 2.12rem; display: block; z-index: -10; left: 6.21rem; top: 0.00rem;">
                    <p class="paragraph"
                        style="width: 45.26rem; height: 1.69rem; z-index: -10; font-size: 1.00rem; left: 0.86rem; top: 0.00rem; text-align: left; font-family: 'pro', serif; color: #ffffff; font-weight: 600;">
                        <span class="position style"
                            style="width: 4.13rem; height: 1.29rem; left: 0.00rem; top: 0.43rem;">CONTACT INFORMATION</span>
                    </p>
                </div>
                <div class="textbox"
                    style="background: #222e65; width: 6.21rem; height: 2.12rem; display: block; z-index: -10; left: 0.00rem; top: 0.00rem;">
                    <p class="paragraph"
                        style="width: 5.60rem; height: 1.69rem; z-index: -10; font-size: 1.00rem; left: 0.61rem; top: 0.00rem; text-align: left; font-family: 'pro', serif; color: #ffffff; font-weight: 600;">
                        <span class="position style"
                            style="width: 1.66rem; height: 1.29rem; left: 0.00rem; top: 0.43rem;">SECTION B</span>
                    </p>
                </div>
            </div>
            <p class="paragraph body-text"
                style="width: 53.66rem; height: 1.07rem; left: 3.59rem; top: 43.79rem; text-align: left; color: #616262; font-weight: 300;">
                <span class="position style"
                    style="width: 2.32rem; height: 1.04rem; left: 0.00rem; top: 0.03rem;">Mobile Number</span>
                    <span class="position style" style="width: 16.61rem; height: 1.04rem; left: 6.47rem; top: 0.00rem;">
                    <span class="style" style="font-weight: 00; color: black; text-decoration: bold;"> <?= filter_var($record['contact_number'], FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?></span>
                </span>
                <span class="position" style="width: 1.99rem; height: 1.04rem; left: 28.24rem; top: 0.03rem;">
                   <span class="style"> Office No.</span>
                   <span class="style" style="font-weight: 00; color: black; text-decoration: bold;"> 331 7878</span>
                    
                </span>
                <span class="position" style="width: 16.98rem; height: 1.04rem; left: 6.00rem; top: 2.50rem;">
                    <span class="style"> </span>
                    <span class="style" style="font-weight: 00; color: black; text-decoration: bold;"> <?= filter_var($record['emp_email'], FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?></span>
                </span>
            </p>
            <p class="paragraph body-text"
                style="width: 53.77rem; height: 1.14rem; left: 3.48rem; top: 46.21rem; text-align: left; color: #616262; font-weight: 300;">
                <span class="position style" style="width: 1.94rem; height: 1.04rem; left: 0.00rem; top: 0.09rem;">Email
                    Address</span>
                <span class="position" style="width: 16.41rem; height: 1.11rem; left: 8.66rem; top: 0.04rem;">
                    <span class="style"> </span>
                    <span class="style" style="text-decoration: underline;"> </span>
                </span>
            </p>
            <p class="paragraph body-text"
                style="width: 53.77rem; height: 1.13rem; font-size: 0.90rem; left: 3.48rem; top: 49.55rem; text-align: left; font-family: 'pro', serif; color: #808285; font-weight: 600;">
                <span class="position style"
                    style="width: 4.33rem; height: 1.16rem; left: 0.00rem; top: -0.00rem;">Permanent Address</span>
                    <span class="position" style="width: 16.98rem; height: 1.04rem; left: 10.00rem; top: 2.30rem;">
                    <span class="style"> </span>
                    <span class="style" style="font-weight: 500; color: black; text-decoration: bold;"> <?= wordwrap(filter_var($record['permanent_address'], FILTER_SANITIZE_FULL_SPECIAL_CHARS), 45, "<br>\n", true) ?>
</span>
                </span>

            </p>

            <p class="paragraph body-text"
                style="width: 25.08rem; height: 5.74rem; font-size: 0.90rem; left: 3.48rem; top: 51.37rem; text-align: left; color: #616262; font-weight: 300;">
                <span class="position style" style="width: 5.68rem; height: 1.11rem; left: 0.00rem; top: 0.50rem;">House
                    / Building Name</span>

                <span class="position style" style="width: 16.41rem; height: 1.11rem; left: 8.66rem; top: 0.50rem;">
                </span>
                <span class="position" style="width: 1.37rem; height: 1.11rem; left: 0.00rem; top: 3.14rem;">
                    <span class="style" style="text-decoration: underline;"> </span>
                    <span class="style"> Flat No/Floor</span>
                </span>
                <span class="position" style="width: 16.41rem; height: 1.11rem; left: 8.66rem; top: 3.14rem;">
                    <span class="style"> </span>
                    <span class="style" style="text-decoration: underline;"> </span>
                </span>
            </p>

            <p class="paragraph body-text"
                style="width: 24.11rem; height: 5.31rem; font-size: 1.10rem; left: 31.82rem; top: 51.37rem; text-align: left; font-family: pro, serif; font-weight: 300;">
                <span class="position" style="width: 1.86rem; height: 1.11rem; left: 0.00rem; top: 0.50rem;">
                    <span class="style"></span>
                    <span class="style"
                        style="font-family: 'pro', serif; font-size: 0.90rem; color: #616262;">Atoll, Island/City</span>
                </span>
                <span class="position style"
                    style="width: 17.02rem; height: 1.11rem; font-size: 0.90rem; left: 7.07rem; top: 0.50rem; font-family: 'pro', serif; color: #616262;">
                </span>
                <span class="position" style="width: 2.90rem; height: 1.11rem; left: 0.00rem; top: 2.92rem;">
                    
                    <span class="style" style="font-family: 'pro', serif; font-size: 0.90rem; color: #616262;">
                        Country</span>
                </span>
                
            </p>

            <p class="paragraph body-text" style="width: 53.77rem; height: 1.13rem; font-size: 0.90rem; left: 3.48rem; top: 57.10rem; text-align: left; color: #616262; font-weight: 300;">
                <span class="position style"  style="width: 1.04rem; height: 1.11rem; left: 0.00rem; top: 0.04rem;">Street Name</span>
                <span class="position" style="width: 16.41rem; height: 1.11rem; left: 8.66rem; top: 0.04rem;">
                    <span class="style"> </span>
                    <span class="style" style="text-decoration: underline;"> </span>
                </span>
            </p>


            <p class="paragraph body-text" style="width: 53.77rem; height: 1.14rem; font-size: 0.90rem; left: 3.48rem; top: 59.95rem; text-align: left; font-family: 'pro', serif; color: #808285; font-weight: 600;">
                <span class="position style" style="width: 2.99rem; height: 1.16rem; left: 0.00rem; top: 0.00rem;"><b>Present Address</b> (if different from permanent)</span>
            </p>
            <p class="paragraph body-text"
                style="width: 8.10rem; height: 5.34rem; font-size: 0.90rem; left: 3.48rem; top: 62.34rem; text-align: left; color: #616262; font-weight: 300;">
                <span class="position style" style="width: 5.68rem; height: 1.11rem; left: 0.00rem; top: 0.04rem;">House/Building Name</span>
                <span class="position style" style="width: 1.37rem; height: 1.11rem; left: 0.00rem; top: 2.71rem;"> Flat No/Floor</span>
            </p>
            <svg viewbox="0.000000, -0.129500, 163.600000, 1.000000" class="graphic"
                style="width: 16.36rem; height: 0.10rem; display: block; z-index: 10; left: 12.14rem; top: 63.37rem;">
                <path stroke-width="0.259000" fill="none" d="M 0 -2.84217e-14 L 163.595 -2.84217e-14" stroke="#a7a9ac"
                    stroke-opacity="1.000000" />
            </svg>
            <svg viewbox="0.000000, -0.128500, 163.600000, 1.000000" class="graphic"
                style="width: 16.36rem; height: 0.10rem; display: block; z-index: 10; left: 12.14rem; top: 63.37rem;">
                <path stroke-width="0.259000" fill="none" d="M 0 -2.84217e-14 L 163.595 -2.84217e-14" stroke="#a7a9ac"
                    stroke-opacity="1.000000" />
            </svg>
            
            <p class="paragraph body-text" style="width: 8.13rem; height: 1.06rem; font-size: 0.90rem; left: 3.48rem; top: 67.68rem; text-align: left; color: #616262; font-weight: 300;">
                <span class="position style"  style="width: 1.04rem; height: 1.11rem; left: 0.00rem; top: -0.04rem;">Street Name</span>
            </p>
            
            <p class="paragraph body-text" style="width: 8.13rem; height: 2.18rem; font-size: 0.90rem; left: 3.48rem; top: 68.74rem; text-align: left; font-family: 'pro', serif; color: #808285; font-weight: 600;">
                <span class="position style"  style="width: 1.05rem; height: 1.16rem; left: 0.00rem; top: 1.04rem;">Next of kin (optional)</span>
            </p>
            
            <p class="paragraph body-text"
                style="width: 3.94rem; height: 6.36rem; font-size: 1.10rem; left: 12.34rem; top: 61.08rem; text-align: left; font-family: pro, serif;">
                <span class="position" style="width: 1.00rem; height: 1.01rem; left: -0.00rem; top: 1.13rem;">
                    
                   <span class="style" style="font-weight: 500; color: black; text-decoration: bold;">M. Nector</span>
            </p>
            <svg viewbox="0.000000, -0.129500, 163.600000, 1.000000" class="graphic"
                style="width: 16.36rem; height: 0.10rem; display: block; z-index: 10; left: 12.14rem; top: 63.37rem;">
                <path stroke-width="0.259000" fill="none" d="M 0 -2.84217e-14 L 163.595 -2.84217e-14" stroke="#a7a9ac"
                    stroke-opacity="1.000000" />
            </svg>
            <svg viewbox="0.000000, -0.129500, 163.600000, 1.000000" class="graphic"
                style="width: 16.36rem; height: 0.10rem; display: block; z-index: 10; left: 12.14rem; top: 68.63rem;">
                <path stroke-width="0.259000" fill="none" d="M 0 5.68434e-14 L 163.595 5.68434e-14" stroke="#a7a9ac"
                    stroke-opacity="1.000000" />
            </svg>
            <svg viewbox="0.000000, -0.129500, 163.600000, 1.000000" class="graphic"
                style="width: 16.36rem; height: 0.10rem; display: block; z-index: 10; left: 12.14rem; top: 66.03rem;">
                <path stroke-width="0.259000" fill="none" d="M 0 -2.84217e-14 L 163.595 -2.84217e-14" stroke="#a7a9ac"
                    stroke-opacity="1.000000" />
            </svg>
            <p class="paragraph body-text"
                style="width: 6.15rem; height: 1.01rem; font-size: 0.90rem; left: 12.34rem; top: 67.44rem; text-align: left; font-family: pro, serif; font-weight: 400;">
                <span class="style" style="font-weight: 500; color: black; text-decoration: bold;">Asaree Hingun</span>
            </p>
            <p class="paragraph body-text"
                style="width: 6.93rem; height: 0.23rem; font-size: 1.10rem; left: 31.05rem; top: 62.12rem; text-align: left; font-family: pro, serif; font-weight: 400;">
            </p>
            <p class="paragraph body-text"
                style="width: 5.97rem; height: 5.34rem; font-size: 0.90rem; left: 31.82rem; top: 62.34rem; text-align: left; color: #616262; font-weight: 300;">
                <span class="position style"
                    style="width: 1.86rem; height: 1.11rem; left: 0.00rem; top: 0.04rem;">Atoll, Island/City</span>
                <span class="position style" style="width: 2.90rem; height: 1.11rem; left: 0.00rem; top: 2.70rem;">Country</span>
            </p>
            <p class="paragraph body-text"
                style="width: 3.59rem; height: 6.28rem; font-size: 1.10rem; left: 39.09rem; top: 61.08rem; text-align: left; font-family: pro, serif;">
                <span class="position" style="width: 2.15rem; height: 1.01rem; left: 0.00rem; top: 0.99rem;">
                    <span class="style" style="font-weight: 500; color: black; text-decoration: bold;">K. Male’</span>
                </span>
                <span class="position style"
                    style="width: 3.55rem; height: 1.01rem; font-size: 0.90rem; left: 0.00rem; top: 3.66rem; font-family: pro, serif;">
                    Maldives</span>
            </p>
            <svg viewbox="0.000000, -0.130500, 170.200000, 1.000000" class="graphic"
                style="width: 17.02rem; height: 0.10rem; display: block; z-index: -10; left: 38.89rem; top: 66.03rem;">
                <path stroke-width="0.261000" fill="none" d="M 0 -2.84217e-14 L 170.152 -2.84217e-14" stroke="#a7a9ac"
                    stroke-opacity="1.000000" />
            </svg>
            <p class="paragraph body-text"
                style="width: 53.77rem; height: 1.34rem; left: 3.48rem; top: 70.91rem; text-align: left; color: #6d6e71; font-weight: 300;">
                <span class="position style"
                    style="width: 0.66rem; height: 1.04rem; left: 0.00rem; top: 0.31rem;">In case of my/our death/to ascertain my/our whereabouts, please inform the status of my/our account to:</span>
            </p>
            <p class="paragraph body-text"
                style="width: 53.77rem; height: 1.07rem; left: 3.48rem; top: 74.22rem; text-align: left; color: #6d6e71; font-weight: 300;">
                <span class="position style"
                    style="width: 2.06rem; height: 1.04rem; left: 0.00rem; top: 0.03rem;">Name</span>
                <span class="position" style="width: 16.55rem; height: 1.04rem; left: 8.51rem; top: 0.03rem;">
                    <span class="style"> </span>
                    <span class="style" style="text-decoration: underline;"> </span>
                </span>
                <span class="position style" style="width: 0.72rem; height: 1.04rem; left: 28.35rem; top: 0.03rem;">
                    ID Card No.</span>
                <span class="position style"
                    style="width: 0.19rem; height: 1.04rem; left: 32.01rem; top: 0.03rem;">.</span>
                <span class="position" style="width: 16.80rem; height: 1.04rem; left: 35.63rem; top: 0.03rem;">
                    <span class="style"> </span>
                    <span class="style" style="text-decoration: underline;"> </span>
                </span>
            </p>
            <p class="paragraph body-text"
                style="width: 53.77rem; height: 1.07rem; left: 3.48rem; top: 76.78rem; text-align: left; color: #6d6e71; font-weight: 300;">
                <span class="position style"
                    style="width: 0.46rem; height: 1.04rem; left: 0.00rem; top: 0.03rem;">Relationship</span>
                <span class="position" style="width: 16.48rem; height: 1.04rem; left: 8.54rem; top: 0.03rem;">
                    <span class="style"> </span>
                    <span class="style" style="text-decoration: underline;"> </span>
                    <span class="style"> </span>
                </span>
                <span class="position style"
                    style="width: 2.32rem; height: 1.04rem; left: 28.35rem; top: 0.03rem;">Mobile No.</span>
                
                <span class="position" style="width: 16.73rem; height: 1.04rem; left: 35.70rem; top: 0.03rem;">
                    <span class="style"> </span>
                    <span class="style" style="text-decoration: underline;"> </span>
                </span>
            </p>
        </div>
        <div class="page" style="background: #ffffff; width: 59.55rem; height: 84.20rem; z-index: 0;">
            <div class="group" style="width: 52.12rem; height: 2.12rem; display: block; left: 4.13rem; top: 4.43rem;">
                <div class="textbox"
                    style="background: #2a9a47; width: 45.91rem; height: 2.12rem; display: block; z-index: -10; left: 6.21rem; top: 0.00rem;">
                    <p class="paragraph"
                        style="width: 45.17rem; height: 1.69rem; z-index: -10; font-size: 1.00rem; left: 0.73rem; top: 0.00rem; text-align: left; font-family: 'pro', serif; color: #ffffff; font-weight: 600;">
                        <span class="position style"  style="width: 6.03rem; height: 1.29rem; left: 0.00rem; top: 0.43rem;">EMPLOYMENT DETAILS</span>
                    </p>
                </div>
                <div class="textbox"
                    style="background: #222e65; width: 6.21rem; height: 2.12rem; display: block; z-index: -10; left: 0.00rem; top: 0.00rem;">
                    <p class="paragraph"
                        style="width: 5.67rem; height: 1.69rem; z-index: -10; font-size: 1.00rem; left: 0.54rem; top: 0.00rem; text-align: left; font-family: 'pro', serif; color: #ffffff; font-weight: 600;">
                        <span class="position style"
                            style="width: 1.66rem; height: 1.29rem; left: 0.00rem; top: 0.43rem;">SECTION C</span>
                    </p>
                </div>
            </div>
            <p class="paragraph body-text"
                style="width: 6.99rem; height: 1.53rem; left: 4.05rem; top: 7.23rem; text-align: left; color: #616262; font-weight: 300;">
                <span class="position style"
                    style="width: 4.44rem; height: 1.04rem; left: 0.00rem; top: 0.50rem;">Employment Status</span>
            </p>
            <p class="paragraph body-text"
                style="width: 3.02rem; height: 1.53rem; font-size: 1.10rem; left: 16.44rem; top: 7.23rem; text-align: left; font-family: pro, serif; font-weight: 300;">
                <span class="position" style="width: 2.82rem; height: 1.04rem; left: -0.00rem; top: 0.50rem;">
                    <span class="style"></span>
                    <span class="style"
                        style="font-family: 'pro', serif; font-size: 0.85rem; color: #616262;">Salaried</span>
                </span>
            </p>
            <p class="paragraph body-text"
                style="width: 31.32rem; height: 1.51rem; font-size: 1.10rem; left: 25.93rem; top: 7.23rem; text-align: left; font-family: pro, serif; font-weight: 300;">
                <span class="position" style="width: 1.28rem; height: 1.04rem; left: 0.00rem; top: 0.50rem;">
                    <span class="style"></span>
                    <span class="style"
                        style="font-family: 'pro', serif; font-size: 0.85rem; color: #616262;">Self employed</span>
                </span>
                <span class="position style"
                    style="width: 4.45rem; height: 1.04rem; font-size: 0.85rem; left: 9.40rem; top: 0.50rem; font-family: 'pro', serif; color: #616262;">
                    Unemployed</span>
                <span class="position style"
                    style="width: 5.02rem; height: 1.04rem; font-size: 0.85rem; left: 18.80rem; top: 0.50rem; font-family: 'pro', serif; color: #616262;">
                    Student/Minor</span>
                <span class="position style"
                    style="width: 2.48rem; height: 1.04rem; font-size: 0.85rem; left: 27.59rem; top: 0.50rem; font-family: 'pro', serif; color: #616262;">
                    Retired</span>
            </p>
            <svg viewbox="0.000000, 0.000000, 16.600000, 16.600000" class="graphic"
                style="width: 1.66rem; height: 1.66rem; display: block; z-index: 10; left: 23.67rem; top: 7.47rem;">
                <path stroke-width="0.250000" fill="none" d="M 0 0 L 16.567 0 L 16.567 16.567 L 0 16.567 L 0 0 Z"
                    stroke="#808285" stroke-opacity="1.000000" />
            </svg>
            <svg viewbox="0.000000, 0.000000, 16.600000, 16.600000" class="graphic"
                style="width: 1.66rem; height: 1.66rem; display: block; z-index: -10; left: 33.07rem; top: 7.47rem;">
                <path stroke-width="0.250000" fill="none" d="M 0 0 L 16.567 0 L 16.567 16.567 L 0 16.567 L 0 0 Z"
                    stroke="#808285" stroke-opacity="1.000000" />
            </svg>
            <svg viewbox="0.000000, 0.000000, 16.600000, 16.600000" class="graphic"
                style="width: 1.66rem; height: 1.66rem; display: block; z-index: -10; left: 42.46rem; top: 7.47rem;">
                <path stroke-width="0.250000" fill="none" d="M 0 0 L 16.567 0 L 16.567 16.567 L 0 16.567 L 0 0 Z"
                    stroke="#808285" stroke-opacity="1.000000" />
            </svg>
            <svg viewbox="0.000000, 0.000000, 16.600000, 16.600000" class="graphic"
                style="width: 1.66rem; height: 1.66rem; display: block; z-index: -10; left: 51.36rem; top: 7.47rem;">
                <path stroke-width="0.250000" fill="none" d="M 0 0 L 16.567 0 L 16.567 16.567 L 0 16.567 L 0 0 Z"
                    stroke="#808285" stroke-opacity="1.000000" />
            </svg>
            <div class="group" style="width: 1.68rem; height: 1.68rem; display: block; left: 14.16rem; top: 10.34rem;">
                <svg viewbox="0.000000, 0.000000, 16.600000, 16.600000" class="graphic"
                    style="width: 1.66rem; height: 1.66rem; display: block; z-index: -10; left: 0.01rem; top: 0.01rem;">
                    <path stroke-width="0.250000" fill="none" d="M 0 0 L 16.567 0 L 16.567 16.567 L 0 16.567 L 0 0 Z"
                        stroke="#808285" stroke-opacity="1.000000" />
                </svg>
                <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACYAAAAmCAYAAACoPemuAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAAuklEQVRYhe3YsQnDMBCF4SdjCHgCN9YKmcDILl2m8zYeKQukEWQBgccQuHEVF0rlENS9Si7eXx1XfXDdmWVZEi6Wc85XpRF5fd97Y4yrz0XTNLFt21ASZa39AJgAoP5bvud5fhRTAVjX9RljBABc7pRngrEJxiYYm2BsgrEJxiYYm2BsgrEJxiYYm2BsgrEJxiYY2+8/1nXdLYTwKonZ9/1+zjUAjOPoj+OYtm0rp8qqhmHwKSVXGpL3BbxeJQ5tPdoFAAAAAElFTkSuQmCC"
                    class="image"
                    style="width: 1.42rem; height: 1.39rem; display: block; z-index: -10; left: 0.13rem; top: 0.14rem;" />
            </div>
            <div class="group" style="width: 1.68rem; height: 1.68rem; display: block; left: 23.66rem; top: 10.34rem;">
                <svg viewbox="0.000000, 0.000000, 16.600000, 16.600000" class="graphic"
                    style="width: 1.66rem; height: 1.66rem; display: block; z-index: -10; left: 0.01rem; top: 0.01rem;">
                    <path stroke-width="0.250000" fill="none" d="M 0 0 L 16.567 0 L 16.567 16.567 L 0 16.567 L 0 0 Z"
                        stroke="#808285" stroke-opacity="1.000000" />
                </svg>
                <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACUAAAAlCAYAAADFniADAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAAtklEQVRYhe3YIQ6DQBCF4bcEUcMBNtlwjcrZCyDpdbhPRe+woqIerlADCk1oFQTcc4t4v5qM+pJx47qu++FCmVkqciOOmVlyzlm5Laqq+oYQPrlA3vtyXdcGAHZUXdfvtm0fuVDDMDzHcQQAXOp8W0KxCcUmFJtQbEKxCcUmFJtQbEKxCcUmFJtQbEKxCcV2SdT+CvLe3/q+f+WCzPN83+YSAGKMaVmWZpqmXKZTRYwxAbDckGN/Uz4j+qCws/MAAAAASUVORK5CYII="
                    class="image"
                    style="width: 1.39rem; height: 1.38rem; display: block; z-index: -10; left: 0.15rem; top: 0.15rem;" />
                <div class="textbox"
                    style="width: 1.68rem; height: 1.68rem; display: block; z-index: -10; left: 0.00rem; top: -0.00rem;">
                    <p class="paragraph body-text"
                        style="width: 1.22rem; height: 1.40rem; z-index: -10; font-size: 0.90rem; left: 0.46rem; top: 0.00rem; text-align: left; font-family: 'pro', serif; font-weight: 400;">
                        <span class="position style"
                            style="width: 0.90rem; height: 0.90rem; left: 0.00rem; top: 0.38rem;">✔</span>
                    </p>
                </div>
            </div>
            <div class="group" style="width: 1.68rem; height: 1.68rem; display: block; left: 33.05rem; top: 10.34rem;">
                <svg viewbox="0.000000, 0.000000, 16.600000, 16.600000" class="graphic"
                    style="width: 1.66rem; height: 1.66rem; display: block; z-index: -10; left: 0.01rem; top: 0.01rem;">
                    <path stroke-width="0.250000" fill="none" d="M 0 0 L 16.567 0 L 16.567 16.567 L 0 16.567 L 0 0 Z"
                        stroke="#808285" stroke-opacity="1.000000" />
                </svg>
                <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACYAAAAmCAYAAACoPemuAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAAqklEQVRYhe3YoQ3EIBjF8UdTxwgwR+3hm1uAZRiqE9TWssUlGAbgTlyoqHsKxPsrgvoln3smpfTFZIUQzmU04lkI4QTwWvuHtfbjnLsGmuC9N621HQBumHPuijG+x7GAnPNRSgEATHfKnmBsgrEJxiYYm2BsgrEJxiYYm2BsgrEJxiYYm2BsgrEJxnbvY957k3M+RmJqrVt/r8B/Xmyt7X00m6Glb56jIc9+2dgkFLDpkDAAAAAASUVORK5CYII="
                    class="image"
                    style="width: 1.41rem; height: 1.41rem; display: block; z-index: -10; left: 0.14rem; top: 0.14rem;" />
            </div>
            <div class="group" style="width: 1.68rem; height: 1.68rem; display: block; left: 42.45rem; top: 10.34rem;">
                <svg viewbox="0.000000, 0.000000, 16.600000, 16.600000" class="graphic"
                    style="width: 1.66rem; height: 1.66rem; display: block; z-index: -10; left: 0.01rem; top: 0.01rem;">
                    <path stroke-width="0.250000" fill="none" d="M 0 0 L 16.567 0 L 16.567 16.567 L 0 16.567 L 0 0 Z"
                        stroke="#808285" stroke-opacity="1.000000" />
                </svg>
                <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACsAAAArCAYAAADhXXHAAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAAuUlEQVRYhe3ZIRKDMBSE4S0guUFyj8q+AyAqc5tciSsg8TlEBQqfaUUnKARmJ2RmP5WJ+sVz+4gxftEAM1u62hFXmNkC4DWUj3EcP977tWLTKedcn3OeAOCI9d6vIYR3vaxzKaV52zYAQBNnUCiWRbEsimVRLItiWRTLolgWxbIolkWxLIplUSyLYlkUy6JYFsWyKJalqdhjAHHO9SmluWbMmX3fn+U9AP+dKec8lVXkrroyiNUOueIH/nIkFXZVOjYAAAAASUVORK5CYII="
                    class="image"
                    style="width: 1.60rem; height: 1.60rem; display: block; z-index: -10; left: 0.01rem; top: 0.05rem;" />
            </div>
            <div class="group" style="width: 1.68rem; height: 1.68rem; display: block; left: 51.35rem; top: 10.34rem;">
                <svg viewbox="0.000000, 0.000000, 16.600000, 16.600000" class="graphic"
                    style="width: 1.66rem; height: 1.66rem; display: block; z-index: -10; left: 0.01rem; top: 0.01rem;">
                    <path stroke-width="0.250000" fill="none" d="M 0 0 L 16.567 0 L 16.567 16.567 L 0 16.567 L 0 0 Z"
                        stroke="#808285" stroke-opacity="1.000000" />
                </svg>
                <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACUAAAAkCAYAAAAOwvOmAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAAt0lEQVRYhe3YIQ6DQBCF4bcEU9MEAYqEc1TNHgFu0JtwlZoeZGt6A4KrJUgsIa1aQt1zi3i/moz6knHj+r7/4kR570OWGnHMex8AWB4XRVF8mqZ5pQKVZXld17UDgB1V1/W7bdt7KtQwDM95ngEApzpfTCg2odiEYhOKTSg2odiEYhOKTSg2odiEYhOKTSg2odj2r0tVVZdxHB+pIMuy3OKcA4CZhW3bummaUpn+yswsOOcsNeTYD7fZI92r0TOfAAAAAElFTkSuQmCC"
                    class="image"
                    style="width: 1.40rem; height: 1.37rem; display: block; z-index: -10; left: 0.14rem; top: 0.15rem;" />
            </div>
            <p class="paragraph body-text"
                style="width: 12.62rem; height: 0.73rem; font-size: 0.60rem; left: 44.63rem; top: 8.74rem; text-align: left; color: #6d6e71; font-weight: 300;">
                <span class="position style"
                    style="width: 0.98rem; height: 0.74rem; left: 0.00rem; top: -0.00rem;">(*fill other income details)</span>
            </p>
            <svg viewbox="0.000000, 0.000000, 16.600000, 16.600000" class="graphic"
                style="width: 1.66rem; height: 1.66rem; display: block; z-index: 10; left: 14.17rem; top: 7.47rem;">
                <path stroke-width="0.250000" fill="none" d="M 0 0 L 16.567 0 L 16.567 16.567 L 0 16.567 L 0 0 Z"
                    stroke="#808285" stroke-opacity="1.000000" />
            </svg>
            <p class="paragraph body-text"
                style="width: 7.01rem; height: 1.54rem; left: 4.05rem; top: 10.10rem; text-align: left; color: #616262; font-weight: 300;">
                <span class="position style"
                    style="width: 4.44rem; height: 1.04rem; left: 0.00rem; top: 0.50rem;">Employment Sector</span>
            </p>
            <p class="paragraph body-text"
                style="width: 3.75rem; height: 1.54rem; font-size: 1.10rem; left: 16.44rem; top: 10.10rem; text-align: left; font-family: pro, serif; font-weight: 300;">
                <span class="position" style="width: 3.54rem; height: 1.04rem; left: 0.00rem; top: 0.50rem;">
                    <span class="style"></span>
                    <span class="style"
                        style="font-family: 'pro', serif; font-size: 0.85rem; color: #616262;">Civil/State</span>
                </span>
            </p>
            <p class="paragraph body-text"
                style="width: 31.32rem; height: 1.54rem; font-size: 1.10rem; left: 25.93rem; top: 10.10rem; text-align: left; font-family: pro, serif; font-weight: 300;">
                <span class="position" style="width: 2.39rem; height: 1.04rem; left: 0.00rem; top: 0.50rem;">
                    <span class="style"></span>
                    <span class="style"
                        style="font-family: 'pro', serif; font-size: 0.85rem; color: #616262;">Private</span>
                </span>
                <span class="position style"  style="width: 2.15rem; height: 1.04rem; font-size: 0.85rem; left: 9.40rem; top: 0.50rem; font-family: 'pro', serif; color: #616262;"> Public</span>
                <span class="position style" style="width: 4.93rem; height: 1.04rem; font-size: 0.85rem; left: 18.80rem; top: 0.50rem; font-family: 'pro', serif; color: #616262;"> Military/Police</span>
                <span class="position style" style="width: 2.78rem; height: 1.04rem; font-size: 0.85rem; left: 27.59rem; top: 0.50rem; font-family: 'pro', serif; color: #616262;">  Political</span>
            </p>
            <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACwAAAAtCAYAAADV2ImkAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAA7EAAAOxAGVKw4bAAABXklEQVRYhe3YsUrDQBjA8S/XEPDSDkkjdHSW0EnEocQHkELwIUR8ChF8hVI65Qk6Otuts0Mf4IZqOMUsTTCDcWmhaZJSkS9n4PuP35fhxxFuOO3+4TGDBqUDANzd3pxyfvShGrOvOE66o/FkoQMAmCaXnPN31ah9aZr2DbA+4U1pmprL5fJMDamY4zif7Xb7ZXuWA0dRdBIEwXOtqop8358JIc49z+Pbc73s416v9+q67rweWjHLsnQp5ZAxluzuSsGu684Hg8E1Pq08IcRUSlm6YzVb/hyBsSMwdgTGjsDYERg7AmNHYOwIjB2BsSMwdgTGjsDYERg7AmPXOHDpc6tlWboQYlo3ZlMYhhdVuwLY9/2ZlHJY9T6rutwv0el04iiKLlVhDil3wq1WK7Ft+0kVZjfG2NfuLAc2DOOt3+9f1Uf6fc28JVar+DjLsn+Nj+OkC7AGj8aThVrO4f0AqCtSf8gywgAAAAAASUVORK5CYII="
                class="image"
                style="width: 1.68rem; height: 1.68rem; display: block; z-index: 0; left: 14.16rem; top: 13.25rem;" />
            <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAC0AAAAtCAYAAAA6GuKaAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAA7EAAAOxAGVKw4bAAABWElEQVRYhe3ZMUvDQBTA8Zf0DOZCgotbxmx1CR0c/QZJvoEgCLr5EUTwM6TQSdycOzmZ0akOnR0MRC63FVok9KiDBJraaIv4TuH9t/fI8OO4KWdcXl0v4J/FAADOz04PHM6lbsx3TWez/bQ/GDMAAIdz6ThOqRu1aWx5UEpZo9HoRBemrTAMbzudzrSeG+j5fL47HA5TfFZ7SZJkVVXd27b9XO9Y28e+77+EYfiAImuJc+4JIZLVfSu62+0+9nq9499lfV2e53dCiE97U4PlxxEaK0JjRWisCI0VobEiNFaExorQWBEaK0JjRWisCI0VobEiNFaExqr1/7TneTtFUdxgYlYry/Jw3X4tOoqiTEqZSPk3H7wa14MxpuI4ziaTyZEu0CY1Tto0zbcgCC6UUnu6QOuyLOt1eW6gDcNQrus+4ZK2jwF8vITqhmxS7WQAAGl/MNbL2a53G3NTOXdqkv8AAAAASUVORK5CYII="
                class="image"
                style="width: 1.68rem; height: 1.68rem; display: block; z-index: 0; left: 23.66rem; top: 13.25rem;" />
            <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAC0AAAAtCAYAAAA6GuKaAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAA7EAAAOxAGVKw4bAAABVklEQVRYhe3ZMUuFUBTA8VNcQbwOEQTh5OAkDY4NQR8gUHmD0Ad4UFtjYwR9hvfAua8RCA6NBW9yaBAHUYgwe5u+hnigDy1f0LkJ57+dM/2Qexfvzs3t3QpGFgMAuLyYHnFFyUVjfupjuTyYzf0FAwDgipJzzjPRqKHtigb8JtYc4jg+CcPwWhSmL8/zzhlj7+u5hS7L8jCKojN8Vn+u6wZ1XXMA6EY303X9xbKsBxRZT7Is76dpOtnc96JN03y0LGv6t6zvS5Lkvms/yotIaKwIjRWhsSI0VoTGitBYERorQmNFaKwIjRWhsSI0VoTGitBYjRLd+6tXVVU5TVMfE7NZnufHXftOtG3bQZZlkyz7n29HreOhaVruOE5QFMWpKNCQWl+ac/5sGMZVVVV7okBdMcZeW3NzkCTpTZKkJ1zS9jGAr5dQ0ZAhrZ0MAGA29xdiOdv1CaxET/O9tye5AAAAAElFTkSuQmCC"
                class="image"
                style="width: 1.68rem; height: 1.68rem; display: block; z-index: 0; left: 33.05rem; top: 13.25rem;" />
            <p class="paragraph body-text"
                style="width: 43.10rem; height: 2.53rem; font-size: 1.10rem; left: 14.16rem; top: 12.40rem; text-align: left; font-family: pro, serif; font-weight: 300;">
                <span class="position" style="width: 3.33rem; height: 1.04rem; left: 2.29rem; top: 1.12rem;">
                    <span class="style"></span>
                    <span class="style" style="font-family: 'pro', serif; font-size: 1.00rem;"> </span>
                    <span class="style"
                        style="font-family: 'pro', serif; font-size: 0.85rem; color: #616262;">Volunteer</span>
                </span>
                <span class="position" style="width: 3.13rem; height: 1.04rem; left: 11.77rem; top: 1.12rem;">
                    <span class="style" style="font-family: 'pro', serif; font-size: 0.85rem; color: #616262;">
                    </span>
                    <span class="style"
                        style="font-family: 'pro', serif; font-size: 0.85rem; color: #616262; font-weight: 400;">
                    </span>
                    <span class="style"
                        style="font-family: 'pro', serif; font-size: 0.85rem; color: #616262;">Judiciary</span>
                </span>
                <span class="position" style="width: 4.52rem; height: 1.04rem; left: 21.17rem; top: 1.12rem;">
                    <span class="style" style="font-family: 'pro', serif; font-size: 0.85rem; color: #616262;">
                    </span>
                    <span class="style"
                        style="font-family: 'pro', serif; font-size: 0.85rem; color: #616262; font-weight: 400;">
                    </span>
                    <span class="style"
                        style="font-family: 'pro', serif; font-size: 0.85rem; color: #616262;">Other,specify</span>
                </span>
                <span class="position" style="width: 14.77rem; height: 1.04rem; left: 27.24rem; top: 1.12rem;">
                    <span class="style" style="font-family: 'pro', serif; font-size: 0.85rem; color: #616262;">
                    </span>
                    <span class="style"
                        style="font-family: 'pro', serif; font-size: 0.85rem; color: #616262; text-decoration: underline;">
                    </span>
                </span>
            </p>
            <div class="group" style="width: 52.16rem; height: 0.05rem; display: block; left: 4.06rem; top: 15.90rem;">
                <svg viewbox="0.000000, -0.250000, 521.600000, 1.000000" class="graphic"
                    style="width: 52.16rem; height: 0.10rem; display: block; z-index: -10; left: 0.00rem; top: -0.00rem;">
                    <path stroke-width="0.500000" fill="none" d="M 521.56 0 L 0 0" stroke="#939598"
                        stroke-opacity="1.000000" />
                </svg>
            </div>
            <p class="paragraph body-text"
                style="width: 53.20rem; height: 1.13rem; font-size: 0.90rem; left: 4.05rem; top: 17.70rem; text-align: left; color: #6d6e71; font-weight: 300;">
                <span class="position style"
                    style="width: 3.49rem; height: 1.11rem; left: 0.00rem; top: 0.04rem;">Employer Name</span>
                
                <span class="position style" style="width: 21.87rem; height: 1.11rem; left: 6.98rem; top: 0.04rem;">
                </span>
                <span class="position" style="width: 2.51rem; height: 1.11rem; left: 6.50rem; top: -0.090rem;">
                    <span class="style" style="color: black;text-decoration: bold;"><?= filter_var($record['company'], FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?> </span>
                   
                </span>
                <span class="position style" style="width: 1.70rem; height: 1.11rem; left: 31.00rem; top: 0.04rem;">
                    Joined Date</span>
                <span class="position" style="width: 15.87rem; height: 1.11rem; left: 36.00rem; top: 0.04rem;">
                   
                    <span class="style" style="color: black;text-decoration: bold;"><?= date('d-M-Y', strtotime($record['xpat_join_date'])) ?></span>
                </span>
            </p>
            <p class="paragraph body-text"
                style="width: 29.05rem; height: 1.81rem; font-size: 0.90rem; left: 4.05rem; top: 18.83rem; text-align: left; color: #6d6e71; font-weight: 300;">
                <span class="position style"
                    style="width: 4.57rem; height: 1.11rem; left: 0.00rem; top: 0.74rem;">Occupation/<br>Designation</span>
            </p>
            <p class="paragraph body-text"
                style="width: 29.05rem; height: 1.11rem; font-size: 0.90rem; left: 4.05rem; top: 20.64rem; text-align: left; color: #6d6e71; font-weight: 300;">
                
                <span class="position" style="width: 22.77rem; height: 1.11rem; left: 6.08rem; top: 0.01rem;">
                    <span class="style"> </span>
                    <span class="style" style="color: black;text-decoration: bold;"><?= filter_var($record['xpat_designation'], FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?></span>
                </span>
            </p>
            <p class="paragraph body-text"
                style="width: 29.05rem; height: 1.13rem; font-size: 0.90rem; left: 4.05rem; top: 23.71rem; text-align: left; font-family: 'pro', serif; color: #808285; font-weight: 600;">
                <span class="position style"
                    style="width: 2.99rem; height: 1.16rem; left: 0.00rem; top: -0.00rem;">Present</span>
                <span class="position style" style="width: 3.09rem; height: 1.16rem; left: 3.17rem; top: -0.00rem;">
                    Address</span>
                <span class="position style" style="width: 0.78rem; height: 1.16rem; left: 6.44rem; top: -0.00rem;">
                    of</span>
                <span class="position style" style="width: 3.73rem; height: 1.16rem; left: 7.39rem; top: -0.00rem;">
                    Employer</span>
            </p>
            <p class="paragraph body-text"
                style="width: 23.54rem; height: 0.23rem; font-size: 1.10rem; left: 33.70rem; top: 19.96rem; text-align: left; font-family: pro, serif; font-weight: 600;">
            </p>
            <p class="paragraph body-text"
                style="width: 22.19rem; height: 1.13rem; font-size: 0.90rem; left: 35.06rem; top: 20.19rem; text-align: left; color: #6d6e71; font-weight: 300;">
                <span class="position style"  style="width: 2.24rem; height: 1.11rem; left: 0.00rem; top: 0.04rem;">Salary Amount</span>
                <span class="position" style="width: 14.67rem; height: 1.11rem; left: 6.38rem; top: 0.04rem;">
                    <span class="style"> </span>
                    <span class="style" style="font-weight: 500; color: black; text-decoration: bold; "><?= filter_var($record['basic_salary'], FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?> </span>
                    
                </span>
            </p>
            <p class="paragraph body-text"
                style="width: 8.24rem; height: 1.94rem; font-size: 0.90rem; left: 4.02rem; top: 25.46rem; text-align: left; color: #6d6e71; font-weight: 300;">
                <span class="position style"
                    style="width: 5.68rem; height: 1.11rem; left: 0.00rem; top: 0.84rem;">House/Building Name</span>
                
            </p>
            <p class="paragraph body-text"
                style="width: 4.10rem; height: 1.71rem; font-size: 1.10rem; left: 14.19rem; top: 25.46rem; text-align: left; font-family: pro, serif;">
                <span class="position" style="width: 1.00rem; height: 1.01rem; left: 0.00rem; top: 0.70rem;">
                   
                    <span class="style" style="font-weight: 500; color: black; text-decoration: bold;font-family: pro, serif; font-size: 0.90rem;">M. Nector</span>
                </span>
            </p>
            <p class="paragraph body-text"
                style="width: 4.55rem; height: 1.94rem; font-size: 1.10rem; left: 35.06rem; top: 25.46rem; text-align: left; font-family: pro, serif; font-weight: 300;">
                <span class="position" style="width: 2.19rem; height: 1.11rem; left: 0.00rem; top: 0.84rem;">
                    <span class="style"></span>
                    <span class="style"
                        style="font-family: 'pro', serif; font-size: 0.90rem; color: #6d6e71;">Street Name</span>
                        <span class="style" style="font-weight: 500; color: black; text-decoration: bold; font-family: pro, serif; font-size: 0.90rem;">  Asaree Hingun</span>
                </span>
                
            </p>
            <p class="paragraph body-text"
                style="width: 16.91rem; height: 1.50rem; font-size: 1.10rem; left: 40.34rem; top: 25.46rem; text-align: left; font-family: pro, serif;">
                <span class="position" style="width: 2.85rem; height: 1.01rem; left: 0.00rem; top: 0.50rem;">
                </span>
            </p>
            <div class="group" style="width: 15.97rem; height: 0.03rem; display: block; left: 40.14rem; top: 27.19rem;">
                <svg viewbox="0.000000, -0.125000, 159.750000, 1.000000" class="graphic"
                    style="width: 15.97rem; height: 0.10rem; display: block; z-index: -10; left: 0.00rem; top: -0.00rem;">
                    <path stroke-width="0.250000" fill="none" d="M 159.735 0 L 0 0" stroke="#a7a9ac"
                        stroke-opacity="1.000000" />
                </svg>
            </div>
            <div class="group" style="width: 18.92rem; height: 0.03rem; display: block; left: 13.99rem; top: 27.39rem;">
                <svg viewbox="0.000000, -0.137500, 189.200000, 1.000000" class="graphic"
                    style="width: 18.92rem; height: 0.10rem; display: block; z-index: -10; left: 0.00rem; top: -0.00rem;">
                    <path stroke-width="0.275000" fill="none" d="M 189.163 0 L 0 0" stroke="#a7a9ac"
                        stroke-opacity="1.000000" />
                </svg>
            </div>
            <p class="paragraph body-text"
                style="width: 5.02rem; height: 1.92rem; font-size: 0.90rem; left: 4.14rem; top: 28.09rem; text-align: left; color: #6d6e71; font-weight: 300;">
                <span class="position style"
                    style="width: 1.37rem; height: 1.11rem; left: 0.00rem; top: 0.83rem;">Flat No/Floor</span>
                
            </p>
            <p class="paragraph body-text"
                style="width: 3.40rem; height: 1.69rem; font-size: 1.10rem; left: 10.96rem; top: 28.09rem; text-align: left; font-family: pro, serif;">
                <span class="position" style="width: 1.20rem; height: 1.01rem; left: 0.00rem; top: 0.69rem;">
                    <span class="style"></span>
                    <span class="style" style="font-family: pro, serif; font-size: 0.90rem;">1st</span>
                </span>
                
            </p>
            <p class="paragraph body-text"
                style="width: 22.19rem; height: 1.88rem; font-size: 1.10rem; left: 35.05rem; top: 28.13rem; text-align: left; font-family: pro, serif; font-weight: 300;">
                <span class="position" style="width: 1.86rem; height: 1.11rem; left: 0.01rem; top: 0.78rem;">
                    <span class="style"></span>
                    <span class="style"
                        style="font-family: 'pro', serif; font-size: 0.90rem; color: #616262;">Atoll, Island/ City</span>
                </span>
                <span class="position" style="width: 2.15rem; height: 1.01rem; left: 7.58rem; top: 0.80rem;">
                    <span class="style" style="font-family: 'pro', serif; font-size: 0.90rem; color: #616262;">
                    </span>
                    <span class="style"
                        style="font-family: pro, serif; font-size: 0.90rem; font-weight: 400;">K. Male’</span>
                </span>
            </p>
            <div class="group" style="width: 22.14rem; height: 0.03rem; display: block; left: 10.77rem; top: 30.00rem;">
                <svg viewbox="0.000000, -0.149000, 221.400000, 1.000000" class="graphic"
                    style="width: 22.14rem; height: 0.10rem; display: block; z-index: -10; left: 0.00rem; top: -0.00rem;">
                    <path stroke-width="0.298000" fill="none" d="M 221.366 0 L 0 0" stroke="#a7a9ac"
                        stroke-opacity="1.000000" />
                </svg>
            </div>
            <div class="group" style="width: 52.24rem; height: 0.05rem; display: block; left: 3.96rem; top: 32.14rem;">
                <svg viewbox="0.000000, -0.250000, 522.400000, 1.000000" class="graphic"
                    style="width: 52.24rem; height: 0.10rem; display: block; z-index: -10; left: 0.00rem; top: -0.00rem;">
                    <path stroke-width="0.500000" fill="none" d="M 522.358 0 L 0 0" stroke="#939598"
                        stroke-opacity="1.000000" />
                </svg>
            </div>
            <svg viewbox="0.000000, -0.125000, 146.750000, 1.000000" class="graphic"
                style="width: 14.68rem; height: 0.10rem; display: block; z-index: 10; left: 41.44rem; top: 29.82rem;">
                <path stroke-width="0.250000" fill="none" d="M 146.747 0 L 0 0" stroke="#a7a9ac"
                    stroke-opacity="1.000000" />
            </svg>
            <p class="paragraph body-text"
                style="width: 53.17rem; height: 1.64rem; left: 4.08rem; top: 32.27rem; text-align: left; font-family: 'pro', serif; color: #6d6e71; font-weight: 600;">
                <span class="position style"
                    style="width: 2.10rem; height: 1.10rem; left: 0.00rem; top: 0.57rem;">Other Income Details</span>
            </p>
            <svg viewbox="0.000000, 0.000000, 16.600000, 16.600000" class="graphic"
                style="width: 1.66rem; height: 1.66rem; display: block; z-index: 10; left: 4.09rem; top: 34.83rem;">
                <path stroke-width="0.250000" fill="none" d="M 0 0 L 16.567 0 L 16.567 16.567 L 0 16.567 L 0 0 Z"
                    stroke="#808285" stroke-opacity="1.000000" />
            </svg>
            <svg viewbox="0.000000, 0.000000, 16.600000, 16.600000" class="graphic"
                style="width: 1.66rem; height: 1.66rem; display: block; z-index: 10; left: 4.09rem; top: 40.15rem;">
                <path stroke-width="0.250000" fill="none" d="M 0 0 L 16.567 0 L 16.567 16.567 L 0 16.567 L 0 0 Z"
                    stroke="#808285" stroke-opacity="1.000000" />
            </svg>
            <svg viewbox="0.000000, 0.000000, 16.600000, 16.600000" class="graphic"
                style="width: 1.66rem; height: 1.66rem; display: block; z-index: 10; left: 4.09rem; top: 42.80rem;">
                <path stroke-width="0.250000" fill="none" d="M 0 0 L 16.567 0 L 16.567 16.567 L 0 16.567 L 0 0 Z"
                    stroke="#808285" stroke-opacity="1.000000" />
            </svg>
            <svg viewbox="0.000000, 0.000000, 16.600000, 16.600000" class="graphic"
                style="width: 1.66rem; height: 1.66rem; display: block; z-index: 10; left: 4.09rem; top: 37.52rem;">
                <path stroke-width="0.250000" fill="none" d="M 0 0 L 16.567 0 L 16.567 16.567 L 0 16.567 L 0 0 Z"
                    stroke="#808285" stroke-opacity="1.000000" />
            </svg>
            <p class="paragraph body-text"  style="width: 50.20rem; height: 10.60rem; left: 6.14rem; top: 35.06rem; text-align: center; color: #6d6e71; font-weight: 300;">
                <span class="position style"  style="width: 2.28rem; height: 1.04rem; left: 0.00rem; top: 0.03rem;">Family Remittance, Please Specify
                    <span class="style" style="font-size: 0.60rem;">(remitter details, employment details and amount) </span>
                </span>
                
                <span class="position" style="width: 1.78rem; height: 1.04rem; left: 0.00rem; top: 2.69rem;">
                    <span class="style">Rent, Please Specify
                    <span class="style" style="font-size: 0.60rem;">(address and rent amount)</span>
                </span>
                 </span>
                <span class="position" style="width: 2.96rem; height: 1.04rem; left: 0.00rem; top: 5.34rem;">
                    <span class="style" style="font-size: 0.60rem;"> </span>
                    <span class="style">Pension, Please Specify /amount</span>
                </span>
                <span class="position" style="width: 37.77rem; height: 1.04rem; left: 12.39rem; top: 5.34rem;">
                    <span class="style"> </span>
                    <span class="style" style="text-decoration: underline;"> </span>
                </span>
                <span class="position" style="width: 2.13rem; height: 1.04rem; left: 0.00rem; top: 8.00rem;">
                    <span class="style" style="text-decoration: underline;"> </span>
                    <span class="style"> Other, Please Specify <span class="style" style="font-size: 0.60rem;">(details and amount )</span></span>
                </span>
                <span class="position" style="width: 34.35rem; height: 0.74rem; left: 15.77rem; top: 8.23rem;">
                    <span class="style" style="font-size: 0.60rem;"> </span>
                    <span class="style" style="font-size: 0.60rem; text-decoration: underline;"> </span>
                </span>
            </p>
            <div class="group" style="width: 52.05rem; height: 0.05rem; display: block; left: 4.01rem; top: 46.67rem;">
                <svg viewbox="0.000000, -0.250000, 520.500000, 1.000000" class="graphic"
                    style="width: 52.05rem; height: 0.10rem; display: block; z-index: -10; left: 0.00rem; top: 0.00rem;">
                    <path stroke-width="0.500000" fill="none" d="M 520.453 0 L 0 0" stroke="#939598"
                        stroke-opacity="1.000000" />
                </svg>
            </div>
            <p class="paragraph body-text" style="width: 53.10rem; height: 1.57rem; left: 4.15rem; top: 46.80rem; text-align: left; font-family: 'pro', serif; color: #808285; font-weight: 600;">
                <span class="position style"
                    style="width: 4.01rem; height: 1.10rem; left: 0.00rem; top: 0.50rem;">Businesses Involved (List all the businesses involved and designation)
                </span>
            </p>
            
            <p class="paragraph body-text"
                style="width: 52.25rem; height: 5.37rem; left: 4.05rem; top: 49.16rem; text-align: left; color: #6d6e71; font-weight: 300;">
                <span class="position style"
                    style="width: 2.06rem; height: 1.04rem; left: 0.00rem; top: 0.41rem;">Name of business</span>
                <span class="position style" style="width: 13.01rem; height: 1.04rem; left: 6.71rem; top: 0.41rem;">
                </span>
                <span class="position" style="width: 2.75rem; height: 1.04rem; left: 20.71rem; top: 0.41rem;">
                    <span class="style" style="text-decoration: underline;"> </span>
                    <span class="style" style="transform: ScaleX(1.50);"> </span>
                    <span class="style">Designation</span>
                </span>
                <span class="position style" style="width: 11.79rem; height: 1.04rem; left: 25.67rem; top: 0.41rem;">
                </span>
                <span class="position" style="width: 1.04rem; height: 1.04rem; left: 38.40rem; top: 0.49rem;">
                    <span class="style" style="text-decoration: underline;"> </span>
                    <span class="style" style="transform: ScaleX(1.50);"> </span>
                    <span class="style">Income (MVR)</span>
                </span>
                
                <span class="position" style="width: 8.80rem; height: 1.04rem; left: 43.41rem; top: 0.49rem;">
                    <span class="style"> </span>
                    <span class="style" style="text-decoration: underline;"> </span>
                </span>
                <span class="position style" style="width: 2.06rem; height: 1.04rem; left: 0.00rem; top: 2.95rem;">
                    Name of business</span>
                <span class="position" style="width: 13.01rem; height: 1.04rem; left: 6.71rem; top: 2.95rem;">
                    <span class="style"> </span>
                    <span class="style" style="text-decoration: underline;"> </span>
                    <span class="style" style="transform: ScaleX(1.50);"> </span>
                </span>
                <span class="position style"
                    style="width: 2.75rem; height: 1.04rem; left: 20.71rem; top: 2.95rem;">Designation</span>
                <span class="position" style="width: 11.79rem; height: 1.04rem; left: 25.67rem; top: 2.95rem;">
                    <span class="style"> </span>
                    <span class="style" style="text-decoration: underline;"> </span>
                    <span class="style" style="transform: ScaleX(1.50);"> </span>
                </span>
                <span class="position style"
                    style="width: 1.04rem; height: 1.04rem; left: 38.40rem; top: 2.95rem;">Income (MVR)</span>
                <span class="position" style="width: 8.80rem; height: 1.04rem; left: 43.41rem; top: 2.95rem;">
                    <span class="style"> </span>
                    <span class="style" style="text-decoration: underline;"> </span>
                </span>
            </p>
            <svg viewbox="0.000000, 0.000000, 16.600000, 16.600000" class="graphic"
                style="width: 1.66rem; height: 1.66rem; display: block; z-index: 10; left: 20.95rem; top: 57.81rem;">
                <path stroke-width="0.250000" fill="none"
                    d="M 0 5.68434e-14 L 16.567 5.68434e-14 L 16.567 16.567 L 0 16.567 L 0 5.68434e-14 Z"
                    stroke="#808285" stroke-opacity="1.000000" />
            </svg>
            <svg viewbox="0.000000, 0.000000, 16.600000, 16.600000" class="graphic"
                style="width: 1.66rem; height: 1.66rem; display: block; z-index: -10; left: 29.06rem; top: 57.81rem;">
                <path stroke-width="0.250000" fill="none"
                    d="M 0 5.68434e-14 L 16.567 5.68434e-14 L 16.567 16.567 L 0 16.567 L 0 5.68434e-14 Z"
                    stroke="#808285" stroke-opacity="1.000000" />
            </svg>
            <svg viewbox="0.000000, 0.000000, 16.600000, 16.600000" class="graphic"
                style="width: 1.66rem; height: 1.66rem; display: block; z-index: 10; left: 37.37rem; top: 57.81rem;">
                <path stroke-width="0.250000" fill="none"
                    d="M 0 5.68434e-14 L 16.567 5.68434e-14 L 16.567 16.567 L 0 16.567 L 0 5.68434e-14 Z"
                    stroke="#808285" stroke-opacity="1.000000" />
            </svg>
            <svg viewbox="0.000000, 0.000000, 16.600000, 16.600000" class="graphic"
                style="width: 1.66rem; height: 1.66rem; display: block; z-index: 10; left: 45.87rem; top: 57.81rem;">
                <path stroke-width="0.250000" fill="none"
                    d="M 0 5.68434e-14 L 16.567 5.68434e-14 L 16.567 16.567 L 0 16.567 L 0 5.68434e-14 Z"
                    stroke="#808285" stroke-opacity="1.000000" />
            </svg>
            <svg viewbox="0.000000, 0.000000, 16.600000, 16.600000" class="graphic"
                style="width: 1.66rem; height: 1.66rem; display: block; z-index: 10; left: 52.39rem; top: 57.81rem;">
                <path stroke-width="0.250000" fill="none"
                    d="M 0 5.68434e-14 L 16.567 5.68434e-14 L 16.567 16.567 L 0 16.567 L 0 5.68434e-14 Z"
                    stroke="#808285" stroke-opacity="1.000000" />
            </svg>
            <p class="paragraph body-text"
                style="width: 53.20rem; height: 1.18rem; left: 4.05rem; top: 54.54rem; text-align: left; color: #6d6e71; font-weight: 300;">
                <span class="position style"
                    style="width: 2.06rem; height: 1.04rem; left: 0.00rem; top: 0.07rem;">Name of business</span>
                <span class="position" style="width: 13.01rem; height: 1.04rem; left: 6.71rem; top: 0.07rem;">
                    <span class="style"> </span>
                    <span class="style" style="text-decoration: underline;"> </span>
                    <span class="style" style="transform: ScaleX(1.50);"> </span>
                </span>
                <span class="position style"
                    style="width: 2.75rem; height: 1.04rem; left: 20.71rem; top: 0.07rem;">Designation</span>
                <span class="position" style="width: 11.79rem; height: 1.04rem; left: 25.67rem; top: 0.07rem;">
                    <span class="style"> </span>
                    <span class="style" style="text-decoration: underline;"> </span>
                    <span class="style" style="transform: ScaleX(1.50);"> </span>
                </span>
                <span class="position style"
                    style="width: 1.04rem; height: 1.04rem; left: 38.40rem; top: 0.14rem;">Income (MVR)</span>
                <span class="position" style="width: 8.80rem; height: 1.04rem; left: 43.41rem; top: 0.14rem;">
                    <span class="style"> </span>
                    <span class="style" style="text-decoration: underline;"> </span>
                </span>
            </p>
            <div class="group" style="width: 52.09rem; height: 0.05rem; display: block; left: 4.13rem; top: 57.14rem;">
                <svg viewbox="0.000000, -0.250000, 520.950000, 1.000000" class="graphic"
                    style="width: 52.10rem; height: 0.10rem; display: block; z-index: -10; left: 0.00rem; top: 0.00rem;">
                    <path stroke-width="0.500000" fill="none" d="M 520.934 0 L 0 0" stroke="#939598"
                        stroke-opacity="1.000000" />
                </svg>
            </div>
            <svg viewbox="0.000000, 0.000000, 16.600000, 16.600000" class="graphic"
                style="width: 1.66rem; height: 1.66rem; display: block; z-index: 10; left: 12.65rem; top: 57.81rem;">
                <path stroke-width="0.250000" fill="none"
                    d="M 0 5.68434e-14 L 16.567 5.68434e-14 L 16.567 16.567 L 0 16.567 L 0 5.68434e-14 Z"
                    stroke="#808285" stroke-opacity="1.000000" />
            </svg>
            <p class="paragraph body-text"
                style="width: 7.58rem; height: 1.48rem; left: 4.02rem; top: 57.27rem; text-align: left; font-family: 'pro', serif; color: #6d6e71; font-weight: 600;">
                <span class="position style"
                    style="width: 2.10rem; height: 1.10rem; left: 0.00rem; top: 0.41rem;">Other</span>
                <span class="position style" style="width: 2.23rem; height: 1.10rem; left: 2.27rem; top: 0.41rem;">
                    Banks</span>
                <span class="position style" style="width: 2.53rem; height: 1.10rem; left: 4.85rem; top: 0.41rem;">
                    Details</span>
            </p>
            <p class="paragraph body-text"
                style="width: 7.58rem; height: 0.92rem; font-size: 0.70rem; left: 4.02rem; top: 58.75rem; text-align: left; color: #616262; font-weight: 300;">
                <span class="position style"
                    style="width: 2.05rem; height: 0.86rem; left: 0.00rem; top: 0.07rem;">(please</span>
                <span class="position style" style="width: 1.02rem; height: 0.86rem; left: 2.19rem; top: 0.07rem;">
                    tick</span>
                <span class="position style" style="width: 3.49rem; height: 0.86rem; left: 3.48rem; top: 0.07rem;">
                    accordingly)</span>
            </p>
            <p class="paragraph body-text"
                style="width: 1.69rem; height: 1.83rem; font-size: 1.10rem; left: 14.97rem; top: 57.27rem; text-align: left; font-family: pro, serif; font-weight: 300;">
                <span class="position" style="width: 1.49rem; height: 1.04rem; left: -0.00rem; top: 0.80rem;">
                    <span class="style"></span>
                    <span class="style"
                        style="font-family: 'pro', serif; font-size: 0.85rem; color: #616262;">BML</span>
                </span>
            </p>
            <p class="paragraph body-text"
                style="width: 10.09rem; height: 1.83rem; font-size: 1.10rem; left: 23.09rem; top: 57.27rem; text-align: left; font-family: pro, serif; font-weight: 300;">
                <span class="position" style="width: 1.14rem; height: 1.04rem; left: 0.00rem; top: 0.80rem;">
                    <span class="style"></span>
                    <span class="style"
                        style="font-family: 'pro', serif; font-size: 0.85rem; color: #616262;">SBI</span>
                </span>
                <span class="position style"
                    style="width: 1.57rem; height: 1.04rem; font-size: 0.85rem; left: 8.32rem; top: 0.80rem; font-family: 'pro', serif; color: #616262;">
                    MCB</span>
            </p>
            <p class="paragraph body-text"
                style="width: 2.16rem; height: 1.83rem; font-size: 1.10rem; left: 39.59rem; top: 57.27rem; text-align: left; font-family: pro, serif; font-weight: 300;">
                <span class="position" style="width: 1.96rem; height: 1.04rem; left: 0.00rem; top: 0.80rem;">
                    <span class="style"></span>
                    <span class="style"
                        style="font-family: 'pro', serif; font-size: 0.85rem; color: #616262;">HSBC</span>
                </span>
            </p>
            <p class="paragraph body-text"
                style="width: 1.77rem; height: 1.83rem; font-size: 1.10rem; left: 48.23rem; top: 57.27rem; text-align: left; font-family: pro, serif; font-weight: 300;">
                <span class="position" style="width: 1.57rem; height: 1.04rem; left: -0.00rem; top: 0.80rem;">
                    <span class="style"></span>
                    <span class="style"
                        style="font-family: 'pro', serif; font-size: 0.85rem; color: #616262;">CBM</span>
                </span>
            </p>
            <p class="paragraph body-text"
                style="width: 2.70rem; height: 1.83rem; font-size: 1.10rem; left: 54.55rem; top: 57.27rem; text-align: left; font-family: pro, serif; font-weight: 300;">
                <span class="position" style="width: 1.43rem; height: 1.04rem; left: -0.00rem; top: 0.80rem;">
                    <span class="style"></span>
                    <span class="style"
                        style="font-family: 'pro', serif; font-size: 0.85rem; color: #616262;">HBL</span>
                </span>
            </p>
            <svg viewbox="0.000000, 0.000000, 16.600000, 16.600000" class="graphic"
                style="width: 1.66rem; height: 1.66rem; display: block; z-index: 10; left: 12.65rem; top: 60.43rem;">
                <path stroke-width="0.250000" fill="none" d="M 0 0 L 16.567 0 L 16.567 16.567 L 0 16.567 L 0 0 Z"
                    stroke="#808285" stroke-opacity="1.000000" />
            </svg>
            <svg viewbox="0.000000, 0.000000, 16.600000, 16.600000" class="graphic"
                style="width: 1.66rem; height: 1.66rem; display: block; z-index: -10; left: 20.95rem; top: 60.43rem;">
                <path stroke-width="0.250000" fill="none" d="M 0 0 L 16.567 0 L 16.567 16.567 L 0 16.567 L 0 0 Z"
                    stroke="#808285" stroke-opacity="1.000000" />
            </svg>
            <svg viewbox="0.000000, 0.000000, 16.600000, 16.600000" class="graphic"
                style="width: 1.66rem; height: 1.66rem; display: block; z-index: 10; left: 29.09rem; top: 60.43rem;">
                <path stroke-width="0.250000" fill="none" d="M 0 0 L 16.567 0 L 16.567 16.567 L 0 16.567 L 0 0 Z"
                    stroke="#808285" stroke-opacity="1.000000" />
            </svg>
            <p class="paragraph body-text"
                style="width: 2.50rem; height: 1.07rem; left: 4.37rem; top: 62.67rem; text-align: left; font-family: 'pro', serif; color: #808285; font-weight: 600;">
                <span class="position style"
                    style="width: 0.47rem; height: 1.10rem; left: 0.00rem; top: -0.00rem;">A</span>
                <span class="position style"
                    style="width: 0.37rem; height: 1.10rem; left: 0.49rem; top: -0.00rem;">s</span>
                <span class="position style"
                    style="width: 0.37rem; height: 1.10rem; left: 0.86rem; top: -0.00rem;">s</span>
                <span class="position style"
                    style="width: 1.08rem; height: 1.10rem; left: 1.22rem; top: -0.00rem;">ets</span>
            </p>
            <p class="paragraph body-text"
                style="width: 10.01rem; height: 1.54rem; font-size: 1.10rem; left: 14.97rem; top: 60.19rem; text-align: left; font-family: pro, serif; font-weight: 300;">
                <span class="position" style="width: 1.52rem; height: 1.04rem; left: -0.00rem; top: 0.50rem;">
                    <span class="style"></span>
                    <span class="style"
                        style="font-family: 'pro', serif; font-size: 0.85rem; color: #616262;">BOC</span>
                </span>
                <span class="position style"
                    style="width: 1.86rem; height: 1.04rem; font-size: 0.85rem; left: 7.94rem; top: 0.50rem; font-family: 'pro', serif; color: #616262;">
                    None</span>
            </p>
            <p class="paragraph body-text"
                style="width: 25.80rem; height: 1.54rem; font-size: 1.10rem; left: 31.45rem; top: 60.19rem; text-align: left; font-family: pro, serif; font-weight: 300;">
                <span class="position" style="width: 2.48rem; height: 1.04rem; left: 0.00rem; top: 0.50rem;">
                    <span class="style"></span>
                    <span class="style"
                        style="font-family: 'pro', serif; font-size: 0.85rem; color: #616262;">Others,</span>
                </span>
                <span class="position style"
                    style="width: 2.39rem; height: 1.04rem; font-size: 0.85rem; left: 2.65rem; top: 0.50rem; font-family: 'pro', serif; color: #616262;">
                    specify</span>
                <span class="position" style="width: 18.31rem; height: 1.04rem; left: 5.80rem; top: 0.50rem;">
                    <span class="style" style="font-family: 'pro', serif; font-size: 0.85rem; color: #616262;">
                    </span>
                    <span class="style"
                        style="font-family: 'pro', serif; font-size: 0.85rem; color: #616262; text-decoration: underline;">
                    </span>
                </span>
            </p>
            <svg viewbox="0.000000, 0.000000, 16.950000, 16.250000" class="graphic"
                style="width: 1.69rem; height: 1.62rem; display: block; z-index: 10; left: 4.16rem; top: 64.22rem;">
                <path stroke-width="0.250000" fill="none"
                    d="M 16.922 16.238 L 0 16.238 L 0 2.84217e-14 L 16.922 2.84217e-14 L 16.922 16.238 Z"
                    stroke="#939598" stroke-opacity="1.000000" />
            </svg>
            <svg viewbox="0.000000, 0.000000, 16.950000, 16.250000" class="graphic"
                style="width: 1.69rem; height: 1.62rem; display: block; z-index: -10; left: 20.93rem; top: 64.22rem;">
                <path stroke-width="0.250000" fill="none"
                    d="M 16.922 16.238 L 0 16.238 L 0 2.84217e-14 L 16.922 2.84217e-14 L 16.922 16.238 Z"
                    stroke="#939598" stroke-opacity="1.000000" />
            </svg>
            <svg viewbox="0.000000, 0.000000, 16.950000, 16.250000" class="graphic"
                style="width: 1.69rem; height: 1.62rem; display: block; z-index: -10; left: 12.66rem; top: 64.22rem;">
                <path stroke-width="0.250000" fill="none"
                    d="M 16.922 16.238 L 0 16.238 L 0 2.84217e-14 L 16.922 2.84217e-14 L 16.922 16.238 Z"
                    stroke="#939598" stroke-opacity="1.000000" />
            </svg>
            <p class="paragraph body-text"
                style="width: 50.75rem; height: 1.76rem; left: 6.51rem; top: 63.74rem; text-align: left; color: #6d6e71; font-weight: 300;">
                <span class="position style"
                    style="width: 2.86rem; height: 1.04rem; left: 0.00rem; top: 0.72rem;">Building</span>
                <span class="position style" style="width: 1.73rem; height: 1.04rem; left: 8.51rem; top: 0.72rem;">
                    Land</span>
                <span class="position style" style="width: 0.47rem; height: 1.04rem; left: 16.59rem; top: 0.72rem;">
                    P</span>
                <span class="position style"
                    style="width: 2.31rem; height: 1.04rem; left: 17.04rem; top: 0.72rem;">ension</span>
                <span class="position style" style="width: 0.41rem; height: 1.04rem; left: 19.52rem; top: 0.72rem;">
                    F</span>
                <span class="position style"
                    style="width: 1.36rem; height: 1.04rem; left: 19.91rem; top: 0.72rem;">und</span>
            </p>
            <svg viewbox="0.000000, 0.000000, 16.600000, 16.600000" class="graphic"
                style="width: 1.66rem; height: 1.66rem; display: block; z-index: -10; left: 20.95rem; top: 66.44rem;">
                <path stroke-width="0.250000" fill="none"
                    d="M 0 -2.84217e-14 L 16.567 -2.84217e-14 L 16.567 16.567 L 0 16.567 L 0 -2.84217e-14 Z"
                    stroke="#808285" stroke-opacity="1.000000" />
            </svg>
            <svg viewbox="0.000000, 0.000000, 16.400000, 16.250000" class="graphic"
                style="width: 1.64rem; height: 1.62rem; display: block; z-index: 10; left: 4.16rem; top: 66.45rem;">
                <path stroke-width="0.250000" fill="none"
                    d="M 16.397 16.238 L 0 16.238 L 0 0 L 16.397 0 L 16.397 16.238 Z" stroke="#939598"
                    stroke-opacity="1.000000" />
            </svg>
            <svg viewbox="0.000000, 0.000000, 16.400000, 16.250000" class="graphic"
                style="width: 1.64rem; height: 1.62rem; display: block; z-index: -10; left: 12.66rem; top: 66.45rem;">
                <path stroke-width="0.250000" fill="none"
                    d="M 16.397 16.238 L 0 16.238 L 0 0 L 16.397 0 L 16.397 16.238 Z" stroke="#939598"
                    stroke-opacity="1.000000" />
            </svg>
            <p class="paragraph body-text"
                style="width: 50.75rem; height: 1.07rem; left: 6.50rem; top: 66.66rem; text-align: left; color: #6d6e71; font-weight: 300;">
                <span class="position style"
                    style="width: 0.42rem; height: 1.04rem; left: 0.00rem; top: 0.03rem;">V</span>
                <span class="position style"
                    style="width: 2.06rem; height: 1.04rem; left: 0.41rem; top: 0.03rem;">essels</span>
                <span class="position style" style="width: 0.42rem; height: 1.04rem; left: 8.51rem; top: 0.03rem;">
                    V</span>
                <span class="position style"
                    style="width: 2.39rem; height: 1.04rem; left: 8.92rem; top: 0.03rem;">ehicles</span>
                <span class="position style" style="width: 2.13rem; height: 1.04rem; left: 16.60rem; top: 0.03rem;">
                    Other,</span>
                <span class="position style" style="width: 2.25rem; height: 1.04rem; left: 18.90rem; top: 0.03rem;">
                    Please</span>
                <span class="position style" style="width: 2.49rem; height: 1.04rem; left: 21.32rem; top: 0.03rem;">
                    Specify</span>
                <span class="position" style="width: 23.22rem; height: 1.04rem; left: 24.71rem; top: 0.03rem;">
                    <span class="style"> </span>
                    <span class="style" style="text-decoration: underline;"> </span>
                </span>
            </p>
            <div class="group" style="width: 52.12rem; height: 2.12rem; display: block; left: 4.13rem; top: 69.25rem;">
                <div class="textbox"
                    style="background: #2a9a47; width: 45.91rem; height: 2.12rem; display: block; z-index: -10; left: 6.21rem; top: 0.00rem;">
                    <p class="paragraph"
                        style="width: 45.05rem; height: 1.66rem; z-index: -10; font-size: 1.00rem; left: 0.86rem; top: 0.00rem; text-align: left; font-family: 'pro', serif; color: #ffffff; font-weight: 600;">
                        <span class="position style"
                            style="width: 4.24rem; height: 1.29rem; left: 0.00rem; top: 0.40rem;">ACCOUNT</span>
                        <span class="position style"
                            style="width: 6.19rem; height: 1.29rem; left: 4.44rem; top: 0.40rem;"> TRANSACTION</span>
                        <span class="position style"
                            style="width: 6.18rem; height: 1.29rem; left: 10.83rem; top: 0.40rem;"> INFORMATION</span>
                    </p>
                </div>
                <div class="textbox"
                    style="background: #222e65; width: 6.21rem; height: 2.12rem; display: block; z-index: -10; left: 0.00rem; top: 0.00rem;">
                    <p class="paragraph"
                        style="width: 5.80rem; height: 1.65rem; z-index: -10; font-size: 1.00rem; left: 0.40rem; top: 0.00rem; text-align: left; font-family: 'pro', serif; color: #ffffff; font-weight: 600;">
                        <span class="position style"
                            style="width: 1.66rem; height: 1.29rem; left: 0.00rem; top: 0.38rem;">SEC</span>
                        <span class="position style"
                            style="width: 2.16rem; height: 1.29rem; left: 1.64rem; top: 0.38rem;">TION</span>
                        <span class="position style"
                            style="width: 0.63rem; height: 1.29rem; left: 4.00rem; top: 0.38rem;"> D</span>
                    </p>
                </div>
            </div>
            <p class="paragraph body-text"
                style="width: 38.41rem; height: 1.46rem; left: 4.13rem; top: 71.42rem; text-align: left; font-family: 'pro', serif; color: #6d6e71; font-weight: 600;">
                <span class="position style"
                    style="width: 3.73rem; height: 1.10rem; left: 0.00rem; top: 0.39rem;">Estimated</span>
                <span class="position style" style="width: 3.09rem; height: 1.10rem; left: 3.90rem; top: 0.39rem;">
                    monthly</span>
                <span class="position style" style="width: 1.99rem; height: 1.10rem; left: 7.16rem; top: 0.39rem;">
                    value</span>
                <span class="position style" style="width: 0.74rem; height: 1.10rem; left: 9.32rem; top: 0.39rem;">
                    of</span>
                <span class="position style" style="width: 4.57rem; height: 1.10rem; left: 10.23rem; top: 0.39rem;">
                    transactions</span>
                <span class="position style" style="width: 0.28rem; height: 1.10rem; left: 14.97rem; top: 0.39rem;">
                    (</span>
                <span class="position style" style="width: 0.70rem; height: 1.10rem; left: 15.42rem; top: 0.39rem;">
                    in</span>
                <span class="position style" style="width: 1.87rem; height: 1.10rem; left: 16.29rem; top: 0.39rem;">
                    MVR)</span>
            </p>
            <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAC0AAAAtCAYAAAA6GuKaAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAA7EAAAOxAGVKw4bAAABWklEQVRYhe3ZMUvDQBTA8aeEkOuQhgRJpswShCwOkkU/QElLoaBfxK8gziK4ODi1kNHBJUtAcNCxqxBIBkmIQ4erZNFBCokkmop9h/D+23vTj+Omu62Ly6t3+GdJAAAnx5M9prBcNOanlm/LneksmEsAAExhea/HMtGorknVIU3TgzAMz0Rh2hqPx6eqqj6u5hqac27EcXyErvqm0WgUcc53W9HVTNN8cV33HofWnKZpSpZlA8uyrqv7VrTjOE+e5002T2svjuPbpv02NuQvIjRWhMaK0FgRGitCY0VorAiNFaGxIjRWhMaK0FgRGitCY0VorAiNVev7tK7rkCRJgIn5Wp7n+037RrTv+1FRFIOiKDar+mW162Ga5utwOIwWi8WhKFCXaifNGHu2bfu8LMsbUaCm+v3+Q3WuoWVZzgzDuMMlrZ8E8PkTKhrSpZVTAgCYzoK5WM56fQDmHlIPdz86xAAAAABJRU5ErkJggg=="
                class="image"
                style="width: 1.68rem; height: 1.68rem; display: block; z-index: 0; left: 19.03rem; top: 73.64rem;" />
            <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAC0AAAAtCAYAAAA6GuKaAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAA7EAAAOxAGVKw4bAAABWklEQVRYhe3Zv0vDQBTA8Vc5SK/QJT+hi6NDDZkcg3+AwsVB0L/KQUQXJ4dWOojokDWToGuHbia4qJfiFYe66SCFtCaYKr5DeN/tPTJ8CMcNSePw6OQd/lkMAGB/b3edN7nUjfmu6dvU6fUHQwYAwJtctlr8WTeqbqw4KKVWR6PRji5MVUEQXHLO72fzHHo8Hq/FcXyAz6ouiqJEKfXKOT+d7VjVw67rPvm+f4NDK880TSal3PY876y4r0R3u927MAy1HpU0Ta+k/Ho/rGiw/DpCY0VorAiNFaGxIjRWhMaK0FgRGitCY0VorAiNFaGxIjRWhMaK0FhVfp+2LKuRZdkFJmYxKeVG2b4ULYRI8jzfyvP8b1U/bO54OI7zIoRIJpPJpi5QnebetGEYD51O59i27WtdoLLa7fZtcV5EP3qed45LWj4G8PknVDekTjMnAwDo9QdDvZzl+gBAG1J8xuOP8gAAAABJRU5ErkJggg=="
                class="image"
                style="width: 1.68rem; height: 1.68rem; display: block; z-index: 0; left: 33.92rem; top: 73.64rem;" />
            <div class="group" style="width: 1.68rem; height: 1.68rem; display: block; left: 4.12rem; top: 73.64rem;">
                <svg viewbox="0.000000, 0.000000, 16.600000, 16.600000" class="graphic"
                    style="width: 1.66rem; height: 1.66rem; display: block; z-index: -10; left: 0.01rem; top: 0.01rem;">
                    <path stroke-width="0.250000" fill="none"
                        d="M 0 -2.84217e-14 L 16.567 -2.84217e-14 L 16.567 16.567 L 0 16.567 L 0 -2.84217e-14 Z"
                        stroke="#939598" stroke-opacity="1.000000" />
                </svg>
                <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACUAAAAkCAYAAAAOwvOmAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAAuUlEQVRYhe3YIQ6DQBCF4ccGj2nIYrlBPYGEA8AhehBO0kNUV1B6gQoMvoYDrFlF1RLqnlvE+9Vk1JeMm2QYhg0nqq7rycRGHKuqajLGNGlYZFn2tdZ+YoGKogCADgB2VFmWz77vb7FQy7Lc13UFAJzqfCGh2IRiE4pNKDah2IRiE4pNKDah2IRiE4pNKDah2IRi278ueZ5f5nl+xII4565hTgGgbdu3977z3scy/ZWM4/gC0MSGHPsBCHAigw144DcAAAAASUVORK5CYII="
                    class="image"
                    style="width: 1.42rem; height: 1.34rem; display: block; z-index: -10; left: 0.14rem; top: 0.17rem;" />
                <div class="textbox"
                    style="width: 1.68rem; height: 1.68rem; display: block; z-index: -10; left: 0.00rem; top: 0.00rem;">
                    <p class="paragraph body-text"
                        style="width: 1.22rem; height: 1.39rem; z-index: -10; font-size: 0.90rem; left: 0.47rem; top: 0.00rem; text-align: left; font-family: 'pro', serif; font-weight: 400;">
                        <span class="position style"
                            style="width: 0.90rem; height: 0.90rem; left: 0.00rem; top: 0.37rem;">✔</span>
                    </p>
                </div>
            </div>
            <p class="paragraph body-text"
                style="width: 36.01rem; height: 3.29rem; left: 6.53rem; top: 72.03rem; text-align: left; color: #6d6e71; font-weight: 300;">
                <span class="position style"
                    style="width: 1.50rem; height: 1.04rem; left: 0.00rem; top: 1.88rem;">Less</span>
                <span class="position style" style="width: 1.58rem; height: 1.04rem; left: 1.67rem; top: 1.88rem;">
                    than</span>
                <span class="position style" style="width: 2.22rem; height: 1.04rem; left: 3.42rem; top: 1.88rem;">
                    20,000</span>
                <span class="position" style="width: 2.22rem; height: 1.04rem; left: 14.88rem; top: 1.88rem;">
                    <span class="style"> </span>
                    <span class="style" style="font-family: 'pro', serif; font-weight: 400;"> </span>
                    <span class="style">20,000</span>
                </span>
                <span class="position style" style="width: 0.72rem; height: 1.04rem; left: 17.27rem; top: 1.88rem;">
                    to</span>
                <span class="position style" style="width: 2.22rem; height: 1.04rem; left: 18.16rem; top: 1.88rem;">
                    50,000</span>
                <span class="position" style="width: 1.73rem; height: 1.04rem; left: 29.76rem; top: 1.88rem;">
                    <span class="style"> </span>
                    <span class="style" style="font-family: 'pro', serif; font-weight: 400;"> </span>
                    <span class="style">More</span>
                </span>
                <span class="position style" style="width: 1.58rem; height: 1.04rem; left: 31.83rem; top: 1.88rem;">
                    than</span>
                <span class="position style" style="width: 2.22rem; height: 1.04rem; left: 33.59rem; top: 1.88rem;">
                    50,000</span>
            </p>
            <p class="paragraph body-text"
                style="width: 38.41rem; height: 1.61rem; left: 4.13rem; top: 75.33rem; text-align: left; font-family: 'pro', serif; color: #6d6e71; font-weight: 600;">
                <span class="position style"
                    style="width: 3.73rem; height: 1.10rem; left: 0.00rem; top: 0.54rem;">Estimated</span>
                <span class="position style" style="width: 3.09rem; height: 1.10rem; left: 3.90rem; top: 0.54rem;">
                    monthly</span>
                <span class="position style" style="width: 2.89rem; height: 1.10rem; left: 7.16rem; top: 0.54rem;">
                    number</span>
                <span class="position style" style="width: 0.74rem; height: 1.10rem; left: 10.22rem; top: 0.54rem;">
                    of</span>
                <span class="position style" style="width: 4.57rem; height: 1.10rem; left: 11.13rem; top: 0.54rem;">
                    transactions</span>
            </p>
            <div class="group" style="width: 1.68rem; height: 1.68rem; display: block; left: 19.03rem; top: 77.61rem;">
                <svg viewbox="0.000000, 0.000000, 16.600000, 16.600000" class="graphic"
                    style="width: 1.66rem; height: 1.66rem; display: block; z-index: -10; left: 0.01rem; top: 0.01rem;">
                    <path stroke-width="0.250000" fill="none"
                        d="M 0 -2.84217e-14 L 16.567 -2.84217e-14 L 16.567 16.567 L 0 16.567 L 0 -2.84217e-14 Z"
                        stroke="#939598" stroke-opacity="1.000000" />
                </svg>
                <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACYAAAAlCAYAAAAuqZsAAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAAuUlEQVRYhe3YIQ6DQBCF4bfNmiZcAFZwiWp2D4Cl1+E+tT3BpraOcIcmBIUl2apF4J5axPvVZNSXjBszjmPCxQohxFtpxLkQQgTgbV5UVfVr2/ZT0IS6ru/7vvcAcMCapvkOw/AsxwKmaXqv6woAuNwpc4KxCcYmGJtgbIKxCcYmGJtgbIKxCcYmGJtgbIKxCcZ2WdjxhnLOYZ7nV0nMtm2PPFsA6LouppT6ZVnKqU5Z7300xvjSkHN/Jv4j/zZ3gEkAAAAASUVORK5CYII="
                    class="image"
                    style="width: 1.41rem; height: 1.39rem; display: block; z-index: -10; left: 0.14rem; top: 0.14rem;" />
            </div>
            <div class="group" style="width: 1.68rem; height: 1.68rem; display: block; left: 4.12rem; top: 77.61rem;">
                <svg viewbox="0.000000, 0.000000, 16.600000, 16.600000" class="graphic"
                    style="width: 1.66rem; height: 1.66rem; display: block; z-index: -10; left: 0.01rem; top: 0.01rem;">
                    <path stroke-width="0.250000" fill="none"
                        d="M 0 -2.84217e-14 L 16.567 -2.84217e-14 L 16.567 16.567 L 0 16.567 L 0 -2.84217e-14 Z"
                        stroke="#939598" stroke-opacity="1.000000" />
                </svg>
                <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACUAAAAmCAYAAABDClKtAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAAuklEQVRYhe3YIQ6DQBCF4beEqgokJJtwjTp2b4BY2cNwmqqeYlPVA8AF6sChCa2CUPfcIt6vJqO+ZNyYruu+OFHOuZilRhxrmiYaY1y+LYqi+Fhr36lAVVVd1nVtAWBH1XX9CiHcU6GGYXiM4wgAONX5toRiE4pNKDah2IRiE4pNKDah2IRiE4pNKDah2IRiE4pNKLb9P1WW5bXv+2cqyDzPt23OAcB7H5dlaadpSmX6K/PeRwAuNeTYDynzI+3h3BrhAAAAAElFTkSuQmCC"
                    class="image"
                    style="width: 1.40rem; height: 1.42rem; display: block; z-index: -10; left: 0.14rem; top: 0.14rem;" />
                <div class="textbox"
                    style="width: 1.68rem; height: 1.68rem; display: block; z-index: -10; left: 0.00rem; top: 0.00rem;">
                    <p class="paragraph body-text"
                        style="width: 1.23rem; height: 1.41rem; z-index: -10; font-size: 0.90rem; left: 0.46rem; top: -0.00rem; text-align: left; font-family: 'pro', serif; font-weight: 400;">
                        <span class="position style"
                            style="width: 0.90rem; height: 0.90rem; left: 0.00rem; top: 0.39rem;">✔</span>
                    </p>
                </div>
            </div>
            <div class="group" style="width: 1.68rem; height: 1.68rem; display: block; left: 33.92rem; top: 77.61rem;">
                <svg viewbox="0.000000, 0.000000, 16.600000, 16.600000" class="graphic"
                    style="width: 1.66rem; height: 1.66rem; display: block; z-index: -10; left: 0.01rem; top: 0.01rem;">
                    <path stroke-width="0.250000" fill="none"
                        d="M 0 -2.84217e-14 L 16.567 -2.84217e-14 L 16.567 16.567 L 0 16.567 L 0 -2.84217e-14 Z"
                        stroke="#939598" stroke-opacity="1.000000" />
                </svg>
                <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACUAAAAlCAYAAADFniADAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAAuElEQVRYhe3YoQ3DMBCF4ecopKBSkFmUPQpsr+AJOkpmKeggLugIMS1NYGgUtShRyh5zwPvR6dAnHTvT9/0XJ8p7n6rSiGPOuWSM8fW2aJrm07btuxTIWntZ1zUCwI7quu4VY7yXQuWcH+M4AgBOdb4todiEYhOKTSg2odiEYhOKTSg2odiEYhOKTSg2odiEYjslan8FWWuvwzA8S0Hmeb5tcw0AIYS0LEucpqmU6a8qhJAA+NKQYz8hTSPiAQ9mAgAAAABJRU5ErkJggg=="
                    class="image"
                    style="width: 1.41rem; height: 1.40rem; display: block; z-index: -10; left: 0.15rem; top: 0.15rem;" />
            </div>
            <p class="paragraph body-text"
                style="width: 36.01rem; height: 1.98rem; left: 6.53rem; top: 76.94rem; text-align: left; color: #6d6e71; font-weight: 300;">
                <span class="position style"
                    style="width: 1.48rem; height: 1.04rem; left: 0.00rem; top: 0.94rem;">0-10</span>
                <span class="position style" style="width: 1.88rem; height: 1.04rem; left: 14.88rem; top: 0.94rem;">
                    10-20</span>
                <span class="position style" style="width: 1.88rem; height: 1.04rem; left: 29.76rem; top: 0.94rem;">
                    20-30</span>
            </p>
            <p class="paragraph body-text"
                style="width: 7.61rem; height: 0.00rem; font-size: 1.10rem; left: 49.65rem; top: 72.49rem; text-align: left; font-family: pro, serif; font-weight: 300;">
            </p>
            <div class="group" style="width: 1.68rem; height: 1.68rem; display: block; left: 48.75rem; top: 77.61rem;">
                <svg viewbox="0.000000, 0.000000, 16.600000, 16.600000" class="graphic"
                    style="width: 1.66rem; height: 1.66rem; display: block; z-index: -10; left: 0.01rem; top: 0.01rem;">
                    <path stroke-width="0.250000" fill="none"
                        d="M 0 -2.84217e-14 L 16.567 -2.84217e-14 L 16.567 16.567 L 0 16.567 L 0 -2.84217e-14 Z"
                        stroke="#939598" stroke-opacity="1.000000" />
                </svg>
                <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACYAAAAmCAYAAACoPemuAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAAvElEQVRYhe3YLQ6DQBDF8bcbJPUQDDfoCfhYjcX0NBynV6jsHqEIztAgUDiabBUI3FOLeH81GfVLxo0ZhiHgYtV17W1sxLmqqry1tkn2RZqm3yzLPjFRRVH8jDEdABywsizffd8/4rGAaZqe8zwDAC53yj3B2ARjE4xNMDbB2ARjE4xNMDbB2ARjE4xNMDbB2ARjE4zt+I/leX4bx/EVE7Ou632fEwBwzvlt27plWeKpTtm2bX0IoYkNOfcH4Zck/EaEjv4AAAAASUVORK5CYII="
                    class="image"
                    style="width: 1.43rem; height: 1.40rem; display: block; z-index: -10; left: 0.14rem; top: 0.14rem;" />
            </div>
            <p class="paragraph body-text"
                style="width: 6.17rem; height: 1.07rem; left: 51.08rem; top: 77.85rem; text-align: left; color: #6d6e71; font-weight: 300;">
                <span class="position style"
                    style="width: 1.73rem; height: 1.04rem; left: 0.00rem; top: 0.03rem;">More</span>
                <span class="position style" style="width: 1.58rem; height: 1.04rem; left: 1.91rem; top: 0.03rem;">
                    than</span>
                <span class="position style" style="width: 0.81rem; height: 1.04rem; left: 3.66rem; top: 0.03rem;">
                    30</span>
            </p>
        </div>
        <div class="page" style="background: #ffffff; width: 59.55rem; height: 84.20rem; z-index: 0;">
            <div class="textbox"
                style="background: #2a9a47; width: 52.36rem; height: 2.12rem; display: block; z-index: 0; left: 3.61rem; top: 4.58rem;">
                <p class="paragraph body-text"
                    style="width: 51.60rem; height: 1.59rem; font-size: 1.00rem; left: 0.77rem; top: 0.00rem; text-align: left; font-family: 'pro', serif; color: #ffffff; font-weight: 600;">
                    <span class="position style"
                        style="width: 2.75rem; height: 1.29rem; left: 0.00rem; top: 0.33rem;">FATCA</span>
                    <span class="position style" style="width: 6.11rem; height: 1.29rem; left: 2.95rem; top: 0.33rem;">
                        DECLARATION</span>
                </p>
            </div>
            <div class="group" style="width: 1.68rem; height: 1.68rem; display: block; left: 17.54rem; top: 7.77rem;">
                <svg viewbox="0.000000, 0.000000, 16.600000, 16.600000" class="graphic"
                    style="width: 1.66rem; height: 1.66rem; display: block; z-index: -10; left: 0.01rem; top: 0.01rem;">
                    <path stroke-width="0.250000" fill="none" d="M 0 0 L 16.567 0 L 16.567 16.567 L 0 16.567 L 0 0 Z"
                        stroke="#808285" stroke-opacity="1.000000" />
                </svg>
                <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACUAAAAlCAYAAADFniADAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAAuElEQVRYhe3YIQ6DQBCF4bcEUcMBgGSPUcnuBZD0OtynqlfYpKoazlABCk2gCkLdc7vi/Woy6kvGjen7fkdCOedCFhtxrWmaYIxx+bEoiuJb1/UnFqiqqmzbthYATpS19t113SMWahzH5zRNAICkznckFJtQbEKxCcUmFJtQbEKxCcUmFJtQbEKxCcUmFJtQbEmizldQWZa3YRhesSDLstyPOQcA731Y17Wd5zmW6a/Mex8AuNiQaz8EASP3Pn/a4wAAAABJRU5ErkJggg=="
                    class="image"
                    style="width: 1.39rem; height: 1.38rem; display: block; z-index: -10; left: 0.14rem; top: 0.14rem;" />
                <div class="textbox"
                    style="width: 1.68rem; height: 1.68rem; display: block; z-index: -10; left: 0.00rem; top: 0.00rem;">
                    <p class="paragraph body-text"
                        style="width: 1.23rem; height: 1.39rem; z-index: -10; font-size: 0.90rem; left: 0.45rem; top: 0.00rem; text-align: left; font-family: 'pro', serif; font-weight: 400;">
                        <span class="position style"
                            style="width: 0.90rem; height: 0.90rem; left: 0.00rem; top: 0.36rem;">✔</span>
                    </p>
                </div>
            </div>
            <div class="textbox"
                style="width: 1.00rem; height: 1.04rem; display: block; z-index: 10; left: 19.73rem; top: 8.04rem;">
                <p class="paragraph body-text"
                    style="width: 1.00rem; height: 1.04rem; z-index: 10; left: 0.00rem; top: 0.00rem; text-align: left; color: #616262; font-weight: 300;">
                    <span class="position style"
                        style="width: 1.00rem; height: 1.04rem; left: 0.00rem; top: -0.00rem;">No</span>
                </p>
            </div>
            <p class="paragraph body-text"
                style="width: 17.37rem; height: 1.49rem; left: 3.56rem; top: 7.21rem; text-align: left; color: #616262; font-weight: 300;">
                <span class="position style"
                    style="width: 1.13rem; height: 1.04rem; left: 0.00rem; top: 0.50rem;">Are</span>
                <span class="position style" style="width: 1.28rem; height: 1.04rem; left: 1.30rem; top: 0.50rem;">
                    you</span>
                <span class="position style" style="width: 0.42rem; height: 1.04rem; left: 2.75rem; top: 0.50rem;">
                    a</span>
                <span class="position style" style="width: 2.24rem; height: 1.04rem; left: 3.33rem; top: 0.50rem;">
                    citizen</span>
                <span class="position style" style="width: 0.68rem; height: 1.04rem; left: 5.75rem; top: 0.50rem;">
                    of</span>
                <span class="position style" style="width: 1.24rem; height: 1.04rem; left: 6.59rem; top: 0.50rem;">
                    any</span>
                <span class="position style" style="width: 1.85rem; height: 1.04rem; left: 8.00rem; top: 0.50rem;">
                    other</span>
                <span class="position style" style="width: 2.99rem; height: 1.04rem; left: 10.02rem; top: 0.50rem;">
                    country?</span>
            </p>
            <p class="paragraph body-text"
                style="width: 17.37rem; height: 0.71rem; font-size: 0.60rem; left: 3.56rem; top: 8.69rem; text-align: left; color: #616262; font-weight: 300;">
                <span class="position style"
                    style="width: 0.46rem; height: 0.74rem; left: 0.00rem; top: -0.03rem;">(if</span>
                <span class="position style" style="width: 2.06rem; height: 0.74rem; left: 0.59rem; top: -0.03rem;">
                    different</span>
                <span class="position style" style="width: 1.16rem; height: 0.74rem; left: 2.76rem; top: -0.03rem;">
                    from</span>
                <span class="position style" style="width: 1.41rem; height: 0.74rem; left: 4.04rem; top: -0.03rem;">
                    home</span>
                <span class="position style" style="width: 2.03rem; height: 0.74rem; left: 5.57rem; top: -0.03rem;">
                    country)</span>
            </p>
            <p class="paragraph body-text"
                style="width: 33.17rem; height: 1.86rem; font-size: 1.10rem; left: 24.07rem; top: 7.21rem; text-align: left; font-family: pro, serif; font-weight: 300;">
                <span class="position" style="width: 1.32rem; height: 1.04rem; left: -0.00rem; top: 0.83rem;">
                    <span class="style"></span>
                    <span class="style"
                        style="font-family: 'pro', serif; font-size: 0.85rem; color: #616262;">Yes,</span>
                </span>
                <span class="position style"
                    style="width: 1.97rem; height: 1.04rem; font-size: 0.85rem; left: 1.49rem; top: 0.83rem; font-family: 'pro', serif; color: #616262;">
                    name</span>
                <span class="position style"
                    style="width: 0.68rem; height: 1.04rem; font-size: 0.85rem; left: 3.63rem; top: 0.83rem; font-family: 'pro', serif; color: #616262;">
                    of</span>
                <span class="position style"
                    style="width: 1.12rem; height: 1.04rem; font-size: 0.85rem; left: 4.48rem; top: 0.83rem; font-family: 'pro', serif; color: #616262;">
                    the</span>
                <span class="position style"
                    style="width: 2.64rem; height: 1.04rem; font-size: 0.85rem; left: 5.77rem; top: 0.83rem; font-family: 'pro', serif; color: #616262;">
                    country</span>
                <span class="position" style="width: 22.26rem; height: 1.04rem; left: 9.49rem; top: 0.83rem;">
                    <span class="style" style="font-family: 'pro', serif; font-size: 0.85rem; color: #616262;">
                    </span>
                    <span class="style"
                        style="font-family: 'pro', serif; font-size: 0.85rem; color: #616262; text-decoration: underline;">
                    </span>
                </span>
            </p>
            <div class="group" style="width: 1.68rem; height: 1.68rem; display: block; left: 21.77rem; top: 7.77rem;">
                <svg viewbox="0.000000, 0.000000, 16.600000, 16.600000" class="graphic"
                    style="width: 1.66rem; height: 1.66rem; display: block; z-index: -10; left: 0.01rem; top: 0.01rem;">
                    <path stroke-width="0.250000" fill="none" d="M 0 0 L 16.567 0 L 16.567 16.567 L 0 16.567 L 0 0 Z"
                        stroke="#808285" stroke-opacity="1.000000" />
                </svg>
                <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACUAAAAlCAYAAADFniADAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAAuklEQVRYhe3YIQ6DQBCF4bcNevWC4g7VZOECCHzPwmF6gR6he4A6Ei5QQ7IOh9kqCHXPLeL9ajLqS8aNGccx4UK1bRtuuRHnuq4LAHyxL6y137qu37lAzjm7bVsPAAeqqqrPMAyPXKhpml4xRgDApc63JxSbUGxCsQnFJhSbUGxCsQnFJhSbUGxCsQnFJhSbUGyXRB2voLIszTzPz1yQdV3v+1wAQNM0IaXUL8uSy/RX4b0PxhifG3LuB7YRI/BvTG76AAAAAElFTkSuQmCC"
                    class="image"
                    style="width: 1.38rem; height: 1.40rem; display: block; z-index: -10; left: 0.17rem; top: 0.14rem;" />
            </div>
            <div class="group" style="width: 1.87rem; height: 1.87rem; display: block; left: 3.65rem; top: 10.91rem;">
                <svg viewbox="0.000000, 0.000000, 18.450000, 18.450000" class="graphic"
                    style="width: 1.84rem; height: 1.84rem; display: block; z-index: -10; left: 0.01rem; top: 0.01rem;">
                    <path stroke-width="0.250000" fill="none" d="M 0 0 L 18.425 0 L 18.425 18.425 L 0 18.425 L 0 0 Z"
                        stroke="#808285" stroke-opacity="1.000000" />
                </svg>
                <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACoAAAArCAYAAAAOnxr+AAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAAyUlEQVRYhe3ZIQ6DQBCF4bcNpoIDAAm3qGX3AK2k1+E+dT3CJnWVhDNUEBSWLFUgisC8ZGnyfjUZ9SUjxzRNM+PgOef8KTZiL+ecB2CTZZGm6acsy1dE06Ysy87TNF0BYIXmef6u6/oej7WtbdvnMAwAgMOffklQdoKyE5SdoOwEZScoO0HZCcpOUHaCshOUnaDsBGUnKDtB2QnKTlB2fwNdnw1FUZiu6x4xMb+N43hZ5gQAqqryIYRb3/fxVDsl1lpvjLGxIXt9AR5NJAg34chNAAAAAElFTkSuQmCC"
                    class="image"
                    style="width: 1.56rem; height: 1.61rem; display: block; z-index: -10; left: 0.15rem; top: 0.13rem;" />
            </div>
            <p class="paragraph body-text"
                style="width: 49.02rem; height: 2.26rem; font-size: 0.90rem; left: 6.22rem; top: 10.73rem; text-align: center; color: #616262; font-weight: 300;">
                <span class="position style"
                    style="width: 0.22rem; height: 1.11rem; left: 0.00rem; top: 0.04rem;">I declare that I possess USA nationality/Lawful Permanent Residency/Passport and authorize Maldives Islamic Bank to <br>disclose required
information to Inland Revenue Services of USA under FATCA.</span>
            </p>
            <div class="group" style="width: 1.87rem; height: 1.87rem; display: block; left: 3.65rem; top: 13.67rem;">
                <svg viewbox="0.000000, 0.000000, 18.450000, 18.450000" class="graphic"
                    style="width: 1.84rem; height: 1.84rem; display: block; z-index: -10; left: 0.01rem; top: 0.01rem;">
                    <path stroke-width="0.250000" fill="none" d="M 0 0 L 18.425 0 L 18.425 18.425 L 0 18.425 L 0 0 Z"
                        stroke="#808285" stroke-opacity="1.000000" />
                </svg>
                <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACoAAAAqCAYAAADFw8lbAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAAx0lEQVRYhe3YMQqDQBCF4bfBIoUHEFy8Qy7gap202et4oBwg9ZIDBAQPEQQrO5Gk0iIWNg/W4n3VMNUP041pmuaLg6uqKpxiR+yp6zoAcMmySNP0UxTFK2LTRp7n52mabgCwhmZZ9vbe3+NlbbVt+xyGAQBw+NMvFMqmUDaFsimUTaFsCmVTKJtC2RTKplA2hbIplE2hbAplUyibQtkUyrb+8K21c9d1j5gx/8ZxvCxzAgBlWQYA177vo0XtSZxzwRjjYofs+QHFbCISvrbFEAAAAABJRU5ErkJggg=="
                    class="image"
                    style="width: 1.55rem; height: 1.57rem; display: block; z-index: -10; left: 0.15rem; top: 0.14rem;" />
                <div class="textbox"
                    style="width: 1.87rem; height: 1.87rem; display: block; z-index: -10; left: 0.00rem; top: -0.00rem;">
                    <p class="paragraph body-text"
                        style="width: 1.32rem; height: 1.49rem; z-index: -10; font-size: 0.90rem; left: 0.55rem; top: 0.00rem; text-align: left; font-family: 'pro', serif; font-weight: 400;">
                        <span class="position style"
                            style="width: 0.90rem; height: 0.90rem; left: 0.00rem; top: 0.46rem;">✔</span>
                    </p>
                </div>
            </div>
            <p class="paragraph body-text"
                style="width: 49.60rem; height: 3.77rem; font-size: 0.90rem; left: 6.22rem; top: 12.99rem; text-align: center; color: #616262; font-weight: 300;">
                <span class="position style"
                    style="width: 0.22rem; height: 1.11rem; left: 0.00rem; top: 0.54rem;">I declare that I do not possess USA nationality/ Lawful Permanent Residency /passport as on date. I further undertake to inform <br>the Bank
of obtaining USA Citizenship/Green card/Passport in future within material time and <br>authorize Maldives Islamic Bank to disclose required
information to Inland Revenue Services in USA.</span>
                
            </p>
            <div class="group" style="width: 1.87rem; height: 1.87rem; display: block; left: 3.65rem; top: 19.52rem;">
                <svg viewbox="0.000000, 0.000000, 18.450000, 18.450000" class="graphic"
                    style="width: 1.84rem; height: 1.84rem; display: block; z-index: -10; left: 0.01rem; top: 0.01rem;">
                    <path stroke-width="0.250000" fill="none" d="M 0 0 L 18.425 0 L 18.425 18.425 L 0 18.425 L 0 0 Z"
                        stroke="#808285" stroke-opacity="1.000000" />
                </svg>
                <div class="textbox"
                    style="width: 1.87rem; height: 1.87rem; display: block; z-index: -10; left: 0.00rem; top: -0.00rem;">
                    <p class="paragraph body-text"
                        style="width: 1.33rem; height: 1.49rem; z-index: -10; font-size: 0.90rem; left: 0.54rem; top: 0.00rem; text-align: left; font-family: 'pro', serif; font-weight: 400;">
                        <span class="position style"
                            style="width: 0.90rem; height: 0.90rem; left: 0.00rem; top: 0.46rem;">✔</span>
                    </p>
                </div>
            </div>
            <p class="paragraph body-text"
                style="width: 53.72rem; height: 1.13rem; font-size: 0.90rem; left: 3.53rem; top: 18.05rem; text-align: left; color: #616262; font-weight: 300;">
                <span class="position style"
                    style="width: 3.55rem; height: 1.11rem; left: 0.00rem; top: 0.04rem;">Politically</span>
                <span class="position style" style="width: 3.09rem; height: 1.11rem; left: 3.73rem; top: 0.04rem;">
                    Exposed</span>
                <span class="position style" style="width: 2.54rem; height: 1.11rem; left: 7.00rem; top: 0.04rem;">
                    Person</span>
                <span class="position style" style="width: 1.95rem; height: 1.11rem; left: 9.72rem; top: 0.04rem;">
                    (PEP)</span>
                <span class="position style" style="width: 4.21rem; height: 1.11rem; left: 11.85rem; top: 0.04rem;">
                    Declaration</span>
                <span class="position" style="width: 0.96rem; height: 0.74rem; left: 16.25rem; top: 0.32rem;">
                    <span class="style"> </span>
                    <span class="style" style="font-size: 0.60rem;">(For</span>
                </span>
                <span class="position style"
                    style="width: 0.97rem; height: 0.74rem; font-size: 0.60rem; left: 17.33rem; top: 0.32rem;">
                    PEP</span>
                <span class="position style"
                    style="width: 2.58rem; height: 0.74rem; font-size: 0.60rem; left: 18.42rem; top: 0.32rem;">
                    definitions</span>
                <span class="position style"
                    style="width: 1.12rem; height: 0.74rem; font-size: 0.60rem; left: 21.23rem; top: 0.32rem;">
                    refer</span>
                <span class="position style"
                    style="width: 2.26rem; height: 0.74rem; font-size: 0.60rem; left: 22.47rem; top: 0.32rem;">
                    annexure</span>
                <span class="position style"
                    style="width: 0.64rem; height: 0.74rem; font-size: 0.60rem; left: 24.98rem; top: 0.32rem;">
                    on</span>
                <span class="position style"
                    style="width: 0.87rem; height: 0.74rem; font-size: 0.60rem; left: 25.74rem; top: 0.32rem;">
                    last</span>
                <span class="position style"
                    style="width: 1.37rem; height: 0.74rem; font-size: 0.60rem; left: 26.72rem; top: 0.32rem;">
                    page)</span>
            </p>
            <p class="paragraph body-text"
                style="width: 51.03rem; height: 1.07rem; font-size: 0.90rem; left: 6.22rem; top: 19.96rem; text-align: left; font-family: 'pro', serif; color: #58595b; font-weight: 300;">
                <span class="position style"
                    style="width: 0.19rem; height: 1.02rem; left: 0.00rem; top: 0.05rem;">I</span>
                <span class="position style" style="width: 2.19rem; height: 1.02rem; left: 0.39rem; top: 0.05rem;">
                    declar</span>
                <span class="position style"
                    style="width: 0.44rem; height: 1.02rem; left: 2.57rem; top: 0.05rem;">e</span>
                <span class="position style" style="width: 1.45rem; height: 1.02rem; left: 3.20rem; top: 0.05rem;">
                    that</span>
                <span class="position style" style="width: 0.19rem; height: 1.02rem; left: 4.85rem; top: 0.05rem;">
                    I</span>
                <span class="position style" style="width: 1.15rem; height: 1.02rem; left: 5.24rem; top: 0.05rem;">
                    am</span>
                <span class="position style" style="width: 1.24rem; height: 1.02rem; left: 6.58rem; top: 0.05rem;">
                    not</span>
                <span class="position style" style="width: 0.41rem; height: 1.02rem; left: 8.01rem; top: 0.05rem;">
                    a</span>
                <span class="position style" style="width: 0.87rem; height: 1.02rem; left: 8.62rem; top: 0.05rem;">
                    PE</span>
                <span class="position style"
                    style="width: 0.33rem; height: 1.02rem; left: 9.49rem; top: 0.05rem;">P</span>
                <span class="position style"
                    style="width: 0.16rem; height: 1.02rem; left: 9.82rem; top: 0.05rem;">,</span>
                <span class="position style" style="width: 1.24rem; height: 1.02rem; left: 10.17rem; top: 0.05rem;">
                    not</span>
                <span class="position style" style="width: 0.41rem; height: 1.02rem; left: 11.61rem; top: 0.05rem;">
                    a</span>
                <span class="position style" style="width: 2.16rem; height: 1.02rem; left: 12.21rem; top: 0.05rem;">
                    family</span>
                <span class="position style" style="width: 3.43rem; height: 1.02rem; left: 14.57rem; top: 0.05rem;">
                    member/</span>
                <span class="position style" style="width: 2.84rem; height: 1.02rem; left: 18.19rem; top: 0.05rem;">
                    associat</span>
                <span class="position style"
                    style="width: 0.93rem; height: 1.02rem; left: 21.02rem; top: 0.05rem;">ed</span>
                <span class="position style" style="width: 1.60rem; height: 1.02rem; left: 22.14rem; top: 0.05rem;">
                    with</span>
                <span class="position style" style="width: 0.41rem; height: 1.02rem; left: 23.94rem; top: 0.05rem;">
                    a</span>
                <span class="position style" style="width: 1.32rem; height: 1.02rem; left: 24.54rem; top: 0.05rem;">
                    PEP</span>
            </p>
            <svg viewbox="0.000000, 0.000000, 18.450000, 18.450000" class="graphic"
                style="width: 1.84rem; height: 1.84rem; display: block; z-index: 10; left: 3.66rem; top: 22.29rem;">
                <path stroke-width="0.250000" fill="none" d="M 0 0 L 18.425 0 L 18.425 18.425 L 0 18.425 L 0 0 Z"
                    stroke="#808285" stroke-opacity="1.000000" />
            </svg>
            <p class="paragraph body-text"
                style="width: 51.03rem; height: 1.07rem; font-size: 0.90rem; left: 6.22rem; top: 22.52rem; text-align: left; font-family: 'pro', serif; color: #58595b; font-weight: 300;">
                <span class="position style"
                    style="width: 0.19rem; height: 1.02rem; left: 0.00rem; top: 0.05rem;">I</span>
                <span class="position style" style="width: 2.19rem; height: 1.02rem; left: 0.58rem; top: 0.05rem;">
                    declar</span>
                <span class="position style"
                    style="width: 0.44rem; height: 1.02rem; left: 2.77rem; top: 0.05rem;">e</span>
                <span class="position style" style="width: 1.45rem; height: 1.02rem; left: 3.40rem; top: 0.05rem;">
                    that</span>
                <span class="position style" style="width: 0.19rem; height: 1.02rem; left: 5.05rem; top: 0.05rem;">
                    I</span>
                <span class="position style" style="width: 1.15rem; height: 1.02rem; left: 5.43rem; top: 0.05rem;">
                    am</span>
                <span class="position style" style="width: 0.41rem; height: 1.02rem; left: 6.78rem; top: 0.05rem;">
                    a</span>
                <span class="position style" style="width: 0.87rem; height: 1.02rem; left: 7.38rem; top: 0.05rem;">
                    PE</span>
                <span class="position style"
                    style="width: 0.33rem; height: 1.02rem; left: 8.25rem; top: 0.05rem;">P</span>
                <span class="position style"
                    style="width: 0.16rem; height: 1.02rem; left: 8.58rem; top: 0.05rem;">,</span>
                <span class="position style" style="width: 2.16rem; height: 1.02rem; left: 9.13rem; top: 0.05rem;">
                    family</span>
                <span class="position style" style="width: 3.43rem; height: 1.02rem; left: 11.48rem; top: 0.05rem;">
                    member/</span>
                <span class="position style" style="width: 2.84rem; height: 1.02rem; left: 15.11rem; top: 0.05rem;">
                    associat</span>
                <span class="position style"
                    style="width: 0.93rem; height: 1.02rem; left: 17.94rem; top: 0.05rem;">ed</span>
                <span class="position style" style="width: 1.60rem; height: 1.02rem; left: 19.06rem; top: 0.05rem;">
                    with</span>
                <span class="position style" style="width: 0.41rem; height: 1.02rem; left: 20.85rem; top: 0.05rem;">
                    a</span>
                <span class="position style" style="width: 1.32rem; height: 1.02rem; left: 21.46rem; top: 0.05rem;">
                    PEP</span>
                <span class="position" style="width: 1.87rem; height: 0.68rem; left: 22.98rem; top: 0.31rem;">
                    <span class="style"> </span>
                    <span class="style" style="font-size: 0.60rem;">(Specify</span>
                </span>
                <span class="position style"
                    style="width: 1.55rem; height: 0.68rem; font-size: 0.60rem; left: 24.99rem; top: 0.31rem;">
                    details</span>
                <span class="position style"
                    style="width: 0.45rem; height: 0.68rem; font-size: 0.60rem; left: 26.67rem; top: 0.31rem;">
                    in</span>
                <span class="position style"
                    style="width: 1.96rem; height: 0.68rem; font-size: 0.60rem; left: 27.25rem; top: 0.31rem;">
                    annexur</span>
                <span class="position style"
                    style="width: 0.29rem; height: 0.68rem; font-size: 0.60rem; left: 29.20rem; top: 0.31rem;">e</span>
                <span class="position style"
                    style="width: 0.16rem; height: 0.68rem; font-size: 0.60rem; left: 29.62rem; top: 0.31rem;"> )</span>
            </p>
            <div class="textbox"
                style="background: #2a9a47; width: 52.36rem; height: 2.12rem; display: block; z-index: 0; left: 3.61rem; top: 25.87rem;">
                <p class="paragraph body-text"
                    style="width: 51.77rem; height: 1.74rem; font-size: 1.00rem; left: 0.60rem; top: -0.00rem; text-align: left; font-family: 'pro', serif; color: #ffffff; font-weight: 600;">
                    <span class="position style"
                        style="width: 2.97rem; height: 1.29rem; left: 0.00rem; top: 0.48rem;">TERMS AND CONDITIONS</span>
                </p>
            </div>
            <p class="paragraph body-text"
                style="width: 53.50rem; height: 1.52rem; font-size: 0.90rem; left: 3.75rem; top: 28.12rem; text-align: left; color: #58595b; font-weight: 300;">
                <span class="position style"
                    style="width: 0.22rem; height: 1.11rem; left: 0.00rem; top: 0.42rem;">I hereby agree:</span>
            </p>
            <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAkAAAAJCAYAAADgkQYQAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAAGklEQVQYlWPMzSv8z0AAMBFSMKqIgYGBgQEAAiYCXdDSmJcAAAAASUVORK5CYII="
                class="image"
                style="width: 0.36rem; height: 0.36rem; display: block; z-index: 0; left: 5.27rem; top: 30.07rem;" />
            <p class="paragraph body-text"
                style="width: 51.98rem; height: 2.51rem; font-size: 1.10rem; left: 5.27rem; top: 29.60rem; text-align: left; font-family: pro, serif; font-weight: 300;">
                <span class="position" style="width: 1.39rem; height: 1.11rem; left: 1.09rem; top: 0.14rem;">
                    <span class="style"></span>
                    <span class="style" style="font-family: 'pro', serif; font-size: 1.00rem;"> </span>
                    <span class="style"
                        style="font-family: 'pro', serif; font-size: 0.80rem; color: #58595b;">That the information and documents presented for identification purposes may be verified by the Bank’s employee having an<br> appropriate authority.</span>
                </span>
                
                <span class="position style"
                    style="width: 0.20rem; height: 1.11rem; font-size: 0.90rem; left: 4.39rem; top: 1.34rem; font-family: 'pro', serif; color: #58595b;">.</span>
            </p>
            <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAkAAAAJCAYAAADgkQYQAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAAGklEQVQYlWPMzSv8z0AAMBFSMKqIgYGBgQEAAiYCXdDSmJcAAAAASUVORK5CYII="
                class="image"
                style="width: 0.36rem; height: 0.36rem; display: block; z-index: 0; left: 5.27rem; top: 32.47rem;" />
            <p class="paragraph body-text"
                style="width: 51.98rem; height: 2.44rem; font-size: 1.10rem; left: 5.27rem; top: 32.07rem; text-align: left; font-family: pro, serif; font-weight: 300;">
                <span class="position" style="width: 1.39rem; height: 1.11rem; left: 1.09rem; top: 0.07rem;">
                    <span class="style"></span>
                    <span class="style" style="font-family: 'pro', serif; font-size: 1.00rem;"> </span>
                    <span class="style"
                        style="font-family: 'pro', serif; font-size: 0.80rem; color: #58595b;">That the details furnished above are true and correct to the best of my knowledge and belief and I undertake to inform the Bank of <br>any changes therein, immediately</span>
            </p>
            <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAkAAAAJCAYAAADgkQYQAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAAGklEQVQYlWPMzSv8z0AAMBFSMKqIgYGBgQEAAiYCXdDSmJcAAAAASUVORK5CYII="
                class="image"
                style="width: 0.36rem; height: 0.36rem; display: block; z-index: 0; left: 5.27rem; top: 34.87rem;" />
            <p class="paragraph body-text"
                style="width: 51.98rem; height: 1.15rem; font-size: 1.10rem; left: 5.27rem; top: 34.49rem; text-align: left; font-family: pro, serif; font-weight: 300;">
                <span class="position" style="width: 1.39rem; height: 1.11rem; left: 1.09rem; top: 0.05rem;">
                    <span class="style"></span>
                    <span class="style" style="font-family: 'pro', serif; font-size: 1.00rem;"> </span>
                    <span class="style"
                        style="font-family: 'pro', serif; font-size: 0.80rem; color: #58595b;">That information provided can be used only by the bank for customer relationship purposes</span>
                </span>
            </p>
            <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAkAAAAJCAYAAADgkQYQAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAAGklEQVQYlWPMzSv8z0AAMBFSMKqIgYGBgQEAAiYCXdDSmJcAAAAASUVORK5CYII="
                class="image"
                style="width: 0.36rem; height: 0.36rem; display: block; z-index: 0; left: 5.27rem; top: 36.11rem;" />
            <p class="paragraph body-text"
                style="width: 51.98rem; height: 2.51rem; font-size: 1.10rem; left: 5.27rem; top: 35.60rem; text-align: left; font-family: pro, serif; font-weight: 300;">
                <span class="position" style="width: 0.47rem; height: 1.11rem; left: 1.09rem; top: 0.14rem;">
                    <span class="style"></span>
                    <span class="style" style="font-family: 'pro', serif; font-size: 1.00rem;"> </span>
                    <span class="style" style="font-family: 'pro', serif; font-size: 0.80rem; color: #58595b;">To be bound by the terms and conditions which apply, and which may from time to time change to account(s) opened and services<br> requested by me with the Bank.</span>
                </span>
            </p>
            <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAkAAAAJCAYAAADgkQYQAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAAGklEQVQYlWPMzSv8z0AAMBFSMKqIgYGBgQEAAiYCXdDSmJcAAAAASUVORK5CYII="
                class="image"
                style="width: 0.36rem; height: 0.36rem; display: block; z-index: 0; left: 5.27rem; top: 38.54rem;" />
            <p class="paragraph body-text"
                style="width: 51.98rem; height: 2.44rem; font-size: 1.10rem; left: 5.27rem; top: 38.07rem; text-align: left; font-family: pro, serif; font-weight: 300;">
                <span class="position" style="width: 1.39rem; height: 1.11rem; left: 1.09rem; top: 0.07rem;">
                    <span class="style"></span>
                    <span class="style" style="font-family: 'pro', serif; font-size: 1.00rem;"> </span>
                    <span class="style"
                        style="font-family: 'pro', serif; font-size: 0.80rem; color: #58595b;">That having read the terms and conditions of this form (Information form for Personal Banking Customers) and agree to abide by <br>and be bound by the same including any changes therein from time to time.</span>
                </span>
            </p>
            <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAkAAAAKCAYAAABmBXS+AAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAAF0lEQVQYlWPMzSv8z0AAMBFSMKqIeEUAWeECX66rM6IAAAAASUVORK5CYII="
                class="image"
                style="width: 0.36rem; height: 0.36rem; display: block; z-index: 0; left: 5.27rem; top: 40.92rem;" />
            <p class="paragraph body-text"  style="width: 51.98rem; height: 1.15rem; font-size: 1.10rem; left: 5.27rem; top: 40.49rem; text-align: left; font-family: pro, serif; font-weight: 300;">
                <span class="position" style="width: 1.39rem; height: 1.11rem; left: 1.09rem; top: 0.05rem;">
                    <span class="style"  style="font-family: 'pro', serif; font-size: 0.80rem; color: #58595b;">That in case any of the above information is found to be false or untrue or misleading or misrepresenting, I am aware that I will be liable for it.</span>
                </span>
            </p>
            <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAkAAAAJCAYAAADgkQYQAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAAGklEQVQYlWPMzSv8z0AAMBFSMKqIgYGBgQEAAiYCXdDSmJcAAAAASUVORK5CYII="
                class="image"
                style="width: 0.36rem; height: 0.36rem; display: block; z-index: 0; left: 5.27rem; top: 42.22rem;" />
            <p class="paragraph body-text"
                style="width: 51.98rem; height: 2.51rem; font-size: 1.10rem; left: 5.27rem; top: 41.60rem; text-align: left; font-family: pro, serif; font-weight: 300;">
                <span class="position" style="width: 0.22rem; height: 1.11rem; left: 1.09rem; top: 0.14rem;">
                    <span class="style"></span>
                    <span class="style" style="font-family: 'pro', serif; font-size: 1.00rem;"> </span>
                    <span class="style" style="font-family: 'pro', serif; font-size: 0.80rem; color: #58595b;">I hereby declare and accept that the information I had previously provided to the Bank shall be accepted as the most current and <br>relevant
information in reference to those parts of the form which I have not provided new or additional information.</span>
                </span>
            </p>
            <div class="group" style="width: 52.30rem; height: 11.33rem; display: block; left: 3.55rem; top: 45.89rem;">
                <svg viewbox="0.000000, 0.000000, 522.550000, 112.850000" class="graphic"
                    style="width: 52.25rem; height: 11.29rem; display: block; z-index: -10; left: 0.02rem; top: 0.02rem;">
                    <path stroke-width="0.497000" fill="none"
                        d="M 0 112.802 L 522.547 112.802 L 522.547 0 L 0 0 L 0 112.802 Z" stroke="#939598"
                        stroke-opacity="1.000000" />
                </svg>
                <svg viewbox="0.000000, -0.100000, 156.850000, 1.000000" class="graphic"
                    style="width: 15.68rem; height: 0.10rem; display: block; z-index: -10; left: 4.75rem; top: 10.36rem;">
                    <path stroke-width="0.200000" fill="none" d="M 156.825 0 L 0 0" stroke="#abacac"
                        stroke-opacity="1.000000" />
                </svg>
                <svg viewbox="0.000000, -0.100000, 156.850000, 1.000000" class="graphic"
                    style="width: 15.68rem; height: 0.10rem; display: block; z-index: -10; left: 30.30rem; top: 10.36rem;">
                    <path stroke-width="0.200000" fill="none" d="M 156.825 0 L 0 0" stroke="#abacac"
                        stroke-opacity="1.000000" />
                </svg>
                <svg viewbox="-0.125000, 0.000000, 1.000000, 112.850000" class="graphic"
                    style="width: 0.10rem; height: 11.29rem; display: block; z-index: -10; left: 25.37rem; top: 0.02rem;">
                    <path stroke-width="0.250000" fill="none" d="M 0 0 L 0 112.802" stroke="#a7a9ac"
                        stroke-opacity="1.000000" />
                </svg>
                <div class="textbox"
                    style="width: 3.41rem; height: 1.04rem; display: block; z-index: -10; left: 26.17rem; top: 9.80rem;">
                    <p class="paragraph body-text"
                        style="width: 3.41rem; height: 1.04rem; z-index: -10; left: 0.00rem; top: 0.00rem; text-align: left; color: #616262; font-weight: 300;">
                        <span class="position style"
                            style="width: 2.19rem; height: 1.04rem; left: 0.00rem; top: -0.00rem;">Signature</span>
                    </p>
                </div>
                <div class="textbox"
                    style="width: 12.14rem; height: 0.96rem; display: block; z-index: -10; left: 26.52rem; top: 0.83rem;">
                    <p class="paragraph body-text"
                        style="width: 12.14rem; height: 0.96rem; z-index: -10; left: 0.00rem; top: -0.00rem; text-align: left; font-family: 'pro', serif; color: #58595b; font-weight: 300;">
                        <span class="position style"
                            style="width: 0.18rem; height: 0.96rem; left: -0.00rem; top: 0.00rem;">If updating the specimen signature:</span>
                    </p>
                </div>
                <div class="textbox"
                    style="width: 3.41rem; height: 1.04rem; display: block; z-index: -10; left: 0.62rem; top: 9.80rem;">
                    <p class="paragraph body-text"
                        style="width: 3.41rem; height: 1.04rem; z-index: -10; left: 0.00rem; top: 0.00rem; text-align: left; color: #616262; font-weight: 300;">
                        <span class="position style"
                            style="width: 2.19rem; height: 1.04rem; left: -0.00rem; top: -0.00rem;">Signature</span>
                    </p>
                </div>
            </div>
            <p class="paragraph body-text"
                style="width: 19.21rem; height: 1.07rem; left: 38.05rem; top: 60.02rem; text-align: left; color: #616262; font-weight: 300;">
                <span class="position style"
                    style="width: 1.61rem; height: 1.04rem; left: 0.00rem; top: 0.03rem;">Date</span>
                <span class="position" style="width: 15.68rem; height: 1.04rem; left: 2.13rem; top: 0.03rem;">
                    <span class="style"> </span>
                    <span class="style" style="text-decoration: underline;"> </span>
                </span>
            </p>
            <table class="table"
                style="width: 51.66rem; height: 7.92rem; table-layout: fixed; z-index: 0; position: absolute; left: 3.56rem; top: 71.45rem; border-collapse: collapse;">
                <tbody>
                    <tr>
                        <td style="border: none; height: 0.0rem; width: 27.180rem;" />
                        <td style="border: none; height: 0.0rem; width: 5.765rem;" />
                        <td style="border: none; height: 0.0rem; width: 10.160rem;" />
                        <td style="border: none; height: 0.0rem; width: 8.560rem;" />
                    </tr>
                    <tr>
                        <td rowspan="1" colspan="1" class="cell"
                            style="border-bottom: 0.05rem solid #939598; background: #f1f2f2; width: 27.180rem; height: 1.975rem; border-top: 0.05rem solid#939598; border-left: 0.05rem solid#939598; vertical-align: top; border-right: 0.05rem solid#939598;">
                            <p class="paragraph table-paragraph"
                                style="width: 26.56rem; height: 1.44rem; font-size: 0.85rem; left: 0.62rem; top: 0.03rem; text-align: left; font-family: 'pro', serif; color: #616262; font-weight: 400;">
                                <span class="position style"
                                    style="width: 2.24rem; height: 1.07rem; left: -0.02rem; top: 0.38rem;">Forms</span>
                                <span class="position style"
                                    style="width: 1.37rem; height: 1.07rem; left: 2.38rem; top: 0.38rem;"> and</span>
                                <span class="position style"
                                    style="width: 3.91rem; height: 1.07rem; left: 3.92rem; top: 0.38rem;">
                                    supporting</span>
                                <span class="position style"
                                    style="width: 4.02rem; height: 1.07rem; left: 8.00rem; top: 0.38rem;">
                                    documents</span>
                            </p>
                        </td>
                        <td rowspan="1" colspan="1" class="cell"
                            style="border-bottom: 0.05rem solid#939598; background: #f1f2f2; width: 5.765rem; height: 1.975rem; border-top: 0.05rem solid#939598; border-left: 0.05rem solid#939598; vertical-align: top; border-right: 0.05rem solid#939598;">
                            <p class="paragraph table-paragraph"
                                style="width: 5.05rem; height: 1.44rem; font-size: 0.85rem; left: 27.90rem; top: 0.03rem; text-align: left; font-family: 'pro', serif; color: #616262; font-weight: 300;">
                                <span class="position style"
                                    style="width: 1.58rem; height: 1.04rem; left: -0.02rem; top: 0.40rem;">Staff</span>
                                <span class="position style"
                                    style="width: 0.72rem; height: 1.04rem; left: 1.72rem; top: 0.40rem;"> ID</span>
                            </p>
                        </td>
                        <td rowspan="1" colspan="1" class="cell"
                            style="border-bottom: 0.05rem solid#939598; background: #f1f2f2; width: 10.160rem; height: 1.975rem; border-top: 0.05rem solid#939598; border-left: 0.05rem solid#939598; vertical-align: top; border-right: 0.05rem solid#939598;">
                            <p class="paragraph table-paragraph"
                                style="width: 9.53rem; height: 1.44rem; font-size: 0.85rem; left: 33.57rem; top: 0.03rem; text-align: left; font-family: 'pro', serif; color: #6d6e71; font-weight: 300;">
                                <span class="position style"
                                    style="width: 3.32rem; height: 1.04rem; left: -0.02rem; top: 0.40rem;">Signature</span>
                            </p>
                        </td>
                        <td rowspan="1" colspan="1" class="cell"
                            style="border-bottom: 0.05rem solid#939598; background: #f1f2f2; width: 8.560rem; height: 1.975rem; border-top: 0.05rem solid#939598; border-left: 0.05rem solid#939598; vertical-align: top; border-right: 0.05rem solid#939598;">
                            <p class="paragraph table-paragraph"
                                style="width: 7.83rem; height: 1.44rem; font-size: 0.85rem; left: 43.84rem; top: 0.03rem; text-align: left; font-family: 'pro', serif; color: #616262; font-weight: 300;">
                                <span class="position style"
                                    style="width: 1.61rem; height: 1.04rem; left: -0.02rem; top: 0.40rem;">Date</span>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td rowspan="1" colspan="1" class="cell"
                            style="border-bottom: 0.05rem solid#939598; background: #f1f2f2; width: 27.180rem; height: 1.975rem; border-top: 0.05rem solid#939598; border-left: 0.05rem solid#939598; vertical-align: top; border-right: 0.05rem solid#939598;">
                            <p class="paragraph table-paragraph"
                                style="width: 26.56rem; height: 1.41rem; font-size: 0.85rem; left: 0.62rem; top: 2.00rem; text-align: left; font-family: 'pro', serif; color: #616262; font-weight: 300;">
                                <span class="position style"
                                    style="width: 3.10rem; height: 1.04rem; left: -0.02rem; top: 0.38rem;">Received</span>
                                <span class="position style"
                                    style="width: 0.83rem; height: 1.04rem; left: 3.25rem; top: 0.38rem;"> by</span>
                            </p>
                        </td>
                        <td rowspan="1" colspan="1" class="cell"
                            style="border-bottom: 0.05rem solid#939598; background: #f1f2f2; width: 5.765rem; height: 1.975rem; border-top: 0.05rem solid#939598; border-left: 0.05rem solid#939598; vertical-align: top; border-right: 0.05rem solid#939598;">
                        </td>
                        <td rowspan="1" colspan="1" class="cell"
                            style="border-bottom: 0.05rem solid#939598; background: #f1f2f2; width: 10.160rem; height: 1.975rem; border-top: 0.05rem solid#939598; border-left: 0.05rem solid#939598; vertical-align: top; border-right: 0.05rem solid#939598;">
                        </td>
                        <td rowspan="1" colspan="1" class="cell"
                            style="border-bottom: 0.05rem solid#939598; background: #f1f2f2; width: 8.560rem; height: 1.975rem; border-top: 0.05rem solid#939598; border-left: 0.05rem solid#939598; vertical-align: top; border-right: 0.05rem solid#939598;">
                        </td>
                    </tr>
                    <tr>
                        <td rowspan="1" colspan="1" class="cell"
                            style="border-bottom: 0.05rem solid#939598; background: #f1f2f2; width: 27.180rem; height: 1.975rem; border-top: 0.05rem solid#939598; border-left: 0.05rem solid#939598; vertical-align: top; border-right: 0.05rem solid#939598;">
                            <p class="paragraph table-paragraph"
                                style="width: 26.56rem; height: 1.40rem; font-size: 0.85rem; left: 0.62rem; top: 3.98rem; text-align: left; font-family: 'pro', serif; color: #616262; font-weight: 300;">
                                <span class="position style"
                                    style="width: 2.99rem; height: 1.04rem; left: -0.02rem; top: 0.37rem;">Checked</span>
                                <span class="position style"
                                    style="width: 0.83rem; height: 1.04rem; left: 3.14rem; top: 0.37rem;"> by</span>
                            </p>
                        </td>
                        <td rowspan="1" colspan="1" class="cell"
                            style="border-bottom: 0.05rem solid#939598; background: #f1f2f2; width: 5.765rem; height: 1.975rem; border-top: 0.05rem solid#939598; border-left: 0.05rem solid#939598; vertical-align: top; border-right: 0.05rem solid#939598;">
                        </td>
                        <td rowspan="1" colspan="1" class="cell"
                            style="border-bottom: 0.05rem solid#939598; background: #f1f2f2; width: 10.160rem; height: 1.975rem; border-top: 0.05rem solid#939598; border-left: 0.05rem solid#939598; vertical-align: top; border-right: 0.05rem solid#939598;">
                        </td>
                        <td rowspan="1" colspan="1" class="cell"
                            style="border-bottom: 0.05rem solid#939598; background: #f1f2f2; width: 8.560rem; height: 1.975rem; border-top: 0.05rem solid#939598; border-left: 0.05rem solid#939598; vertical-align: top; border-right: 0.05rem solid#939598;">
                        </td>
                    </tr>
                    <tr>
                        <td rowspan="1" colspan="1" class="cell"
                            style="border-bottom: 0.05rem solid#939598; background: #f1f2f2; width: 27.180rem; height: 1.975rem; border-top: 0.05rem solid#939598; border-left: 0.05rem solid#939598; vertical-align: top; border-right: 0.05rem solid#939598;">
                            <p class="paragraph table-paragraph"
                                style="width: 26.56rem; height: 1.40rem; font-size: 0.85rem; left: 0.62rem; top: 5.97rem; text-align: left; font-family: 'pro', serif; color: #616262; font-weight: 300;">
                                <span class="position style"
                                    style="width: 3.75rem; height: 1.04rem; left: -0.02rem; top: 0.37rem;">Authorized</span>
                                <span class="position style"
                                    style="width: 0.83rem; height: 1.04rem; left: 3.89rem; top: 0.37rem;"> by</span>
                            </p>
                        </td>
                        <td rowspan="1" colspan="1" class="cell"
                            style="border-bottom: 0.05rem solid#939598; background: #f1f2f2; width: 5.765rem; height: 1.975rem; border-top: 0.05rem solid#939598; border-left: 0.05rem solid#939598; vertical-align: top; border-right: 0.05rem solid#939598;">
                        </td>
                        <td rowspan="1" colspan="1" class="cell"
                            style="border-bottom: 0.05rem solid#939598; background: #f1f2f2; width: 10.160rem; height: 1.975rem; border-top: 0.05rem solid#939598; border-left: 0.05rem solid#939598; vertical-align: top; border-right: 0.05rem solid#939598;">
                        </td>
                        <td rowspan="1" colspan="1" class="cell"
                            style="border-bottom: 0.05rem solid#939598; background: #f1f2f2; width: 8.560rem; height: 1.975rem; border-top: 0.05rem solid#939598; border-left: 0.05rem solid#939598; vertical-align: top; border-right: 0.05rem solid#939598;">
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="group" style="width: 52.51rem; height: 17.62rem; display: block; left: 3.25rem; top: 62.29rem;">
                <svg viewbox="0.000000, 0.000000, 525.150000, 155.000000" class="graphic"
                    style="width: 52.52rem; height: 15.50rem; display: block; z-index: -10; left: 0.00rem; top: 2.12rem;">
                    <path fill="#f1f2f2" fill-opacity="1.000000"
                        d="M 0 154.985 L 525.104 154.985 L 525.104 -2.84217e-14 L 0 -2.84217e-14 L 0 154.985 Z"
                        stroke="none" />
                </svg>
                <svg viewbox="0.000000, 0.000000, 364.000000, 39.950000" class="graphic"
                    style="width: 36.40rem; height: 4.00rem; display: block; z-index: -10; left: 8.50rem; top: 4.01rem;">
                    <path stroke-width="0.250000" fill="none"
                        d="M 16.567 -2.84217e-14 L 33.134 -2.84217e-14 L 33.134 16.567 L 16.567 16.567 L 16.567 -2.84217e-14 Z"
                        stroke="#808285" stroke-opacity="1.000000" />
                    <path stroke-width="0.250000" fill="none"
                        d="M 0 -2.84217e-14 L 16.567 -2.84217e-14 L 16.567 16.567 L 0 16.567 L 0 -2.84217e-14 Z"
                        stroke="#808285" stroke-opacity="1.000000" />
                    <path stroke-width="0.250000" fill="none"
                        d="M 254.465 -2.84217e-14 L 271.032 -2.84217e-14 L 271.032 16.567 L 254.465 16.567 L 254.465 -2.84217e-14 Z"
                        stroke="#808285" stroke-opacity="1.000000" />
                    <path stroke-width="0.250000" fill="none"
                        d="M 347.426 -2.84217e-14 L 363.993 -2.84217e-14 L 363.993 16.567 L 347.426 16.567 L 347.426 -2.84217e-14 Z"
                        stroke="#808285" stroke-opacity="1.000000" />
                    <path stroke-width="0.250000" fill="none"
                        d="M 254.465 23.347 L 271.032 23.347 L 271.032 39.914 L 254.465 39.914 L 254.465 23.347 Z"
                        stroke="#808285" stroke-opacity="1.000000" />
                    <path stroke-width="0.250000" fill="none"
                        d="M 347.426 23.347 L 363.993 23.347 L 363.993 39.914 L 347.426 39.914 L 347.426 23.347 Z"
                        stroke="#808285" stroke-opacity="1.000000" />
                    <path stroke-width="0.250000" fill="none"
                        d="M 16.567 23.347 L 33.134 23.347 L 33.134 39.914 L 16.567 39.914 L 16.567 23.347 Z"
                        stroke="#808285" stroke-opacity="1.000000" />
                </svg>
                <div class="textbox"
                    style="width: 32.84rem; height: 2.67rem; display: block; z-index: -10; left: 0.69rem; top: 2.64rem;">
                    <p class="paragraph"
                        style="width: 32.85rem; height: 1.11rem; z-index: -10; font-size: 0.90rem; left: 0.00rem; top: 0.00rem; text-align: left; font-family: 'pro', serif; color: #6d6e71; font-weight: 400;">
                        <span class="position style"
                            style="width: 2.74rem; height: 1.13rem; left: 0.00rem; top: -0.00rem;">(Please</span>
                        <span class="position style"
                            style="width: 3.57rem; height: 1.13rem; left: 2.92rem; top: -0.00rem;"> complete</span>
                        <span class="position style"
                            style="width: 3.57rem; height: 1.13rem; left: 6.67rem; top: -0.00rem;"> Annexure</span>
                        <span class="position style"
                            style="width: 0.45rem; height: 1.13rem; left: 10.60rem; top: -0.00rem;"> 1</span>
                        <span class="position style"
                            style="width: 0.28rem; height: 1.13rem; left: 11.22rem; top: -0.00rem;"> -</span>
                        <span class="position style"
                            style="width: 3.68rem; height: 1.13rem; left: 11.68rem; top: -0.00rem;"> Customer</span>
                        <span class="position style"
                            style="width: 1.56rem; height: 1.13rem; left: 15.54rem; top: -0.00rem;"> Risk</span>
                        <span class="position style"
                            style="width: 2.44rem; height: 1.13rem; left: 17.28rem; top: -0.00rem;"> Rating</span>
                        <span class="position style"
                            style="width: 2.06rem; height: 1.13rem; left: 20.07rem; top: -0.00rem;"> sheet</span>
                        <span class="position style"
                            style="width: 1.45rem; height: 1.13rem; left: 22.32rem; top: -0.00rem;"> and</span>
                        <span class="position style"
                            style="width: 2.42rem; height: 1.13rem; left: 23.94rem; top: -0.00rem;"> attach</span>
                        <span class="position style"
                            style="width: 1.66rem; height: 1.13rem; left: 26.54rem; top: -0.00rem;"> with</span>
                        <span class="position style"
                            style="width: 1.39rem; height: 1.13rem; left: 28.38rem; top: -0.00rem;"> this</span>
                        <span class="position style"
                            style="width: 2.08rem; height: 1.13rem; left: 29.95rem; top: -0.00rem;"> form)</span>
                    </p>
                    <p class="paragraph body-text"
                        style="width: 32.05rem; height: 1.54rem; z-index: -10; left: 0.79rem; top: 1.12rem; text-align: left; color: #616262; font-weight: 300;">
                        <span class="position style"
                            style="width: 1.41rem; height: 1.04rem; left: 0.00rem; top: 0.51rem;">CRP</span>
                        <span class="position style"
                            style="width: 0.46rem; height: 1.04rem; left: 1.58rem; top: 0.51rem;"> R</span>
                        <span class="position style"
                            style="width: 1.74rem; height: 1.04rem; left: 2.10rem; top: 0.51rem;">ating</span>
                        <span class="position" style="width: 1.40rem; height: 1.04rem; left: 16.19rem; top: 0.51rem;">
                            <span class="style"> </span>
                            <span class="style" style="color: #6d6e71;">Risk</span>
                        </span>
                        <span class="position style"
                            style="width: 5.03rem; height: 1.04rem; left: 17.76rem; top: 0.51rem; color: #6d6e71;">
                            Categorization</span>
                        <span class="position style"
                            style="width: 1.43rem; height: 1.04rem; left: 30.52rem; top: 0.51rem; color: #6d6e71;">
                            Low</span>
                    </p>
                </div>
                <div class="textbox"
                    style="width: 1.70rem; height: 1.04rem; display: block; z-index: -10; left: 41.23rem; top: 4.27rem;">
                    <p class="paragraph body-text"
                        style="width: 1.70rem; height: 1.04rem; z-index: -10; left: 0.00rem; top: 0.00rem; text-align: left; color: #6d6e71; font-weight: 300;">
                        <span class="position style"
                            style="width: 1.60rem; height: 1.04rem; left: -0.00rem; top: -0.00rem;">High</span>
                    </p>
                </div>
                <div class="textbox"
                    style="width: 7.59rem; height: 1.04rem; display: block; z-index: -10; left: 1.48rem; top: 6.60rem;">
                    <p class="paragraph body-text"
                        style="width: 7.59rem; height: 1.04rem; z-index: -10; left: 0.00rem; top: 0.00rem; text-align: left; color: #616262; font-weight: 300;">
                        <span class="position style"
                            style="width: 3.06rem; height: 1.04rem; left: 0.00rem; top: -0.00rem;">Sanction</span>
                        <span class="position style"
                            style="width: 1.20rem; height: 1.04rem; left: 3.23rem; top: -0.00rem;"> List</span>
                        <span class="position style"
                            style="width: 2.89rem; height: 1.04rem; left: 4.60rem; top: -0.00rem;"> checked</span>
                    </p>
                </div>
                <div class="textbox"
                    style="width: 7.84rem; height: 1.04rem; display: block; z-index: -10; left: 17.67rem; top: 6.60rem;">
                    <p class="paragraph body-text"
                        style="width: 7.84rem; height: 1.04rem; z-index: -10; left: 0.00rem; top: 0.00rem; text-align: left; color: #6d6e71; font-weight: 300;">
                        <span class="position style"
                            style="width: 1.33rem; height: 1.04rem; left: 0.00rem; top: -0.00rem;">KYC</span>
                        <span class="position style"
                            style="width: 2.47rem; height: 1.04rem; left: 1.50rem; top: -0.00rem;"> update</span>
                        <span class="position style"
                            style="width: 3.43rem; height: 1.04rem; left: 4.31rem; top: -0.00rem;"> frequency</span>
                    </p>
                </div>
                <div class="textbox"
                    style="width: 3.09rem; height: 1.04rem; display: block; z-index: -10; left: 30.44rem; top: 6.60rem;">
                    <p class="paragraph body-text"
                        style="width: 3.10rem; height: 1.04rem; z-index: -10; left: 0.00rem; top: 0.00rem; text-align: left; color: #6d6e71; font-weight: 300;">
                        <span class="position style"
                            style="width: 2.99rem; height: 1.04rem; left: -0.00rem; top: -0.00rem;">Annually</span>
                    </p>
                </div>
                <div class="textbox"
                    style="width: 5.44rem; height: 1.04rem; display: block; z-index: -10; left: 37.49rem; top: 6.60rem;">
                    <p class="paragraph body-text"
                        style="width: 5.45rem; height: 1.04rem; z-index: -10; left: 0.00rem; top: 0.00rem; text-align: left; color: #6d6e71; font-weight: 300;">
                        <span class="position style"
                            style="width: 1.80rem; height: 1.04rem; left: -0.00rem; top: -0.00rem;">Once</span>
                        <span class="position style"
                            style="width: 0.65rem; height: 1.04rem; left: 2.14rem; top: -0.00rem;"> in</span>
                        <span class="position style"
                            style="width: 0.41rem; height: 1.04rem; left: 2.96rem; top: -0.00rem;"> 3</span>
                        <span class="position style"
                            style="width: 1.81rem; height: 1.04rem; left: 3.53rem; top: -0.00rem;"> years</span>
                    </p>
                </div>
                <div class="textbox"
                    style="background: #2a9a47; width: 52.51rem; height: 2.12rem; display: block; z-index: -10; left: 0.00rem; top: 0.00rem;">
                    <p class="paragraph"
                        style="width: 51.83rem; height: 1.75rem; z-index: -10; font-size: 1.00rem; left: 0.68rem; top: 0.00rem; text-align: left; font-family: 'pro', serif; color: #ffffff; font-weight: 600;">
                        <span class="position style"
                            style="width: 1.78rem; height: 1.29rem; left: 0.00rem; top: 0.48rem;">FOR BANK USE ONLY</span>
                    </p>
                </div>
            </div>
            
            
            
            
            <
        </div>
        <div class="page" style="background: #ffffff; width: 59.55rem; height: 84.20rem; z-index: 0;">
            <div class="textbox"
                style="background: #2a9a47; width: 51.28rem; height: 2.12rem; display: block; z-index: 0; left: 4.16rem; top: 5.48rem;">
                <p class="paragraph body-text"
                    style="width: 50.49rem; height: 1.61rem; font-size: 1.00rem; left: 0.80rem; top: -0.00rem; text-align: left; font-family: 'pro', serif; color: #ffffff; font-weight: 600;">
                    <span class="position style"
                        style="width: 4.73rem; height: 1.29rem; left: 0.00rem; top: 0.34rem;">ANNEXURE</span>
                </p>
            </div>
            <p class="paragraph body-text"
                style="width: 53.17rem; height: 1.07rem; left: 4.08rem; top: 9.09rem; text-align: left; font-family: 'pro', serif; color: #808285; font-weight: 600;">
                <span class="position style"
                    style="width: 1.16rem; height: 1.10rem; left: 0.00rem; top: -0.00rem;">Please tick the appropriate box if you have been holding any of the following positions:</span>
                
            </p>
            <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAC0AAAAtCAYAAAA6GuKaAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAAlUlEQVRYhe3ZsQ2CYBRF4av5q//VbsFaJLKFMXEGSFiLKaCWFgtjLVrcF5JzJvj6c7rdH5sOVpGk7to2UeuSjfnWc10v/TBORZKi1iUi5mzU3s7ZgH8C7Qq0K9CuQLsC7Qq0K9CuQLsC7Qq0K9CuQLsC7Qq0K9CuQLsC7Qq0K9CuDoku0vuEZkP29HEWSeqHccrl/NYLz9cVh4fUCv8AAAAASUVORK5CYII="
                class="image"
                style="width: 1.68rem; height: 1.68rem; display: block; z-index: 0; left: 4.08rem; top: 10.68rem;" />
            <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAC0AAAAtCAYAAAA6GuKaAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAAlUlEQVRYhe3ZsQ2CYBRF4av5q//VbsFaJLKFMXEGSFiLKaCWFgtjLVrcF5JzJvj6c7rdH5sOVpGk7to2UeuSjfnWc10v/TBORZKi1iUi5mzU3s7ZgH8C7Qq0K9CuQLsC7Qq0K9CuQLsC7Qq0K9CuQLsC7Qq0K9CuQLsC7Qq0K9CuDoku0vuEZkP29HEWSeqHccrl/NYLz9cVh4fUCv8AAAAASUVORK5CYII="
                class="image"
                style="width: 1.68rem; height: 1.68rem; display: block; z-index: 0; left: 4.08rem; top: 12.76rem;" />
            <p class="paragraph body-text"
                style="width: 33.35rem; height: 6.03rem; font-size: 1.10rem; left: 4.08rem; top: 8.79rem; text-align: left; font-family: pro, serif; font-weight: 300;">
                <span class="position" style="width: 0.95rem; height: 1.04rem; left: 2.30rem; top: 2.16rem;">
                    <span class="style"></span>
                    <span class="style" style="font-family: 'pro', serif; font-size: 1.00rem;"> </span>
                    <span class="style" style="font-family: 'pro', serif; font-size: 0.85rem; color: #6d6e71;">Heads of State/Heads of Governments (example: President, Vice President, Prime Ministers)</span>
                </span>
                <span class="position" style="width: 2.41rem; height: 1.04rem; left: 2.30rem; top: 4.24rem;">
                    <span class="style" style="font-family: 'pro', serif; font-size: 0.85rem; color: #6d6e71;">
                    </span>
                    <span class="style"
                        style="font-family: 'pro', serif; font-size: 0.85rem; color: #6d6e71; font-weight: 400;">
                    </span>
                    <span class="style"
                        style="font-family: 'pro', serif; font-size: 0.85rem; color: #6d6e71;">Cabinet Ministers & State Ministers [includes Deputy or Assistant Ministers]</span>
                </span>
                
            </p>
            <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAC0AAAAtCAYAAAA6GuKaAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAAlUlEQVRYhe3ZsQ2CYBRF4av5q//VbsFaJLKFMXEGSFiLKaCWFgtjLVrcF5JzJvj6c7rdH5sOVpGk7to2UeuSjfnWc10v/TBORZKi1iUi5mzU3s7ZgH8C7Qq0K9CuQLsC7Qq0K9CuQLsC7Qq0K9CuQLsC7Qq0K9CuQLsC7Qq0K9CuDoku0vuEZkP29HEWSeqHccrl/NYLz9cVh4fUCv8AAAAASUVORK5CYII="
                class="image"
                style="width: 1.68rem; height: 1.68rem; display: block; z-index: 0; left: 4.08rem; top: 14.86rem;" />
            <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAC0AAAAtCAYAAAA6GuKaAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAAlUlEQVRYhe3ZsQ2CYBRF4av5q//VbsFaJLKFMXEGSFiLKaCWFgtjLVrcF5JzJvj6c7rdH5sOVpGk7to2UeuSjfnWc10v/TBORZKi1iUi5mzU3s7ZgH8C7Qq0K9CuQLsC7Qq0K9CuQLsC7Qq0K9CuQLsC7Qq0K9CuQLsC7Qq0K9CuDoku0vuEZkP29HEWSeqHccrl/NYLz9cVh4fUCv8AAAAASUVORK5CYII="
                class="image"
                style="width: 1.68rem; height: 1.68rem; display: block; z-index: 0; left: 4.08rem; top: 16.87rem;" />
            <p class="paragraph body-text"
                style="width: 22.36rem; height: 5.36rem; font-size: 1.10rem; left: 4.08rem; top: 13.50rem; text-align: left; font-family: pro, serif; font-weight: 300;">
                <span class="position" style="width: 3.19rem; height: 1.04rem; left: 2.30rem; top: 1.63rem;">
                    <span class="style"></span>
                    <span class="style" style="font-family: 'pro', serif; font-size: 1.00rem;"> </span>
                    <span class="style"
                        style="font-family: 'pro', serif; font-size: 0.85rem; color: #6d6e71;">Members of Parliament [Any Similar Legislative Bodies]</span>
                </span>
                
                <span class="position" style="width: 1.72rem; height: 1.04rem; left: 2.30rem; top: 3.64rem;">
                    <span class="style" style="font-family: 'pro', serif; font-size: 0.85rem; color: #6d6e71;">
                    </span>
                    <span class="style"
                        style="font-family: 'pro', serif; font-size: 0.85rem; color: #6d6e71; font-weight: 400;">
                    </span>
                    <span class="style"
                        style="font-family: 'pro', serif; font-size: 0.85rem; color: #6d6e71;">Judges & Magistrates</span>
                </span>
                
            </p>
            <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAC0AAAAtCAYAAAA6GuKaAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAAlUlEQVRYhe3ZsQ2CYBRF4av5q//VbsFaJLKFMXEGSFiLKaCWFgtjLVrcF5JzJvj6c7rdH5sOVpGk7to2UeuSjfnWc10v/TBORZKi1iUi5mzU3s7ZgH8C7Qq0K9CuQLsC7Qq0K9CuQLsC7Qq0K9CuQLsC7Qq0K9CuQLsC7Qq0K9CuDoku0vuEZkP29HEWSeqHccrl/NYLz9cVh4fUCv8AAAAASUVORK5CYII="
                class="image"
                style="width: 1.68rem; height: 1.68rem; display: block; z-index: 0; left: 4.08rem; top: 18.93rem;" />
            <p class="paragraph body-text"
                style="width: 53.17rem; height: 2.01rem; font-size: 1.10rem; left: 4.08rem; top: 18.60rem; text-align: left; font-family: pro, serif; font-weight: 300;">
                <span class="position" style="width: 1.43rem; height: 1.04rem; left: 2.30rem; top: 0.60rem;">
                    <span class="style"></span>
                    <span class="style" style="font-family: 'pro', serif; font-size: 1.00rem;"> </span>
                    <span class="style"
                        style="font-family: 'pro', serif; font-size: 0.85rem; color: #6d6e71;">Elected Council Members</span>
                </span>
            </p>
            <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAC0AAAAtCAYAAAA6GuKaAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAAlUlEQVRYhe3ZsQ2CYBRF4av5q//VbsFaJLKFMXEGSFiLKaCWFgtjLVrcF5JzJvj6c7rdH5sOVpGk7to2UeuSjfnWc10v/TBORZKi1iUi5mzU3s7ZgH8C7Qq0K9CuQLsC7Qq0K9CuQLsC7Qq0K9CuQLsC7Qq0K9CuQLsC7Qq0K9CuDoku0vuEZkP29HEWSeqHccrl/NYLz9cVh4fUCv8AAAAASUVORK5CYII="
                class="image"
                style="width: 1.68rem; height: 1.68rem; display: block; z-index: 0; left: 4.08rem; top: 21.01rem;" />
            <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAC0AAAAtCAYAAAA6GuKaAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAAlUlEQVRYhe3ZsQ2CYBRF4av5q//VbsFaJLKFMXEGSFiLKaCWFgtjLVrcF5JzJvj6c7rdH5sOVpGk7to2UeuSjfnWc10v/TBORZKi1iUi5mzU3s7ZgH8C7Qq0K9CuQLsC7Qq0K9CuQLsC7Qq0K9CuQLsC7Qq0K9CuQLsC7Qq0K9CuDoku0vuEZkP29HEWSeqHccrl/NYLz9cVh4fUCv8AAAAASUVORK5CYII="
                class="image"
                style="width: 1.68rem; height: 1.68rem; display: block; z-index: 0; left: 4.08rem; top: 23.02rem;" />
            <p class="paragraph body-text"
                style="width: 39.49rem; height: 5.72rem; font-size: 1.10rem; left: 4.08rem; top: 19.29rem; text-align: left; font-family: pro, serif; font-weight: 300;">
                <span class="position" style="width: 3.19rem; height: 1.04rem; left: 2.30rem; top: 1.99rem;">
                    <span class="style"></span>
                    <span class="style" style="font-family: 'pro', serif; font-size: 1.00rem;"> </span>
                    <span class="style"
                        style="font-family: 'pro', serif; font-size: 0.85rem; color: #6d6e71;">Members & Senior Most Officials of a State Agency or Institution [like members of boards of central banks]</span>
                </span>
               
                <span class="position" style="width: 2.22rem; height: 1.04rem; left: 2.30rem; top: 4.00rem;">
                    <span class="style" style="font-family: 'pro', serif; font-size: 0.85rem; color: #6d6e71;">
                    </span>
                    <span class="style"
                        style="font-family: 'pro', serif; font-size: 0.85rem; color: #6d6e71; font-weight: 400;">
                    </span>
                    <span class="style"
                        style="font-family: 'pro', serif; font-size: 0.85rem; color: #6d6e71;">Senior Military Officials (Chief and vice chief of defense force)</span>
                </span>
                
            </p>
            <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAC0AAAAtCAYAAAA6GuKaAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAAlUlEQVRYhe3ZsQ2CYBRF4av5q//VbsFaJLKFMXEGSFiLKaCWFgtjLVrcF5JzJvj6c7rdH5sOVpGk7to2UeuSjfnWc10v/TBORZKi1iUi5mzU3s7ZgH8C7Qq0K9CuQLsC7Qq0K9CuQLsC7Qq0K9CuQLsC7Qq0K9CuQLsC7Qq0K9CuDoku0vuEZkP29HEWSeqHccrl/NYLz9cVh4fUCv8AAAAASUVORK5CYII="
                class="image"
                style="width: 1.68rem; height: 1.68rem; display: block; z-index: 0; left: 4.08rem; top: 25.08rem;" />
            <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAC0AAAAtCAYAAAA6GuKaAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAAlUlEQVRYhe3ZsQ2CYBRF4av5q//VbsFaJLKFMXEGSFiLKaCWFgtjLVrcF5JzJvj6c7rdH5sOVpGk7to2UeuSjfnWc10v/TBORZKi1iUi5mzU3s7ZgH8C7Qq0K9CuQLsC7Qq0K9CuQLsC7Qq0K9CuQLsC7Qq0K9CuQLsC7Qq0K9CuDoku0vuEZkP29HEWSeqHccrl/NYLz9cVh4fUCv8AAAAASUVORK5CYII="
                class="image"
                style="width: 1.68rem; height: 1.68rem; display: block; z-index: 0; left: 4.08rem; top: 27.16rem;" />
            <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAC0AAAAtCAYAAAA6GuKaAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAAlUlEQVRYhe3ZsQ2CYBRF4av5q//VbsFaJLKFMXEGSFiLKaCWFgtjLVrcF5JzJvj6c7rdH5sOVpGk7to2UeuSjfnWc10v/TBORZKi1iUi5mzU3s7ZgH8C7Qq0K9CuQLsC7Qq0K9CuQLsC7Qq0K9CuQLsC7Qq0K9CuQLsC7Qq0K9CuDoku0vuEZkP29HEWSeqHccrl/NYLz9cVh4fUCv8AAAAASUVORK5CYII="
                class="image"
                style="width: 1.68rem; height: 1.68rem; display: block; z-index: 0; left: 4.08rem; top: 29.29rem;" />
            <p class="paragraph body-text"
                style="width: 33.35rem; height: 8.82rem; font-size: 1.10rem; left: 4.08rem; top: 22.57rem; text-align: left; font-family: pro, serif; font-weight: 300;">
                <span class="position" style="width: 2.22rem; height: 1.04rem; left: 2.30rem; top: 2.78rem;">
                    <span class="style"></span>
                    <span class="style" style="font-family: 'pro', serif; font-size: 1.00rem;"> </span>
                    <span class="style"
                        style="font-family: 'pro', serif; font-size: 0.85rem; color: #6d6e71;">Senior Officials appointed as per the provisions of a specific law (example: Head of FIU)</span>
                </span>
                
                <span class="position" style="width: 2.22rem; height: 1.04rem; left: 2.30rem; top: 4.86rem;">
                    <span class="style" style="font-family: 'pro', serif; font-size: 0.85rem; color: #6d6e71;">
                    </span>
                    <span class="style"
                        style="font-family: 'pro', serif; font-size: 0.85rem; color: #6d6e71; font-weight: 400;">
                    </span>
                    <span class="style"
                        style="font-family: 'pro', serif; font-size: 0.85rem; color: #6d6e71;">Senior Political Appointees of a Government (example: Coordinators at various Ministries)</span>
                </span>
               
                <span class="position" style="width: 0.95rem; height: 1.04rem; left: 2.30rem; top: 6.99rem;">
                    <span class="style" style="font-family: 'pro', serif; font-size: 0.85rem; color: #6d6e71;">
                    </span>
                    <span class="style"
                        style="font-family: 'pro', serif; font-size: 0.85rem; color: #6d6e71; font-weight: 400;">
                    </span>
                    <span class="style" style="font-family: 'pro', serif; font-size: 0.85rem; color: #6d6e71;">Board Members of State-Owned Enterprises (eg: STO, Fenaka, MWSC, Etc…)</span>
                </span>
                
            </p>
            <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAC0AAAAtCAYAAAA6GuKaAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAAlUlEQVRYhe3ZsQ2CYBRF4av5q//VbsFaJLKFMXEGSFiLKaCWFgtjLVrcF5JzJvj6c7rdH5sOVpGk7to2UeuSjfnWc10v/TBORZKi1iUi5mzU3s7ZgH8C7Qq0K9CuQLsC7Qq0K9CuQLsC7Qq0K9CuQLsC7Qq0K9CuQLsC7Qq0K9CuDoku0vuEZkP29HEWSeqHccrl/NYLz9cVh4fUCv8AAAAASUVORK5CYII="
                class="image"
                style="width: 1.68rem; height: 1.68rem; display: block; z-index: 0; left: 4.08rem; top: 31.34rem;" />
            <p class="paragraph body-text"
                style="width: 53.17rem; height: 1.64rem; font-size: 1.10rem; left: 4.08rem; top: 31.39rem; text-align: left; font-family: pro, serif; font-weight: 300;">
                <span class="position" style="width: 0.41rem; height: 1.04rem; left: 2.30rem; top: 0.22rem;">
                    <span class="style"></span>
                    <span class="style" style="font-family: 'pro', serif; font-size: 1.00rem;"> </span>
                    <span class="style" style="font-family: 'pro', serif; font-size: 0.85rem; color: #6d6e71;">Foreign and Local Diplomats [include ambassadors, chargés d'affaires etc.]</span>
                </span>
                
            </p>
            <svg viewbox="0.000000, 0.000000, 16.600000, 16.600000" class="graphic"
                style="width: 1.66rem; height: 1.66rem; display: block; z-index: 10; left: 4.09rem; top: 33.34rem;">
                <path stroke-width="0.250000" fill="none" d="M 0 0 L 16.567 0 L 16.567 16.567 L 0 16.567 L 0 0 Z"
                    stroke="#808285" stroke-opacity="1.000000" />
            </svg>
            <p class="paragraph body-text"
                style="width: 50.88rem; height: 1.61rem; left: 6.38rem; top: 33.02rem; text-align: left; color: #6d6e71; font-weight: 300;">
                <span class="position style"
                    style="width: 2.22rem; height: 1.04rem; left: 0.00rem; top: 0.58rem;">Senior Political Party Members [including members of the governing bodies of political parties]</span>
                
            </p>
            <p class="paragraph body-text"
                style="width: 53.17rem; height: 1.07rem; left: 4.08rem; top: 37.77rem; text-align: left; font-family: 'pro', serif; color: #808285; font-weight: 600;">
                <span class="position style"
                    style="width: 1.08rem; height: 1.10rem; left: 0.00rem; top: -0.00rem;">OR</span>
            </p>
            <p class="paragraph body-text"
                style="width: 53.17rem; height: 1.07rem; left: 4.08rem; top: 41.06rem; text-align: left; font-family: 'pro', serif; color: #808285; font-weight: 600;">
                <span class="position style"
                    style="width: 0.51rem; height: 1.10rem; left: 0.00rem; top: 0.00rem;">If the answer to the above is ‘NO’, please tick any of the following boxes, if applicable:</span>
                
            </p>
            <svg viewbox="0.000000, 0.000000, 16.600000, 16.600000" class="graphic"
                style="width: 1.66rem; height: 1.66rem; display: block; z-index: 10; left: 4.09rem; top: 43.40rem;">
                <path stroke-width="0.250000" fill="none" d="M 0 0 L 16.567 0 L 16.567 16.567 L 0 16.567 L 0 0 Z"
                    stroke="#808285" stroke-opacity="1.000000" />
            </svg>
            <p class="paragraph body-text"
                style="width: 50.88rem; height: 1.07rem; left: 6.37rem; top: 43.63rem; text-align: left; color: #6d6e71; font-weight: 300;">
                <span class="position style"
                    style="width: 0.20rem; height: 1.04rem; left: 0.00rem; top: 0.03rem;">I am actively seeking or being considered for above stated positions;</span>
                
            </p>
            <svg viewbox="0.000000, 0.000000, 16.600000, 16.600000" class="graphic"
                style="width: 1.66rem; height: 1.66rem; display: block; z-index: 10; left: 4.09rem; top: 45.48rem;">
                <path stroke-width="0.250000" fill="none" d="M 0 0 L 16.567 0 L 16.567 16.567 L 0 16.567 L 0 0 Z"
                    stroke="#808285" stroke-opacity="1.000000" />
            </svg>
            <p class="paragraph body-text"
                style="width: 50.88rem; height: 2.08rem; left: 6.37rem; top: 44.69rem; text-align: left; color: #6d6e71; font-weight: 300;">
                <span class="position style"
                    style="width: 0.20rem; height: 1.04rem; left: 0.00rem; top: 1.04rem;">I have been retired for less than 12 months from the above-mentioned positions;</span>
                
            </p>
            <svg viewbox="0.000000, 0.000000, 16.600000, 16.600000" class="graphic"
                style="width: 1.66rem; height: 1.66rem; display: block; z-index: 10; left: 4.09rem; top: 47.58rem;">
                <path stroke-width="0.250000" fill="none" d="M 0 0 L 16.567 0 L 16.567 16.567 L 0 16.567 L 0 0 Z"
                    stroke="#808285" stroke-opacity="1.000000" />
            </svg>
            <p class="paragraph body-text"
                style="width: 49.93rem; height: 2.75rem; left: 6.37rem; top: 46.77rem; text-align: left; color: #6d6e71; font-weight: 300;">
                <span class="position style"
                    style="width: 0.97rem; height: 1.04rem; left: 0.00rem; top: 0.71rem;">My Close Family Members [Parents, Spouses, Children, sibling etc.] – are holding, OR actively seeking OR being considered OR retired for<br> less than
12 months from the above stated positions. (Please Complete below)</span>
               
            </p>
            <svg viewbox="0.000000, 0.000000, 16.600000, 16.600000" class="graphic"
                style="width: 1.66rem; height: 1.66rem; display: block; z-index: 10; left: 4.09rem; top: 50.65rem;">
                <path stroke-width="0.250000" fill="none" d="M 0 0 L 16.567 0 L 16.567 16.567 L 0 16.567 L 0 0 Z"
                    stroke="#808285" stroke-opacity="1.000000" />
            </svg>
            <p class="paragraph body-text"
                style="width: 50.88rem; height: 2.92rem; left: 6.38rem; top: 49.52rem; text-align: left; color: #6d6e71; font-weight: 300;">
                <span class="position style"
                    style="width: 1.27rem; height: 1.04rem; left: 0.00rem; top: 0.88rem;">Any individual holding any of the above stated position is associated party with my Business and holds more than 25% voting rights/share<br> in your
Business/Company; (Please Complete below)</span>
                
            </p>
            <svg viewbox="0.000000, 0.000000, 16.600000, 16.600000" class="graphic"
                style="width: 1.66rem; height: 1.66rem; display: block; z-index: 10; left: 4.09rem; top: 53.45rem;">
                <path stroke-width="0.250000" fill="none" d="M 0 0 L 16.567 0 L 16.567 16.567 L 0 16.567 L 0 0 Z"
                    stroke="#808285" stroke-opacity="1.000000" />
            </svg>
            <p class="paragraph body-text"
                style="width: 50.88rem; height: 3.98rem; left: 6.38rem; top: 52.44rem; text-align: left; color: #6d6e71; font-weight: 300;">
                <span class="position style"
                    style="width: 1.27rem; height: 1.04rem; left: 0.00rem; top: 0.92rem;">Any individual holding any of the above stated position has significant influence over the policy, business and strategy of my<br> Business/Company
implying that the individual takes part in day to day management and the position is not an isolated consultative role or <br>a non—executive role.
(Please Complete below)</span>
                
            </p>
            <svg viewbox="0.000000, 0.000000, 16.600000, 16.600000" class="graphic"
                style="width: 1.66rem; height: 1.66rem; display: block; z-index: 10; left: 4.09rem; top: 57.44rem;">
                <path stroke-width="0.250000" fill="none" d="M 0 0 L 16.567 0 L 16.567 16.567 L 0 16.567 L 0 0 Z"
                    stroke="#808285" stroke-opacity="1.000000" />
            </svg>
            <p class="paragraph body-text"
                style="width: 49.92rem; height: 2.97rem; left: 6.38rem; top: 56.41rem; text-align: left; color: #6d6e71; font-weight: 300;">
                <span class="position style"
                    style="width: 0.20rem; height: 1.04rem; left: 0.00rem; top: 0.93rem;">I have a joint beneficial ownership of a legal entity or a legal arrangement (for example company or trust etc.) or any other <br>close business
relationship with an individual holding any of the above stated positions;</span>
                
            </p>
            <svg viewbox="0.000000, 0.000000, 16.600000, 16.600000" class="graphic"
                style="width: 1.66rem; height: 1.66rem; display: block; z-index: 10; left: 4.09rem; top: 60.97rem;">
                <path stroke-width="0.250000" fill="none"
                    d="M 0 -5.68434e-14 L 16.567 -5.68434e-14 L 16.567 16.567 L 0 16.567 L 0 -5.68434e-14 Z"
                    stroke="#808285" stroke-opacity="1.000000" />
            </svg>
            <p class="paragraph body-text"
                style="width: 49.73rem; height: 2.03rem; left: 6.58rem; top: 60.88rem; text-align: left; color: #6d6e71; font-weight: 300;">
                <span class="position style"
                    style="width: 0.20rem; height: 1.04rem; left: 0.00rem; top: -0.01rem;">I have a sole beneficial ownership of a legal entity or a legal arrangement (for example company or trust etc.) which is set up by a person <br>holding
any of the above stated positions;</span>
                
            </p>
            <svg viewbox="0.000000, -0.123000, 422.450000, 1.000000" class="graphic"
                style="width: 42.24rem; height: 0.10rem; display: block; z-index: 10; left: 12.75rem; top: 68.86rem;">
                <path stroke-width="0.246000" fill="none" d="M 0 0 L 422.435 0" stroke="#a7a9ac"
                    stroke-opacity="1.000000" />
            </svg>
            <p class="paragraph body-text"
                style="width: 51.30rem; height: 5.00rem; left: 4.08rem; top: 65.14rem; text-align: left; color: #6d6e71; font-weight: 300;">
                <span class="position style"
                    style="width: 0.41rem; height: 1.04rem; left: 0.00rem; top: 0.03rem;">Full Name</span>
                </span>
                <span class="position" style="width: 2.75rem; height: 1.04rem; left: 0.00rem; top: 2.53rem;">
                    <span class="style" style="text-decoration: underline;"> </span>
                    <span class="style"> Designation / Position</span>
                </span>
            </p>
        </div>
        <div class="page" style="background: #ffffff; width: 59.55rem; height: 84.20rem; z-index: 0;">
            <img src="data:image/png;base64,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"
                class="image"
                style="width: 2.89rem; height: 2.12rem; display: block; z-index: 0; left: 3.68rem; top: 5.52rem;" />
            <div class="group" style="width: 13.95rem; height: 5.41rem; display: block; left: 42.02rem; top: 5.58rem;">
                <svg viewbox="0.000000, 0.000000, 139.550000, 36.750000" class="graphic"
                    style="width: 13.96rem; height: 3.67rem; display: block; z-index: -10; left: 0.00rem; top: 1.74rem;">
                    <path fill="#f1f0f0" fill-opacity="1.000000"
                        d="M 0 36.727 L 139.549 36.727 L 139.549 0 L 0 0 L 0 36.727 Z" stroke="none" />
                </svg>
                <div class="textbox"
                    style="width: 13.95rem; height: 3.67rem; display: block; z-index: -10; left: 0.00rem; top: 1.74rem;">
                    <p class="paragraph body-text"
                        style="width: 13.72rem; height: 1.33rem; z-index: -10; font-size: 0.70rem; left: 0.23rem; top: -0.00rem; text-align: left; color: #6d6e71; font-weight: 300;">
                        <span class="position style"
                            style="width: 0.90rem; height: 0.86rem; left: 0.00rem; top: 0.48rem;">CIF</span>
                        <span class="position style"
                            style="width: 1.05rem; height: 0.86rem; left: 1.04rem; top: 0.48rem;"> NO.</span>
                    </p>
                </div>
                <div class="textbox"
                    style="background: #2a9a47; width: 13.95rem; height: 1.74rem; display: block; z-index: -10; left: 0.00rem; top: 0.00rem;">
                    <p class="paragraph"
                        style="width: 13.76rem; height: 1.39rem; z-index: -10; font-size: 0.80rem; left: 0.20rem; top: 0.00rem; text-align: left; font-family: 'pro', serif; color: #ffffff; font-weight: 400;">
                        <span class="position style"
                            style="width: 1.38rem; height: 1.01rem; left: 0.00rem; top: 0.39rem;">FOR</span>
                        <span class="position style"
                            style="width: 1.89rem; height: 1.01rem; left: 1.54rem; top: 0.39rem;"> BANK</span>
                        <span class="position style"
                            style="width: 1.36rem; height: 1.01rem; left: 3.59rem; top: 0.39rem;"> USE</span>
                        <span class="position style"
                            style="width: 1.82rem; height: 1.01rem; left: 5.11rem; top: 0.39rem;"> ONLY</span>
                    </p>
                </div>
            </div>
            <div class="textbox"
                style="width: 10.25rem; height: 1.26rem; display: block; z-index: 10; left: 45.06rem; top: 7.66rem;">
                <table class="table"
                    style="width: 10.24rem; height: 1.25rem; table-layout: fixed; z-index: 10; position: absolute; left: 0.00rem; top: 0.01rem; border-collapse: collapse;">
                    <tbody>
                        <tr>
                            <td style="border: none; height: 0.0rem; width: 1.280rem;" />
                            <td style="border: none; height: 0.0rem; width: 1.280rem;" />
                            <td style="border: none; height: 0.0rem; width: 1.280rem;" />
                            <td style="border: none; height: 0.0rem; width: 1.280rem;" />
                            <td style="border: none; height: 0.0rem; width: 1.280rem;" />
                            <td style="border: none; height: 0.0rem; width: 1.280rem;" />
                            <td style="border: none; height: 0.0rem; width: 1.280rem;" />
                            <td style="border: none; height: 0.0rem; width: 1.280rem;" />
                        </tr>
                        <tr>
                            <td rowspan="1" colspan="1" class="cell"
                                style="border-bottom: 0.03rem solid#616262; background: #f1f0f0; width: 1.280rem; height: 1.240rem; border-top: 0.03rem solid#616262; border-left: 0.03rem solid#616262; vertical-align: top; border-right: 0.03rem solid#616262;">
                            </td>
                            <td rowspan="1" colspan="1" class="cell"
                                style="border-bottom: 0.03rem solid#616262; background: #f1f0f0; width: 1.280rem; height: 1.240rem; border-top: 0.03rem solid#616262; border-left: 0.03rem solid#616262; vertical-align: top; border-right: 0.03rem solid#616262;">
                            </td>
                            <td rowspan="1" colspan="1" class="cell"
                                style="border-bottom: 0.03rem solid#616262; background: #f1f0f0; width: 1.280rem; height: 1.240rem; border-top: 0.03rem solid#616262; border-left: 0.03rem solid#616262; vertical-align: top; border-right: 0.03rem solid#616262;">
                            </td>
                            <td rowspan="1" colspan="1" class="cell"
                                style="border-bottom: 0.03rem solid#616262; background: #f1f0f0; width: 1.280rem; height: 1.240rem; border-top: 0.03rem solid#616262; border-left: 0.03rem solid#616262; vertical-align: top; border-right: 0.03rem solid#616262;">
                            </td>
                            <td rowspan="1" colspan="1" class="cell"
                                style="border-bottom: 0.03rem solid#616262; background: #f1f0f0; width: 1.280rem; height: 1.240rem; border-top: 0.03rem solid#616262; border-left: 0.03rem solid#616262; vertical-align: top; border-right: 0.03rem solid#616262;">
                            </td>
                            <td rowspan="1" colspan="1" class="cell"
                                style="border-bottom: 0.03rem solid#616262; background: #f1f0f0; width: 1.280rem; height: 1.240rem; border-top: 0.03rem solid#616262; border-left: 0.03rem solid#616262; vertical-align: top; border-right: 0.03rem solid#616262;">
                            </td>
                            <td rowspan="1" colspan="1" class="cell"
                                style="border-bottom: 0.03rem solid#616262; background: #f1f0f0; width: 1.280rem; height: 1.240rem; border-top: 0.03rem solid#616262; border-left: 0.03rem solid#616262; vertical-align: top; border-right: 0.03rem solid#616262;">
                            </td>
                            <td rowspan="1" colspan="1" class="cell"
                                style="border-bottom: 0.03rem solid#616262; background: #f1f0f0; width: 1.280rem; height: 1.240rem; border-top: 0.03rem solid#616262; border-left: 0.03rem solid#616262; vertical-align: top; border-right: 0.03rem solid#616262;">
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <p class="paragraph body-text"
                style="width: 53.58rem; height: 2.46rem; font-size: 1.10rem; left: 3.67rem; top: 5.49rem; text-align: left; font-family: pro, serif;">
                <span class="position" style="width: 1.04rem; height: 1.55rem; left: 3.20rem; top: 0.90rem;">
                    <span class="style"></span>
                    <span class="style" style="font-family: 'pro', serif; font-size: 1.00rem;"> </span>
                    <span id="information-form-individual-last-page" class="style"
                        style="font-family: 'pro', serif; font-size: 1.30rem; color: #292929;">M</span>
                </span>
                <span class="position style"
                    style="width: 2.58rem; height: 1.55rem; font-size: 1.30rem; left: 4.25rem; top: 0.90rem; font-family: 'pro', serif; color: #292929;">aldiv</span>
                <span class="position style"
                    style="width: 1.16rem; height: 1.55rem; font-size: 1.30rem; left: 6.82rem; top: 0.90rem; font-family: 'pro', serif; color: #292929;">es</span>
                <span class="position style"
                    style="width: 3.71rem; height: 1.55rem; font-size: 1.30rem; left: 8.25rem; top: 0.90rem; font-family: 'pro', serif; color: #292929;">
                    Islamic</span>
                <span class="position style"
                    style="width: 2.65rem; height: 1.55rem; font-size: 1.30rem; left: 12.24rem; top: 0.90rem; font-family: 'pro', serif; color: #292929;">
                    Bank</span>
            </p>
            <p class="paragraph heading-1"
                style="width: 53.73rem; height: 1.88rem; left: 3.52rem; top: 7.96rem; text-align: left; color: #222e65; font-weight: 600;">
                <span class="position style"
                    style="width: 9.88rem; height: 2.07rem; left: 0.00rem; top: -0.02rem;">INFORMATION FORM</span>
                
            </p>
            <p class="paragraph heading-2"
                style="width: 53.58rem; height: 1.58rem; left: 3.67rem; top: 9.38rem; text-align: left; font-family: 'pro', serif; color: #2a9a47; font-weight: 400;">
                <span class="position style"
                    style="width: 3.83rem; height: 1.26rem; left: 0.00rem; top: 0.34rem;">INDIVIDUAL</span>
            </p>
            <div class="group" style="width: 52.23rem; height: 2.12rem; display: block; left: 3.65rem; top: 11.41rem;">
                <div class="textbox"
                    style="background: #2a9a47; width: 46.03rem; height: 2.12rem; display: block; z-index: -10; left: 6.21rem; top: 0.00rem;">
                    <p class="paragraph"
                        style="width: 45.15rem; height: 1.73rem; z-index: -10; font-size: 1.00rem; left: 0.88rem; top: 0.00rem; text-align: left; font-family: 'pro', serif; color: #ffffff; font-weight: 600;">
                        <span class="position style"
                            style="width: 1.65rem; height: 1.29rem; left: 0.00rem; top: 0.47rem;">TAX INFORMATION</span>
                       
                    </p>
                </div>
                <div class="textbox"
                    style="background: #222e65; width: 6.21rem; height: 2.12rem; display: block; z-index: -10; left: 0.00rem; top: 0.00rem;">
                    <p class="paragraph"
                        style="width: 5.61rem; height: 1.63rem; z-index: -10; font-size: 1.00rem; left: 0.60rem; top: 0.00rem; text-align: left; font-family: 'pro', serif; color: #ffffff; font-weight: 600;">
                        <span class="position style" style="width: 1.66rem; height: 1.29rem; left: 0.00rem; top: 0.37rem;">SECTION E</span>
                    </p>
                </div>
            </div>
            <p class="paragraph body-text"
                style="width: 53.62rem; height: 1.70rem; left: 3.63rem; top: 13.58rem; text-align: left; color: #6d6e71; font-weight: 300;">
                <span class="position style"
                    style="width: 0.45rem; height: 1.04rem; left: 0.00rem; top: 0.68rem;">Tax Identification Number (MIRA)</span>
                
                <span class="position style" style="width: 0.45rem; height: 1.04rem; left: 28.62rem; top: 0.68rem;">
                    Tax Identification Number</span>
                
            </p>
            <svg viewbox="0.000000, -0.123500, 126.700000, 1.000000" class="graphic"
                style="width: 12.67rem; height: 0.10rem; display: block; z-index: 10; left: 15.87rem; top: 15.38rem;">
                <path stroke-width="0.247000" fill="none" d="M 0 0 L 126.7 0" stroke="#a7a9ac"
                    stroke-opacity="1.000000" />
            </svg>
            <svg viewbox="0.000000, -0.123500, 126.700000, 1.000000" class="graphic"
                style="width: 12.67rem; height: 0.10rem; display: block; z-index: 10; left: 43.20rem; top: 15.38rem;">
                <path stroke-width="0.247000" fill="none" d="M 0 0 L 126.7 0" stroke="#a7a9ac"
                    stroke-opacity="1.000000" />
            </svg>
            <p class="paragraph body-text"
                style="width: 9.44rem; height: 1.60rem; font-size: 0.60rem; left: 32.25rem; top: 15.27rem; text-align: left; color: #6d6e71; font-weight: 300;">
                <span class="position style"
                    style="width: 1.68rem; height: 0.74rem; left: 0.00rem; top: 0.02rem;">(Applicable in any other country must<br>
be declared under CRS )</span>
                
            </p>
            <div class="group" style="width: 52.30rem; height: 0.10rem; display: block; left: 3.63rem; top: 17.31rem;">
                <svg viewbox="0.000000, -0.500000, 523.050000, 1.000000" class="graphic"
                    style="width: 52.30rem; height: 0.10rem; display: block; z-index: -10; left: 0.00rem; top: -0.00rem;">
                    <path stroke-width="1.000000" fill="none" d="M 0 0 L 523.05 0" stroke="#bcbec0"
                        stroke-opacity="1.000000" />
                </svg>
            </div>
            <div class="group" style="width: 1.33rem; height: 1.15rem; display: block; left: 3.63rem; top: 17.80rem;">
                <svg viewbox="0.000000, 0.000000, 13.100000, 11.250000" class="graphic"
                    style="width: 1.31rem; height: 1.12rem; display: block; z-index: -10; left: 0.01rem; top: 0.01rem;">
                    <path stroke-width="0.250000" fill="none" d="M 0 0 L 13.067 0 L 13.067 11.25 L 0 11.25 L 0 0 Z"
                        stroke="#939598" stroke-opacity="1.000000" />
                </svg>
                <div class="textbox"
                    style="width: 1.33rem; height: 1.15rem; display: block; z-index: -10; left: 0.00rem; top: 0.00rem;">
                    <p class="paragraph body-text"
                        style="width: 0.98rem; height: 1.11rem; z-index: -10; font-size: 0.80rem; left: 0.35rem; top: 0.00rem; text-align: left; font-family: 'pro', serif; font-weight: 400;">
                        <span class="position style"
                            style="width: 0.80rem; height: 0.80rem; left: 0.00rem; top: 0.20rem;">✔</span>
                    </p>
                </div>
            </div>
            <p class="paragraph body-text"
                style="width: 50.65rem; height: 1.32rem; font-size: 0.80rem; left: 6.59rem; top: 17.46rem; text-align: left; font-family: 'pro', serif; color: #58595b; font-weight: 400;">
                <span class="position style"
                    style="width: 1.46rem; height: 0.96rem; left: 0.00rem; top: 0.36rem;">I/we hereby agree</span>
                
            </p>
            <svg viewbox="0.000000, 0.000000, 3.750000, 3.750000" class="graphic"
                style="width: 0.38rem; height: 0.38rem; display: block; z-index: 10; left: 7.65rem; top: 20.07rem;">
                <path fill="#58595b" fill-opacity="1.000000" d="M 3.75 0 L 0 0 L 0 3.75 L 3.75 3.75 L 3.75 0 Z"
                    stroke="none" />
            </svg>
            <p class="paragraph body-text"
                style="width: 48.59rem; height: 0.93rem; font-size: 0.80rem; left: 8.67rem; top: 19.90rem; text-align: left; font-family: 'pro', serif; color: #58595b; font-weight: 300;">
                <span class="position style"
                    style="width: 0.17rem; height: 0.95rem; left: 0.00rem; top: 0.01rem;">I am / we are not registered as a tax resident in a foreign jurisdiction;</span>
                
            </p>
            <svg viewbox="0.000000, 0.000000, 3.750000, 3.750000" class="graphic"
                style="width: 0.38rem; height: 0.38rem; display: block; z-index: 10; left: 7.65rem; top: 21.07rem;">
                <path fill="#58595b" fill-opacity="1.000000" d="M 3.75 0 L 0 0 L 0 3.75 L 3.75 3.75 L 3.75 0 Z"
                    stroke="none" />
            </svg>
            <p class="paragraph body-text"
                style="width: 48.59rem; height: 0.93rem; font-size: 0.80rem; left: 8.67rem; top: 20.83rem; text-align: left; font-family: 'pro', serif; color: #58595b; font-weight: 300;">
                <span class="position style"
                    style="width: 0.17rem; height: 0.95rem; left: 0.00rem; top: -0.02rem;">It is my/ our sole responsibility to inform the Bank if I/ we get registered as a tax resident of any foreign jurisdiction, at any time in the future.</span>
               
            </p>
            <p class="paragraph body-text"
                style="width: 53.64rem; height: 1.07rem; left: 3.61rem; top: 23.89rem; text-align: left; font-family: 'pro', serif; color: #58595b; font-weight: 400;">
                <span class="position style"
                    style="width: 2.26rem; height: 1.02rem; left: 0.00rem; top: 0.03rem;">Signature</span>
                
                <span class="position" style="width: 15.65rem; height: 1.02rem; left: 3.98rem; top: 0.03rem;">
                    <span class="style"> </span>
                    <span class="style" style="text-decoration: underline;"> </span>
                </span>
                <span class="position" style="width: 0.51rem; height: 1.04rem; left: 38.10rem; top: 0.02rem;">
                    <span class="style"> </span>
                    <span class="style" style="font-family: 'pro', serif; color: #6d6e71; font-weight: 300;">D</span>
                </span>
                <span class="position style"
                    style="font-weight: 300; width: 1.07rem; height: 1.04rem; left: 38.61rem; top: 0.02rem; font-family: 'pro', serif; color: #6d6e71;">ate</span>
                <span class="position" style="width: 12.15rem; height: 1.04rem; left: 40.23rem; top: 0.02rem;">
                    <span class="style" style="font-family: 'pro', serif; color: #6d6e71; font-weight: 300;"> </span>
                    <span class="style"
                        style="font-family: 'pro', serif; color: #6d6e71; font-weight: 300; text-decoration: underline;">
                    </span>
                </span>
            </p>
        </div>
        <div class="page" style="background: #ffffff; width: 59.50rem; height: 84.10rem; z-index: 0;">
            <svg viewbox="0.000000, -0.125000, 1.000000, 1.000000" class="graphic"
                style="width: 0.10rem; height: 0.10rem; display: block; z-index: 10; left: 7.72rem; top: 2.60rem;">
                <path stroke-width="0.250000" fill="none" d="M 0 0 L 1 0" stroke="#58595b" stroke-opacity="1.000000" />
            </svg>
            <svg viewbox="0.000000, -0.125000, 1.000000, 1.000000" class="graphic"
                style="width: 0.10rem; height: 0.10rem; display: block; z-index: 10; left: 34.33rem; top: 2.60rem;">
                <path stroke-width="0.250000" fill="none" d="M 0 0 L 1 0" stroke="#58595b" stroke-opacity="1.000000" />
            </svg>
            <p class="paragraph body-text"
                style="width: 52.97rem; height: 1.40rem; font-size: 0.75rem; left: 3.83rem; top: 1.40rem; text-align: left; font-family: 'pro', serif; color: #58595b; font-weight: 600;">
                <span class="position style"
                    style="width: 1.25rem; height: 0.96rem; left: 0.00rem; top: 0.44rem; transform: ScaleX(1.05);">Full Name : <?= filter_var($record['employee_name'], FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?></span>
                
                <span class="position style"
                    style="width: 17.17rem; height: 0.96rem; left: 4.19rem; top: 0.44rem; transform: ScaleX(1.05);">
                </span>
                <span class="position" style="width: 0.75rem; height: 0.96rem; left: 28.04rem; top: 0.44rem;">
                    <span class="style" style="color:black; text-decoration: bold;"> </span>
                    <span class="style"> </span>
                    <span class="style" style="transform: ScaleX(1.05);">ID No: <?= filter_var($record['passport_nic_no'], FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?></span>
                </span>
               
                <span class="position" style="width: 11.07rem; height: 0.96rem; left: 30.81rem; top: 0.44rem;">
                    <span class="style" style="transform: ScaleX(1.05);"> </span>
                    <span class="style" style="color:black; text-decoration: bold;"> </span>
                </span>
            </p>
            <div class="textbox"
                style="background: #006b4e; width: 52.70rem; height: 2.12rem; display: block; z-index: 0; left: 3.48rem; top: 4.12rem;">
                <p class="paragraph body-text"
                    style="width: 52.30rem; height: 1.63rem; font-size: 1.00rem; left: 0.40rem; top: 0.00rem; text-align: left; font-family: 'pro', serif; color: #ffffff; font-weight: 600;">
                    <span class="position style"
                        style="width: 6.29rem; height: 1.22rem; left: 0.00rem; top: 0.41rem;">DECLARATION</span>
                </p>
            </div>
            <p class="paragraph body-text"
                style="width: 53.33rem; height: 1.41rem; font-size: 0.75rem; left: 3.47rem; top: 6.37rem; text-align: left; font-family: 'pro', serif; color: #58595b; font-weight: 300;">
                <span class="position style"
                    style="width: 1.25rem; height: 0.89rem; left: 0.00rem; top: 0.53rem; transform: ScaleX(1.05);">This</span>
                <span class="position style"
                    style="width: 3.53rem; height: 0.89rem; left: 1.42rem; top: 0.53rem; transform: ScaleX(1.05);">
                    declaration</span>
                <span class="position style"
                    style="width: 0.46rem; height: 0.89rem; left: 5.12rem; top: 0.53rem; transform: ScaleX(1.05);">
                    is</span>
                <span class="position style"
                    style="width: 1.81rem; height: 0.89rem; left: 5.75rem; top: 0.53rem; transform: ScaleX(1.05);">
                    made</span>
                <span class="position style"
                    style="width: 0.66rem; height: 0.89rem; left: 7.74rem; top: 0.53rem; transform: ScaleX(1.05);">
                    to</span>
                <span class="position style"
                    style="width: 2.76rem; height: 0.89rem; left: 8.57rem; top: 0.53rem; transform: ScaleX(1.05);">
                    Maldives</span>
                <span class="position style"
                    style="width: 2.15rem; height: 0.89rem; left: 11.51rem; top: 0.53rem; transform: ScaleX(1.05);">
                    Islamic</span>
                <span class="position style"
                    style="width: 1.51rem; height: 0.89rem; left: 13.83rem; top: 0.53rem; transform: ScaleX(1.05);">
                    Bank</span>
                <span class="position style"
                    style="width: 0.91rem; height: 0.89rem; left: 15.51rem; top: 0.53rem; transform: ScaleX(1.05);">
                    Plc</span>
            </p>
            <p class="paragraph body-text"
                style="width: 53.34rem; height: 1.23rem; font-size: 0.75rem; left: 3.46rem; top: 7.78rem; text-align: left; font-family: 'pro', serif; color: #58595b; font-weight: 400;">
                <span class="position style"
                    style="width: 1.43rem; height: 0.95rem; left: 0.00rem; top: 0.29rem; transform: ScaleX(1.05);">I/we hereby agree</span>
            </p>
            <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACMAAAAeCAYAAACmPacqAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAAgElEQVRYhe3XsQ3CMBgF4TOyFNOGITISEoyFkFggkTJShgj178p0iIJXO8W7Cb720uP5ahykDHC/XadSyt4LERGXeVm3DFCG4X3uiKG1BHDqBviTMSpjVMaojFEZozJGZYzKGJUxKmNUxqiMUWWAqHUkpW7PHbWOX8y8rFsvyG8f3GgY0jFkPHIAAAAASUVORK5CYII="
                class="image"
                style="width: 1.31rem; height: 1.13rem; display: block; z-index: 0; left: 3.36rem; top: 9.71rem;" />
            <p class="paragraph body-text"
                style="width: 53.45rem; height: 2.09rem; font-size: 1.10rem; left: 3.35rem; top: 8.75rem; text-align: left; font-family: pro, serif;">
                <span class="position" style="width: 0.51rem; height: 0.95rem; left: 1.87rem; top: 0.93rem;">
                    <span class="style"></span>
                    <span class="style"
                        style="font-family: 'pro', serif; font-size: 1.00rem; transform: ScaleX(1.05);">
                    </span>
                    <span class="style"
                        style="font-family: 'pro', serif; font-size: 0.75rem; color: #58595b; transform: ScaleX(1.05);">General Terms</span>
                </span>
            </p>
            <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAoAAAAKCAYAAACNMs+9AAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAAGElEQVQYlWOMiIz+z0AEYCJG0ahC6ikEAFFSAh9YFx1pAAAAAElFTkSuQmCC"
                class="image"
                style="width: 0.37rem; height: 0.37rem; display: block; z-index: 0; left: 6.27rem; top: 11.75rem;" />
            <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAoAAAAKCAYAAACNMs+9AAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAAGElEQVQYlWOMiIz+z0AEYCJG0ahC6ikEAFFSAh9YFx1pAAAAAElFTkSuQmCC"
                class="image"
                style="width: 0.37rem; height: 0.37rem; display: block; z-index: 0; left: 6.27rem; top: 12.64rem;" />
            <p class="paragraph body-text"
                style="width: 41.55rem; height: 2.97rem; font-size: 1.10rem; left: 6.27rem; top: 10.31rem; text-align: left; font-family: pro, serif; font-weight: 300;">
                <span class="position" style="width: 0.37rem; height: 0.89rem; left: 1.03rem; top: 1.20rem;">
                    <span class="style"></span>
                    <span class="style"
                        style="font-family: 'pro', serif; font-size: 1.00rem; transform: ScaleX(1.05);">
                    </span>
                    <span class="style"
                        style="font-family: 'pro', serif; font-size: 0.75rem; color: #58595b; transform: ScaleX(1.05);">To having read, understood and expressly assent to be bound by the Bank’s Terms and Conditions as amended from time to time.</span>
                </span>
                
                <span class="position style"
                    style="width: 0.14rem; height: 0.89rem; font-size: 0.75rem; left: 41.35rem; top: 1.20rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">.</span>
                <span class="position" style="width: 0.37rem; height: 0.89rem; left: 1.03rem; top: 2.08rem;">
                    <span class="style"
                        style="font-family: 'pro', serif; font-size: 0.75rem; color: #58595b; transform: ScaleX(1.05);">
                    </span>
                    <span class="style" style="font-family: 'pro', serif; font-size: 0.75rem; color: #58595b;"></span>
                    <span class="style"
                        style="font-family: 'pro', serif; font-size: 0.75rem; color: #58595b; font-weight: 400; transform: ScaleX(1.05);">
                    </span>
                    <span class="style"
                        style="font-family: 'pro', serif; font-size: 0.75rem; color: #58595b; transform: ScaleX(1.05);">To be bound by the list of Bank charges amended from time to time.</span>
                </span>
                
                <span class="position style"
                    style="width: 0.14rem; height: 0.89rem; font-size: 0.75rem; left: 22.31rem; top: 2.08rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">.</span>
            </p>
            <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAoAAAAKCAYAAACNMs+9AAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAAGElEQVQYlWOMiIz+z0AEYCJG0ahC6ikEAFFSAh9YFx1pAAAAAElFTkSuQmCC"
                class="image"
                style="width: 0.37rem; height: 0.37rem; display: block; z-index: 0; left: 6.27rem; top: 13.52rem;" />
            <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAoAAAAKCAYAAACNMs+9AAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAAGElEQVQYlWOMiIz+z0AEYCJG0ahC6ikEAFFSAh9YFx1pAAAAAElFTkSuQmCC"
                class="image"
                style="width: 0.37rem; height: 0.37rem; display: block; z-index: 0; left: 6.27rem; top: 14.46rem;" />
            <p class="paragraph body-text"
                style="width: 25.58rem; height: 2.30rem; font-size: 1.10rem; left: 6.27rem; top: 12.76rem; text-align: left; font-family: pro, serif; font-weight: 300;">
                <span class="position" style="width: 0.46rem; height: 0.89rem; left: 1.03rem; top: 0.53rem;">
                    <span class="style"></span>
                    <span class="style"
                        style="font-family: 'pro', serif; font-size: 1.00rem; transform: ScaleX(1.05);">
                    </span>
                    <span class="style"
                        style="font-family: 'pro', serif; font-size: 0.75rem; color: #58595b; transform: ScaleX(1.05);">A copy of the current Terms and Conditions are available at www.mib.com.mv.</span>
                </span>
               
                
                <span class="position" style="width: 0.37rem; height: 0.89rem; left: 1.03rem; top: 1.41rem;">
                    <span class="style"
                        style="font-family: 'pro', serif; font-size: 0.75rem; color: #58595b; transform: ScaleX(1.05);">
                    </span>
                    <span class="style" style="font-family: 'pro', serif; font-size: 0.75rem; color: #58595b;"></span>
                    <span class="style"
                        style="font-family: 'pro', serif; font-size: 0.75rem; color: #58595b; font-weight: 400; transform: ScaleX(1.05);">
                    </span>
                    <span class="style"
                        style="font-family: 'pro', serif; font-size: 0.75rem; color: #58595b; transform: ScaleX(1.05);">The information that I have provided in this application is true and accurate.</span>
                </span>
                
                <span class="position style"
                    style="width: 0.38rem; height: 0.89rem; font-size: 0.75rem; left: 24.23rem; top: 1.41rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">e</span>
                <span class="position style"
                    style="width: 0.14rem; height: 0.89rem; font-size: 0.75rem; left: 24.60rem; top: 1.41rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">.</span>
            </p>
            <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACMAAAAeCAYAAACmPacqAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAAgElEQVRYhe3XsQ3CMBgF4TOyFNOGITISEoyFkFggkTJShgj178p0iIJXO8W7Cb720uP5ahykDHC/XadSyt4LERGXeVm3DFCG4X3uiKG1BHDqBviTMSpjVMaojFEZozJGZYzKGJUxKmNUxqiMUWWAqHUkpW7PHbWOX8y8rFsvyG8f3GgY0jFkPHIAAAAASUVORK5CYII="
                class="image"
                style="width: 1.31rem; height: 1.13rem; display: block; z-index: 0; left: 3.36rem; top: 15.75rem;" />
            <img src="data:image/png;base64,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"
                class="image"
                style="width: 40.60rem; height: 2.51rem; display: block; z-index: 10; left: 7.29rem; top: 17.68rem;" />
            <svg viewbox="0.000000, 0.000000, 3.700000, 3.700000" class="graphic"
                style="width: 0.37rem; height: 0.37rem; display: block; z-index: 10; left: 6.27rem; top: 18.72rem;">
                <path fill="#58595b" fill-opacity="1.000000" d="M 3.694 0 L 0 0 L 0 3.694 L 3.694 3.694 L 3.694 0 Z"
                    stroke="none" />
            </svg>
            <svg viewbox="0.000000, 0.000000, 3.700000, 3.700000" class="graphic"
                style="width: 0.37rem; height: 0.37rem; display: block; z-index: 10; left: 6.27rem; top: 17.80rem;">
                <path fill="#58595b" fill-opacity="1.000000" d="M 3.694 0 L 0 0 L 0 3.694 L 3.694 3.694 L 3.694 0 Z"
                    stroke="none" />
            </svg>
            
            
            
            
            
            
            
            <p class="paragraph body-text"
                style="width: 53.45rem; height: 2.08rem; font-size: 1.10rem; left: 3.35rem; top: 14.80rem; text-align: left; font-family: pro, serif;">
                <span class="position" style="width: 0.51rem; height: 0.95rem; left: 1.87rem; top: 0.92rem;">
                    <span class="style"></span>
                    <span class="style"
                        style="font-family: 'pro', serif; font-size: 1.00rem; transform: ScaleX(1.05);">
                    </span>
                    <span class="style"
                        style="font-family: 'pro', serif; font-size: 0.75rem; color: #58595b; transform: ScaleX(1.05);">General Investment Account (GIA)</span>
                </span>
                
            </p>
            <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACMAAAAeCAYAAACmPacqAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAAgElEQVRYhe3XsQ3CMBgF4TOyFNOGITISEoyFkFggkTJShgj178p0iIJXO8W7Cb720uP5ahykDHC/XadSyt4LERGXeVm3DFCG4X3uiKG1BHDqBviTMSpjVMaojFEZozJGZYzKGJUxKmNUxqiMUWWAqHUkpW7PHbWOX8y8rFsvyG8f3GgY0jFkPHIAAAAASUVORK5CYII="
                class="image"
                style="width: 1.31rem; height: 1.13rem; display: block; z-index: 0; left: 3.36rem; top: 20.88rem;" />
            <p class="paragraph body-text"
                style="width: 53.45rem; height: 1.40rem; font-size: 1.10rem; left: 3.35rem; top: 20.61rem; text-align: left; font-family: pro, serif;">
                <span class="position" style="width: 0.43rem; height: 0.95rem; left: 1.87rem; top: 0.37rem;">
                    <span class="style"></span>
                    <span class="style"
                        style="font-family: 'pro', serif; font-size: 1.00rem; transform: ScaleX(1.05);">
                    </span>
                    <span class="style"
                        style="font-family: 'pro', serif; font-size: 0.75rem; color: #58595b; transform: ScaleX(1.05);">K</span>
                </span>
                <span class="position style"
                    style="width: 1.97rem; height: 0.95rem; font-size: 0.75rem; left: 2.31rem; top: 0.37rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">aamiy</span>
                <span class="position style"
                    style="width: 1.64rem; height: 0.95rem; font-size: 0.75rem; left: 4.28rem; top: 0.37rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">aabu</span>
                <span class="position style"
                    style="width: 0.43rem; height: 0.95rem; font-size: 0.75rem; left: 6.09rem; top: 0.37rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    K</span>
                <span class="position style"
                    style="width: 0.94rem; height: 0.95rem; font-size: 0.75rem; left: 6.53rem; top: 0.37rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">ids</span>
                <span class="position style"
                    style="width: 0.48rem; height: 0.95rem; font-size: 0.75rem; left: 7.64rem; top: 0.37rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    A</span>
                <span class="position style"
                    style="width: 0.70rem; height: 0.95rem; font-size: 0.75rem; left: 8.11rem; top: 0.37rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">cc</span>
                <span class="position style"
                    style="width: 1.30rem; height: 0.95rem; font-size: 0.75rem; left: 8.81rem; top: 0.37rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">oun</span>
                <span class="position style"
                    style="width: 0.26rem; height: 0.95rem; font-size: 0.75rem; left: 10.11rem; top: 0.37rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">t</span>
            </p>
            <div class="group" style="width: 0.37rem; height: 0.37rem; display: block; left: 6.27rem; top: 23.05rem;">
                <svg viewbox="0.000000, 0.000000, 3.700000, 3.700000" class="graphic"
                    style="width: 0.37rem; height: 0.37rem; display: block; z-index: -10; left: 0.00rem; top: 0.00rem;">
                    <path fill="#58595b" fill-opacity="1.000000" d="M 3.694 0 L 0 0 L 0 3.694 L 3.694 3.694 L 3.694 0 Z"
                        stroke="none" />
                </svg>
            </div>
            <img src="data:image/png;base64,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"
                class="image"
                style="width: 42.72rem; height: 0.73rem; display: block; z-index: 0; left: 7.29rem; top: 22.93rem;" />
            <p class="paragraph body-text"
                style="width: 50.54rem; height: 0.73rem; font-size: 0.35rem; left: 6.27rem; top: 22.93rem; text-align: left; font-family: 'pro', serif;">
            </p>
            <svg viewbox="0.000000, 0.000000, 3.700000, 3.700000" class="graphic"
                style="width: 0.37rem; height: 0.37rem; display: block; z-index: 10; left: 6.27rem; top: 24.82rem;">
                <path fill="#58595b" fill-opacity="1.000000" d="M 3.694 0 L 0 0 L 0 3.694 L 3.694 3.694 L 3.694 0 Z"
                    stroke="none" />
            </svg>
            <svg viewbox="0.000000, 0.000000, 3.700000, 3.700000" class="graphic"
                style="width: 0.37rem; height: 0.37rem; display: block; z-index: 10; left: 6.27rem; top: 26.64rem;">
                <path fill="#58595b" fill-opacity="1.000000" d="M 3.694 0 L 0 0 L 0 3.694 L 3.694 3.694 L 3.694 0 Z"
                    stroke="none" />
            </svg>
            <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAABJQAAAAUCAYAAADbVWeOAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAgAElEQVR4nOx9dVxUy/v/nGWT2qW7QRosQkRRBLvw2p3X7q5rdwdiN4qK3S0praQC0rE0u2zn+f2BRw+Hs8ui3uv9/L73/Xrx0p0zZ+Y5M8888zwzzzwDwTAM/kMzhEKhxv4DBx9sWL+uz++m5UdRV1dnGR5+ff/ixQtH/25a/kP7sGfPvifz58+dqKmp2fC7afm/hBMnTl4OCRm23cTEJO930/JP4+7dexssrSzTu3Tu/PB30/J/FTAMQ8qeQxD03yT9H/4DDk6ePH1hyJBBe83MzD79blr+rWhoaDC7fj1iT0FhoZdUKiMvXbLoDxsbmzRV39++Y+frFcuXDaVSqby/k05lOHYs9PrYsaPXGhgYFP8uGv4vQigUarx6/WZOdlZ2n+qaajsAAJg5Y8ZsFxfnd7+ZtP85vHj5ah6VSuH27NHjsqI8z56/WKipodHg7989/J+k7T/g49Wr13NIJJIwIKDnxd9Rf2Njo8n5CxdPLF+2NORHy8jP/+L7/v37sZMnT1ryq+iKiY2bKODz6X37BocCAIBcLids2bothvirKvj/AXI5rPblS4Hv76bjZyCRSKhFxcWdkd8wDEOnz5w96+bm+qq7n9/130nbf1COgoICb6lMRvqn6isuLu505+69jdOnTZ3HYDCqfqSMb/zl6vq6e3e/a7+axn8CJSUlHUUikcbvpuN3oKq62p7BYDB/Nx3/l/Hk6dOld+/e26itrV2DfRYQEHBh2NAhu38HXf/hP/zbUVJa6ikUijR/Nx3/VrDYbKObtyK3zZ8/byIAAMjlcrX2lvHlS4EvDMOEX0+d6igqLuosFotpv5OG9uLSpStHLCzMswIDe5/53bT8CORyOeH+g4drx4wetWHwoIEHAACgurrajkwmC343bf+LqKurs1Kn0djK8tTW1NpIdCTUf4qm/6AcdfX1lhQymY/8zs3N6/702bMlM2dMn/1PbPxLpVJKYWFR158pg8/nMcorKlx+FU0AANDY0GDG4XD00Wl5efl+/ycWlGAYhmQyGYlIJIrbyKl0p/h/ETAMQ3Fx8eMpZDL/n1hQkkql5Lbb+T/gAQbgH+W/iopK5+TklJARISFb0QtKivpQLpcT5HK5GpFIlKDT4+Pfj6OQyfz/1QWl/1X8N9b+efxdbd6zR49Lv3IH6T/8u/DfWP0PbeHv4JG0tLQhaMOHQCDI/sn6fyVg+J/Vj34WCYmJo7hcjh56QUmRDvVvRGpq2jAdHZ1KdJqRkVEBXt5/O+/8rwD+m23Qf0M//S+NAQBaepCXlZe7JSYmjRwzZvQ6tFz9N7TrvwG/ddfhn8LhI0dvfc7N9f/ddPwOEAgE+cmwUOMJE8av+LvrehcVNS3ixs2df3c9/+HXoFs33xunT4XpW1tbfUTScj59Cjhy9NgNvPy79+x9Vlpa6olOgyAIDjtx3OSf4K//8B3JySkh585fCPsVZf3dSsz/L4iOiZl87dr1vb+bjv/wvwUul6ezfMWq/45k/QeFeP36zZ+3bkVu/dXl1tc3WKB32BWBz+fTly1fmfur6/+/jAP79znNmjVzJjpt69bt0dXV1fa/i6b2oKCw0ItGpXLayldbW2u9YeOmxH+Cpv91/NObxmj8Sp3xZ7Bj567XFZWVzr+bjh9B714B586cPqlnYmycj6R9+PBh0KlTp8//fbX+7+jn3zyUhEKhhkQiodJoNA52pY3PF2hTqRQugUCQKytMLBZTm5qaDAkEgkxXV7fiVxEJwzAkEAi01dXVcd0FhUKhBpfL1QMAACKJJGLQ6dXo55wmjkF765RKpWQWi2VMIBBkDAaD2da3txdyuVytsbHRFL36qaurW96eevh8Pp3P59PpdHo1iUQSKcqnqN0AaLttEQiFQk0ul6tLIpGE2tratXhxPbgcrh4s/3tdo9lstqFEIqFqamo2UKlUblv5RSKROgRBcjKZLFSWTy6XqzU0NJhBEEFOp2tXq7p6LhaLaU1NTQYAAKCmpibR0dH56eNDcrmc0NDQYA5BEMxgMJhqampSZflZLJaxVColAwCAhoZGI41G4wDQ3LeNjY2mRCJRrKWlVYftMwKBINPS0qpHpwkEAm2pVEbGq6dJwThSxjtcLldXKBRqUqk0jqamRqOy7wAAAA6HoycSiTRIJJKQTqe3OgKkDAKBQEsqlZJpNFoTtv/4fD6dRqM1tTcejVwuJwiFIk11dVpTe97DK6exkWUKw/Jv44PBYDDx+EwkEqlzOBx9IpEoptPp1Xg083g8nR85viCRSChsNttITU1Nit19xAOPx2PQaDSOsp1tBM0yjWVCJKqJ29t3WMAwDNXX11sgvxkMRhUyLyFjVV1dnd2W3GqvfCOSSCK6tnYNrnzj8nTlCo5+IHIJgJZj8O8ADMMQj8dnaGios9riZy6XqwsAAMrcs2EYhlgslrEMddxWW1u7Fu9oAzIvqqmpSel0erUqfNFeSKVSEovFMoEgglxHh1GpbE6Uy+UEHo+nQyAQZBoaGizMMzWRSKTeVl+g+w7LU42NjSYQgSBXxBMAfOcd5DdWdslkUpKAz6e3/eWqQSwW00QikTqFQuFh5zWhUKhJIpGEbc0ZeFBFRspkMmJjY6MpgUCQ0en0alXq4fF4DIFAoI0ewwi4XJ6OUCjQQn5j5wmsHNDV1a1QxHNisZjG4XD1GAx6lTK6cGTLNzmMfJ+6ugarLZmvqmwRCARaPB5PR6nuxOXq/YixKZVKSWKxhPaz85NMJiPy+TxGW/n4fIG2SCTUpNPpVarYAwAA0JbupagePp/HoFAoPERHUbTxoeo4R/qBTCYLtLW1a9uiQS6XqwkEAi2sXEGjsbHRVCaTEQEAQFtbuwb9rXh90sRparctggWiI6ky/yF2HZVK5WJtBLFYTIVhmEChUHAXHBvqG8zNTE3bXAgXSyRUkVCo8rFTGIYhNptthOisACieb5D8LBbLGAAAMRgMpiL5hNhD2H5AwOPxGHK5XE1DQ6PxZ+w5oVCoQSQSxW3ZCN/nMQjW1dUtb289TU1NBmKxmKaKLFIFP6IzwjAMNTQ0mAPQzN/K7Ewkf1u6iSI7oi3IZDIii8UyIZHJAm0trTrsc7FYTBWJRBoUCoWP5SWRSKSupqYm+ZE+Q8tlNTU1KVaX4vH4DJlcrvS0V3vsCJSNW99W3u80NM+xAABApVK5yvQ9FotlTCQSxcrytKXTKAJx3foNqX/OmjnzeOiJcKlURp4xfdocd3e3VwAAUFhY2DU6OmZKJZPpqKmp2eDu5vaye3e/cOxgLSws6nL58pUjHC5XT1dXp0IkFGm4urq8oVAoPBMTkzwfH+9IAJp3eCsqKp3HjR2zFkvIrcjbW/T0dMsCe/c+i6RJpVJyekZGv8TEpJGsRpapoaFBYZeuXe57eng8QwSCQCDQ2rptRxSVSuGSiCQRl8fVtbCwyJw7Z/bUd1FR0x4/frq8pqbG9tSpM+eRBYg1q1cO0NPTK8PSgCzuVFYyHU+fOXuWTCIJRWKRel1dnZWbm9urKZMnL0IYYufO3S9HjRq50cHBPgFbztu372aUl1e4Tpo0YRleoz999mzxq1dv5mhra9VCoHnQMauqHFauWD7E1tYmVVmHiUQi9cjI21tS09KGkskUPpVK5VZXV9vNnz93opur6xu8d06dPnPO18f7lqen5zMkTSgUaqampQ1JTU0bxuVw9czNzbK9vb1uOzk5xSB5qqur7a5evXZg4MABB69fj9iDKINGxkZfevj7X0EC81VX19juP3DwAYfD0ZfJZKSMzMy+AAAwdOiQ3T38u1/Fo2nX7j3PJ04Yv9zCwiIL+2zp0uX5u3fv9EQmuouXLh/t3KnTo0+fPgVkZmUH0WhUTk1NrQ2NRuWMHzdulYeH+wtsGampaUNv3ry1HQYwJBFLqBQqlTtm9MgNnTp1eozOt2PnrleTJ09afOPGzZ18Hp8BAAAUCoXn5d31Tq+AgPPKJh6xWEzdtXvPc5lURqJSqVy+QKDNYNCrVixfNgwAAGpqamxOnDh5ZfPmv1p5x2VmZgXFxMZOmjd3zhQkDYZhiM1iGYeFnbwsk8mIcpmcWFVdbW9ra5MyY/q0OejFqtTUtKFfCgq8DQz0i1+/fjObQm5uK7FETJs7d85kurZ2zdWr1w7U1NTYAgCAoaFhYd++wcfR/MXl8nS2btsWvXfPbncYhqFVq9dmCgQCbS6Xq7ty1ZosAADo5utzQ0NDo/HV6zdzqqur7Y8cPX4DEdab/trYQ1NTo/H06bNnvb29bnfs6PkUTZuxkdGXl69ezSOTmvPbO9gndO/uF25laZmBbgsYhqE3b97OevzkyXIajdakoa7BYrFZxlOnTFl45+7djUsWLx6paDGKz+fTDxw8dO+PESO2nDlz9gwMALRk8cJR1tbWH2AYhj5/zu0RFx8/vqa6xo7OoFd16tjxsZdX17ttTYwCgUArMSlpZPrHjAE8Po9haWmZ4evjc9Pe3i4J6b+Xr17NW7Z0yQi89w8fOXorOCjohKury9uY2LiJ9+7dX48e77V1dVbTpk5Z0Llzp0cANCuJh48cjZwwftzKy5evHkaMJj19vdIe/v5XEB7ncDh6W7ftiOLxeDoikUgD6afgoD5hSIA8PJSUlnpcvHjpeBO7yVBXT7dcIpFSnJ0co/HyymQy4oMHD9dER8dMoanTmpqaOAZmZqafpk6ZvBAvcPnnz597RNy4tZPFYpkwGAwmh8PRNzU1+YwEEjxy9NiNfv36HnNydIzFvrtu/caUlSuWDUF4+86duxtNTU0/f/r8OaCkpNSTAEFyAAAgkUnCRQsXjGGx2cbXr0fsQcaqlZXVx6FDB+/GLuQKhUKNDx8+Dk5JSR3O4XD0zczMcprlm2MMoujU1tZZXbx46fiQoYP3XAu/vg+Rb4aGhoU9evhfdnV1eQtA80UHe/buf8LhcPSlUik5OzsnEAAABg8auD8goOfFz7m5/keOHLtpYW6eDQAA1TXVdpMmTlzatWuX+4r640fA5fJ0roaHH8jJ+dRbU1OzvqmpydDN1fX1pEkTlqKNntTUtKFFxcWd1QgE6buo6Gk0GpWze9fOjnhlZmRk9r1yNfyghro6C+E5Nptt1K9f32Noflq9em3GkqWL/zh37vxJuaxZedLS0qrz8+t2HZnfq6ur7Y4cPXZz+7at3nhG/6HDRyL79Ak85eHu/rL1t3F19+7d/3j+/LkTT506c16NqCaRiCXU2rpaa2dn56ipU6YsQMuAFy9ezieSiKKiouIumZlZwWampp9Wrlw+BIBmYzQuLm5CVlZ2kFAk1LS2svrg59ftupWVVTryfs6nTwFJiUkjHZ0cYx4+fLyKSqHwAABAKBJq/jlr5kxjY+P88GvX91WUN8ce0NfXLwns0/s0moeFQqFGWNipyzW1tTbonXw2m2104MA+JwAAOHrseERJcUlHLo+ng4xVczOznMWLF44WCoWa69ZvSD14YL8jtj1KSks9Iq7f2L169cqBSNrGvzYlzJn957Rjx0OvC4UizVGj/tiIHGOvqKhwfvPm3azyinJXKpXKcXF2fhcQ0PNiW5suMAxDnz59DoiPjx9XU1Nry2AwmJ06d3zUtUuX+2gZuWHjpsT58+dOvHTp8lGxSKwOAAA0dRrbx8c7soe//xWs8SAUCjVv3Li1Iz0jvT+FQuVRKBReTU2N7ZLFC0d16NAhvra2zios7ORlsURMQ+YHqVRKpjPoVegApBE3bu5MTk4eoa+nXyqXy9Vq6+qsdmzf1hXNC3K5XO358xcLUtPShgqFIk0qlcL18faODAzsfQZN142bt7bb29klpaalDWEymY6ILKZQKLxFixaMqaurs4qIuLkLUcxtbGxShw0bsgu7+CAUCjVTUlKHpX34MITL4eqZW5hn+Xh7Rzo6dohD8lRWMh1v3rq1PTgo6MSNGzd3IrLF2MQ4r4e//xVnZ6doAABgMpkdDh46cofT1GQgl8vVPnz4OAgAAEKGD9vu59ctQlG/lZSUeF66dOVoY2OjKZlMFkikEkpQnz4n+/fvdwTRWdZv2JhcW1tnDQAASckpIwAAYPGihaPNzc1y0GUdDz0RXlRY1IXPF9ARHjU1McldunTxH+hvfvb8xaLMjMy+UpmMRKdrV/fw97/i7e11B0tbfX2D+avXr+cUFxV3JqgRpI4dOsQFBPS8oIpBkpycEhIZeXsLRIDk2lratXwBnz540KB9eHn5fIF2TEzM5OycnECRSKxuY2Od5tet23VLS4tMJM+HDx8GZWfnBLq7u728eStym4a6BovD5erxeDydgJ49LoaEDN+OlVXV1dV2Fy9ePlbJZDrSaLQmgUCg7d/dLzwkZPg2tEEaFx8/7tq16/vMTJsDwjOrmB2WL1s63Nra+gMAAFy9Gn7A1tY22c+vW8TDR49XRkfHTKmpqbXZt//gQxKJJAQAgB3bt3qpEpPo7t17G/T19Us4XK5ebGzcRE0Njcb6+noLiADJR4SEbMXyyrr1G1Lnzpk95eix0AiJREKdPGniEkTXyMrK7nPt+vW9YrGYBsthAkFNTToiZPi2bt18bwAAQBOHo79t24539fX1Ftk52YEPHj5ajS5bIBBo79u725VGo3H27Nn3pKqqyqG2rs4K4R0HB/v3f86aOQvvO7KyswMvX75yRJ2mzv423zQ1GfYJ7H164MABh9B5KyoqnK9evXagklnpxGAwmCKRWB0AGNq+basX0g9isZgWefvO5tSU1GEkMllAo1E51dU1dnPm/DnVw939ZXFxScfnL14sdHZ2irp9+85mACB4x/ZtXWk0KmfJ0mUFx44escLSyGQyO5w9d/7Uxg3re2P54tXrN7NLS8s8yGSSwNnJKTogoOcF7IJjdXW13fkLF0/UVNfY6unrlcIwTDAxNs6j0VRYFPpqg965c3djalraUHV1dXZtbZ01mUwSjBs7Zg3afpHL5YRFi5cWHz92xLLVN1RVOZw5c+7MXxvX9/oRnfFE2MlLgYG9z7x+9XpOXX29JQQgmEgkijt26vg4OKhPGFZ/FggEWtevR+xJz8jor6mpWc9mNxk5OnaInTJ50mJEfj558nTp23dRM6qqqhwOHTpyBxkDW7ds6qZsMTg9PaNf5O3bW7gcrh5Dh8FkNbJMHJ0cY+bM/nM6AADs3rP36dgxo9edO38hjMPh6g8aOOBAcHBQWHNf1Ni+fvNmdmlpqQeJRBI6OTnG9AoION+6z2psm/us2g7pM2Nj43wNjZZ2R319vcXhI0dvbdu6xVcoFGpu/GtzAp/PYwgEQi2kXXv16nl+0MCBB5F2UWZHIJDJZMQ7d+7+FR//fpyWtnYtlULhiSUSasjwoTsUtQuaps1btsUYGxt9gQAENzQ2mHX387sWEjJ8OzpfRUWF89Onz5aUlpW7AwCAna1tckBAj4uIvAKgWac5EXbySm1tnbUinUYp5s1fUH723PkwDoerA8MwQP4ePny0gsViGaLTYBgGkbfv/CWRSEjI78ysrMCly5bnlpSUuiNpcrkcuhp+be/6DX8lxsXFj0XSn794Oe/M2XMnsWXCMAwuXrx85PHjJ0vRZYRfu75HLpdD6HxSqZQYGXl7E/L70ePHyw4fOXoD/V5tba0l+p0tW7ZFZWZlBeLVi/7jcrmMKVOn865cuXqgqqraFkmXSCTky5evHly1ak2GRCIhwzAMHjx8tDLs5KnzeOWs37AxKTs7p5eievLy8n1lMpkaOi0i4saOBw8frVRGH4fD1VmxcnXWvXv314pEIiqS3tTUpMflchkwDIPKysoOi5cs+4J+b9/+A/eTkpJDkN8ikYh28+atrdjyWSyW4dNnzxciv2tqaqyXr1iZExZ26gKPx9dG571589ZWdJ8jPHP58tWDbbUzDMNg1eq16UVFRZ3wnk2aPFUoEAg0kN8nT50+Fxoadvnlq9ez0fyQn//Fe978BeUJiYl/oN9/8PDRyo0bN72vr683Q9JKS0vdFi1eWvD8xct56Lxr121ICQ0Nu1xYWNgZnZ6bl9etrf549y5q6o6du16gaULzXiWT6bBo8dICvHdTU9MG79699wk6bcbMPxvOX7h4HE2LTCZTe/T48bJ58xeUNzU16SHpSUnJIVu3bX976dLlw+j6P3361OP48dCrR48dv9bQ0GCKLufosePXBAKBJppvZsyc1YimISU1dcjuPfse49G8es26DwUFBV2x6fv3H7yXmJg0Ak3btm073ly6dPkwls9Pnjp9jsPh6KLTLl++evDgocORaB7j8fja+/cfvDd33oIKNM3YP5FIRF20eElhWNipCyKRiIZ+divy9mY0H6F5F91mq1atyUDzIo/Ho9+5e2899r2qqmrbd++ipsJws0yYPXtudU1NjTU2X01NrdW8+QvLpFIpEYZhUFBQ0FUsFlPQeZ49e74APVb4fL7W/AWLSk+eOn2O3dSkj+Xn3NxcP3Ta27fvpp8IO3lRlbH26fNn/0WLlxbk5+f7oNMfPny0YsmSZfmvX7+ZhaTJ5XJo5649z86dvxAqFArVEd5JSEgcOWvW7DrsOHn67PnCtevWp5aWlbkibSqXyyG0/Ny6bfvbzMysPni0zZk7v7Kurt4c+X094sbO3bv3Pnnz5u0MdL4nT58ujrhxc/ux46HhaNnHZrMNQk+EXUL3p0gkouLJNw6Ho/v4ydMlyO/6+nqzZctWfG6Wbzw6Om9k5O1NRUXFHdFpj588XXLh4qWj2HLXrd+QnJScPBz5LRAINNBjta2/R48fL7t06fJhZXk4HI7uosVLC169ev0nwlcikYgaGXl706LFSwrR9Ccnpwzbu3f/w6vh1/a2VXdJSak7VranpKYOOXjocCQ6DZFNWH6PiYmdgJa/Gzb+lZCWljYIb0zMmTuficyf2D+BQKA5e868qkuXrxxiMpn2SLpEIiFdj7ixc+my5bno8f348ZOl+/YfuI+V542NjcYPHz1eji2/qKioE3oOzM7O6fXXps1xZ86eO4mWUcXFxZ779x+8FxZ26gKah+VyORR6IuwSemzKZDJCXl6+L7auJUuX59XU1Fohv1ksluGsWbPrsPn4fL7WlKnTeXjtUVBQ0HXN2vVp2HLPnD13Ej2vwTAMXr9+M6uysrIDtoyHDx+twPYt9u/mzVtbsXITSUePqSVLluWfOnXmbFlZuQs638eP6f1evno9G53GYrEMly1f+enx4ydL0XKPxWIZ8vl8LRhunnsqKiod0e+JRCLajJmzGpH+qK2ttZw5a3Y9otsgaeh31qxdn3b8eOhVrGzLzs7phdYnYbh5ntm9Z9/jmJjYCej0e/cfrLkVeXtzaGjYZTR/NjQ0mJ46deYsOq9QKFRH659ovkPzIpPJtF+1ak1GWNipC8g3I38RETd2YNvx3r37a8OvXd+jrK+Qv/T0jL6LFi8pLCgo6Ir0EYvFMty2bccb7Jxw81bkFjxZiP3D0wWQvylTp/MuXLx0FD0eYBgGL1+9np324cNALD/g6dpv372bhjdXov+ePXu+YMPGvxLQ/C2RSEinTp85M3v23OrS0lI3JL2urt786dNni7Bl5OXl+6LlT0pq6pCdu/Y8O3vufBi6Hzgcju6OnbtehIaGXUa/X1RU1Gne/IVlGRmZQUjbcrlcxuHDR2/u3LX7OXpMzJg5q7G0rMwVzd/ouSks7NQF7Dy2dNny3PLycmdV+rkFz9y4uf3osePX7ty9tx4tr8rLy52XLFmWj5WD8+YvKD9/4eJxrC7x+vWbWatWr01H9yWTybRfsWJVNlbnOXY8NDw6OmYSDj9w0XKlvKLCacmSZfmqfEdpWZkrdq79+DG93969+x+i01JSU4csXbY8NyfnU090m1dVVdkh/+fxePRVq9Zk3LlzdwNahnE4HF3Eni0uKfHYum3727CTp87LZDICkkcqlRLHjZ8ox6OxrKzcZdmyFZ/RaXFx8WPx7JWnz54vRNvJpWVlrgsXLSlKT8/oi6Y7Kip6ypKly/Pu3Lm7QVn7XLp0+fDeffsfPHr8eBn6/aKiok7zFywqjY2LG4ekyWQywthxE2C8csrLy52XLlue22IMtkNn3H/g4N0DBw/dSc/ICEan19TUWmF1FYFAoLF8xcqcR48fL0PWBiQSCfnho8fL585bUIFdR1ixcnVWcUmJhyp03L59Z+NfmzbHofsdq19u3Ljp/YmwkxcrmUwH9Lvx8e/H4Nkrz5+/mN/IYhmh22rhoiVFHz+m90O3eXR0zKSly5bnouV9TU2N9fwFi0rR5cXExE44cvRYBLYeVewI5Hv27tv/4Ny58yfQ82VdXb35nr37Hs2dt6BCWRudOXvu5I2bt7ah+EINrVN//Pix/9p1G1IuXb5yCKt/hZ08dV4lnWbJsny0TnP//oPVV6+G78PyIkEkEqs7ONi/R+/41NbWWbHYbGO8HQUalcpJT88YAEDz6vC5s+dPLVm8eCR6VwCCIHj0qJEby8rK3CEC9EOuhQmJiaNsbKxTsTtfampq0vLyClfkmJFcDqsZGhgUoevW19cv/ZE6AWj2ALK2tk4zMjIsRNKIRKJ40qQJy3T19Mpev37zJwAA9OzhfyklJSWEj3Flr6iocObx+AxnZ6coRXU4ONgnYHdF6HR6NbOS2WqnEo3rERF7evh3vzJs2NBdaC8xLS2temVuuVg8e/Z8UZcunR9g0+l0ek1aWtoQqVT67ehDRUWlc1BQnzCsq56Xl9edqKjoaarW+TOAAASXlpW5B/UJPIXmB3t7u6SFC+aPCw+/vl8ikVAAaF6Zf/78xcJ169YEo49dWlhYZK1buyb41q3IbSw22whdvo4OoxJ7ja6DvX1Cenp6f2V0yeVyNUMDgyI0TT/DewAAQNfWrkbTQiAQZIMGDjzo1bXr3fv3H6xD583Nzes+cuQfm9D1Ozk5xRQWFXext7dLRB9pIhAIMhdn53fpGRn9foY+VZGblzbrt9kAACAASURBVNd91KiRG7F87tihQ9yHj+nfdt0LCgq9MjIy+i1auGAsmsfU1WlNvXoFnEeOVyiqB4IguK6u3tLHxysSvdNXXFzcSU1NTYJ31bFUKiUXFBR4Kyrz/oOHa3x9fW5i042MDAtjYmMnyeVyApFIFPv38L8SHRM7GZsvOiZmSq+AnheQYxe2trYp2B0dBoNRVclsOd7r6+stfH18bmLdeX19fG6+efMOd8evLcjlcsKlS1eOzpn95zR7e/sWcQ4GDRp4QCAUaqHToqKjpxKJauJpU6csQDwECQSCzMfHO3L8hHErL166fAz+uovGrKpyePLk6bK/Nm4IsDA3z0b4EIIgGC0/24u6+nrL3r17nUOnBfQMuHD//oN1wUFBJ9CyT1tbu5ZIJIrRR1hevHw13/OrtxwampqaDenpGf0RWQEAAJVMpmNgYK8z2KMD3t5et99FRU1XhV7sHESlUnnYo6RtoaCw0OvBg4ersX/IsZHrETd2B/budbZPn8DTCF+RyWThH3+M2OLm5vbqzp27fyFlQRAEZ2ZlBY8eNXJjW/VaWlpkYmU7XZtezWRi5yIYsrSwyMBe2+3r63Pr7dt33+KE9AkMPP36zds/sfXExsZO6tnD/5KSwJUwm802srSwyDQ2Nv6CJBKJRMnYMaPXWVhYZD579nzRt9wQBJeVlbkHB/VpERPi3v0H67r7dWt1OYClpWXGi5cv56PT8vLy/UKGt/RQsLKySmc3NRnqG+iXoHkYgiC4c6dOj1JSUoYjaQQCQY7noUyn47XfzwOCAGxkZFSAnte4XK5uQWGhF57noI6uTkVCQsIYReUVFBR6fT0218pDQiQSaRQXF3dCpxkaGhRivVvc3d1eJiYmjkKnXb0afrBvcFDowIEDDqHlHp1Or0F2orW0tOpNTU1axOwhk8kCuRwmfNftmo+noHUbvPlVV0+vDCvbnJ2dotLSPgzB5mWz2UbYK7kDAnpeuHPn7l8DBvQ7jOZPHR2dSolETEXoAQCAJ0+fLe3SpbXnIYPBqEpJTglBjj8BAEBpWZl7//59j2J337t27XovOiZmCrYMVSAWi2nnL1w8sWzZ0uG2trYpiMyl0+k1q1atGPT58+eeOZ8+BfxI2cpgg9GJAQDAv7tf+KuXr+civ2UyGTE6JmaKq0uzZycalhYWmS9evpqnqHwWm2304OHDNatXrRyI5m8ikSgZNnToLnZTkyE6/73799f7+XVrdcmMjY116tOnz79dbgABCM7IyOg3ZPCgveh+0NTUbFixfNnQ7JzswPz8L74ANPPb2XPnT83+c9YMd3e3V0jbamhosBYunD+Ox+PpxMe/H4eUgeh/yG86nY571OpXAIIgODs7J3D4sKE70fLKzMzs08qVKwZH3orcyuXydJB0mUxOtLSwyEDrEo2Njaa379zZtH792j7ovjQ2Nv6yfsO6wCdPni6rrq62+zvoR2Bhbp6NnWsZDHoLXYjH4zEuXrx8bM3qVf2dnZ2i0fotOih4RMSNXb7dfG+EhAzfjpZhmpqaDYg9CwEI/vw5t8fXdvshW1QkEql/TE8fgPbkQGBsZPQlOjpmKgDN3p5Xr4YfHD9u7CoPD/cXaLp79uxxSdXb8SorKp0HDRx4EP2+tbX1hyWLF428di1i7z912yGJSBJhvYkNDPRLKiorndHHFW/fvrO5S5cu9wcNHHgQ8RwjEoniwYMGHuju1+3ajZu32vSywUNRUVHnuLj4CRvWrwtE93sr/RKCYAadXoWOayQSidQ/fPg4yNbWNgVbrrGxcT7adr169dqBcWPHrPH09HiObvMePfyvUCit7QdV8eDho9Vt2REAAJCSmjpMJBRpTps2dT56vtTT0y3v3KnZq1AZsHKIQCDI9PRaHrEsLS31HD582A6s/uXi4vwuLS1tCOpdfJ2GwahSRachAABDpiamn9GJsXFxE60sLdPxXtDS1q79mJ4+AAAAvnwp8DE0MixELyYhIJFIIgf71oSpirdv381U9D5NncbOzMoKAgAACAC4uqbGDj2R/yjgr7dI4B2hAgCAAQP6HY5/3zyh0On0Gnc395excfET0Hmio2OmYF2tVQGxjSM4fL5AOzkpeUT//v2OtqdcPMTExE7GE44AAADDMKGoqLhL8y8IplKpXFtbm1aDkkKl8ErLSj1+lhZV4ebqgnucz8nJKUZTU6Oh8OvVinFx8RMCAnpewHP1NzIyLHRzc32Vlpo2FEmDIAC74ChAEATBTU1NhtgFQ2ye2ro6a7SB+nOAIQ8Pj+d4TwYM6H84DqXMAAjA9nZ2Sbhn6GEYcuzQ+ngRRIDklRX/QDA8CMB2trbJeG6sFAqFV1r6nW9iY+Mm9u0bfBwv5oW7uxvuOGxRFQTBcrlczdy85dHJd1HR0+zsbJPw3mmWYc2L4nhISUkdjp6c0ODzBXRmVVUHAJoD9MXExE6GUXHQ5HI5ITYmdlLv3r3O4r2PgEgithrvEATByFEINH5mrBUWFnWFIEiOVy4EQXCHDg7xaPrfvYuePnjQoP148su/e/fw6uoau5qaWhsAAHj54uX8fv2Cj6kSy0xVQADA7u5urY5EQRCAAQCwg4P9e+wzAgTJK1B8HR0dPdXWprXMQlBQWOjVXCYEk8lkAdYYBQDh0zKV2hyCAIxdHGwvyGSyQEtLqw77B0EQLJPJiImJiaOCg4NO4L07eNDA/bGxcRO/EwRgA3394raOdSoCCYc3AYBgF9fWcpJIJIorKipcEB7y9fW5+flzbo+GhgYzJA8Mw1BMbNtjAgAAXF1dX+OlDxjQ/3BCYuLob9RAADY1Mf2M5lO5XK6WnZ0TiBfHjkAgyGpr66y/bSZAADY3M8vBKl5fCYYcO3T4YfmJ334/DwhAsKmpSQtdLTk5JcTE2LjVYhIAAGhradd++PhxIN4zAAB4FxU13c7OTrGMRC38Awi//wkEgry2ts4aMTBYbLZRVnZOYJ8+gadU/KwWQPMsBEEwn8+nf42fohCeOHMmBEFwI4tlIkTFdoEgAOPpdhAAMJlMFuDpRBBEaCFb4uLiJlhZ4evGUpmMVFLSfGkFBEGwuro628rq+4UXCLBzYHuQk/Opl7GxUT722DgAzTIkODg4tIUs+EXw8HBv1cYUCoVXgrqkIy8v349GpXLw5g4tLa26FvyEQXz8+3FeXl538OJ6GBoaFNFRcVElEgklLzevO14cJCKRKKmorHRCL64YGhoUGRoaFmHzkslkYVCfPicTk5JGAgBAWVmZm0QsoeLNPwQCQTZwwICDLdsWgv+OhWNFcHVxeYvXtqamJrn2DvYJWV9tIgCa+c/UtKVdl5iYNNKra9e7ePFnGHR6ta+vz83ExOa2+CeBtX3i49+P8/Rwf47XZwiEQqFG/PuEsQMH9D+kKA8AzWOeSCSKlZXVFj6mpw/QUxADSVtb65s9XFdXb1lZWenk7e11Gy8vVtfCAwwDyMMT3wawt7dL0tfXK83Nzeve3m9oLyAIgl1cnFvJewAAIBDUpJVfnR9gGIaiomOmKuqHgQMHHHr/PmEssnjSHjx7/mLRkKGD97Slx0AQgE0wmxMZGZn9dHR1cOM4a2lp1n38Oi/W19dblJaVuSPH9rHA0zdVRXJS8ghFdoRAINBG7Ig3r9/+OXDggIN4Y9tBBZ6BIAhmMpkdlOWztbFJwRv3quq5quo0RAAA0NFhtFDAmJVMx9TUtKEJCd8VOAQNjY1mRkaGBQAAUF5e7mph0Xox6RsRKq7IIoBRQfeYTKbjuXMXTuJ5J5SWlXpYfp1QAwN7n/nw8eOgZctX5gYF9TnZs4f/pZ8JCKssYJ+lhUVmVVW1A/K7d2CvM9euRewNDuoThhi2CYmJo7du2eyr9DthGMrPz+/27l309LLyMjcYBlB9fb1lp44dHyt6p7y8zM3c3Dxb1VVuRZBKpeSa2lqbAwcO3cN7XlJS6snhcPSR3xQKhadoZR8xLP8mQKj/wQaoFVgsLCwsMplMpqOjY4e4srIyt549elxWlNfO1ja5AmMUKDOIa2trrdGxN9Do1s03Ijk5JWTJ0uVfgvoEnurVK+C8KoGOEcA4QSb19fVwPZwMDQ0LBQIBvTnAdTO9VJriGzjU1Ai4QUlrav/WPvsGZbTVomgoryh3VTQBoyaSNhdnW8kwZlWHu3fvbXz+7MUibN6amhpb56/xv7Dgcrm69fX1lvv2HXiI97y6utoeGR9mZmaftLW1a3Lz8rojsVU+5+b2MDE1ycXupBcXF3d69y5q+peCQm8IAnBjI8sULTshCIIJBIJU0fj+0bFWyax0srS0aGV8IEDXB8MwVF5e7mpjY40bx41IJIqtra0+VDIrnYyMDAtLS8s8FPVd+9ByHCi6WYZAIMgUySKEp2QyGbGmptbm0KEjrWJ7AABAcXFx5xbyjUzmK/KAq62tUanNJ0+etPjwoSO33759NzM4KCisU6eOj9p7Ja6FuXkW1isLQUVlpZOenn6pogCsxsbG+RKplMLlcnURg4yBGQ/KwGQyO0RFx0zNzs4OhAAEN3E4BmQyqRUfKuoXDoerLxQKNWk0GodKpXJ9fX1uRkVFT0PO8Ofn53fT09MrQ3seYQFBEAxBEGxgoF+C99zC3DyLyazqAMMwhChe2G+sq6uzbGhoMFc0dlkslgmnqckAubRDufzED+qMlZ9isZiWlJQ8Ii4ufgKPx9MRSyTUX2Vk4imIDDqjCv27ksl0zMzM7Pvp0+dWXikcDkefoCQ4NZPJ7HD79p3NT548bRXrsaq62t4Ts4igqP9lMimpoaHBzNDQsKistMzd2srqoyr8z+VydePi4scnJiWPlMmkJKFQpMnhcPSQ5/r6+iV9+waHrli56lO3bt0i+gQGnkLfSIoAb3EeAABgWE6or6+3MDMz+xZYWIlskSraBKytrbVxdnaKFovFtNraOuv9+w+28u4GAIDysjI3tGyhUilchWX+oDwvKy93s7FRHGfT3s42KSU5JUTR8x8FiYTveVNfX28hl8sJBAJBzmQyHbOzcwLxxp9YIqah2waLyspKJ7yNSwCaZQOF8v2mupqaGtt6JeOcw+Hoc7gcfU1NjUZIBd0R8bAsK2tuW0V9Zmdnmxxx4+Yu5PfMGdNnb9+x8427m/vLPn0CT7m4OL/91Zf3IIAAgA0M29aB0WlY+VhWXu7WwcEhXlEZdra2yZ8+f+7589QqR1VVlX1UdMzUrKysIAhAMIfD0QeoNi8tK/OwtbNNVlZGeXmFq5mZ6SdFAcXR0NbWrmnv5REtbNFKpmNKSupwPMObL+DTebzmxcvKykonc3OLLEU8oMptiwAAoK+HbwMA0NzPVVVVDkicY6XfAP/cjXJt6fCWlhaZLBbLhEKh8BTZ3Do6OpWamhoN9fX1lljv5rZQWlrmMXjQwP2q5NVhtLS7mExmh9TU1GHlZeVu2Lx8AZ+OyKKKykonCwvzLEX88aP2NpfL1a1vaLBQJKOqqqodEBqU6eiq1D982LAde/fue5Kekdmvb3DQiW7dfCOwm/kkMkmh52Rtba01+ncLnYbPZ4jFYpqqOg0RAAgGoKUAZbFZxv36Bh93VeAVgnxkfUODOTZoFRoECJJDoH2eOgAgUf3ZxuvWrQ1SNAgR5VpdXZ29ccP63kVFRZ3fvH03a8XK1Z969Qo4N27smLXtv4EGhmAFt/gA0PzdEomEgii1bq6urwUCPr2wsKirnZ1tcmZmVpCtrW2ysgUtGIaha9cj9hQUFHqPGztmjY2NdRqRSBS/fPlq7nfPoNZoZLFMaOo0pTc6qAIOh6NPIZP5M2ZMm6MoDxJd/qtXwL8CyvqFSCSKEQ+1pqYmQwqVonCBiEwmC6Sy7+6a7fUkQ4NCofBXrlw+pKy83PXtm7ezVq9Zl+7r431r8uRJi9trUALQPAEoWmWGIAgmkUhCZCe4zXGl4LvaWu3+FWjPmG9iNxnSFExciJHZRhEw5l8AQLPxOG7smDWKBDXlaxBeLNhsthGdrl2tbHxooVb6A3v3OhsdHTMFWVCKioqeht2df/Lk6dLYuLiJE8aPXzF+/LhVZDJZkJKSOgzvaNCvBquRZaLsNgc0pFIp+estUQp3I0gkklAqkVIAaJ4naG3cMtNuKOlvZbyA8DWPx9NRU1OTtiHfkPb4JfLNydEx9vjxoxapqWlDX7x8Of/8hYuhc+fMnqrI07W9YLPZRlQlMg1PNqg6Bt8nJIy+dSty24QJ41cMGzpkF41G4xQXF3c6EXayxaJ8e+RkYO/eZw4dPnJ72LChuwgEgiwqOmZqYGDv0229p0w2kUgkEQzDEDL34n0ji8UyMTDQL1bW99ra2jUAqCI/8XkDTSOXy9PZtXv3C1dX1zdTp05eaGhoWAhBELx9+05cvemnAUEwdk5ms9jG/v7+V/y6+eIGcFZyxBCwWCyTKZMnLzIzw7/JCb3RoqouwGrWU9oMPltZyXTct2//o379+h5buGDeOMSr7M8/53yTrRAEwSP/GLE5KKhPWExM7OTDR45G6uroVCxcOH8s2gtN5fnmJ2VLU1OTAY1K5aiiO2F16l8FNpttpIWz04yARCILJdJf5TH9Har0P4vFMvb09Hg2dOiQ3fhlKA5/wWKxTDQ1lM9TiIHMYrGNjY2N85X1A9qjCSgxrIlEolgm/647KpOzZDJZgD7q4+fXLaJz506P3r9PGHM9ImI3ny+gL1m8cJSiDcifRVvyEXODFwxh5rcmdpOhss1TEpnU4vv+DiQnp4Rcux6xZ/z4sauGDB68V12d1lReXuFy4OD3DW5WI8tE3UW5XsFisUzUaSroHqrpkErBYrONvX28I/somMOQRXsWi2Wiqamhkq6lCDCAIUU3yQLw1QaVSn/5+MZCVZnKboOnAPiqM/4AX7FYLJO2bjEE4Ku+A7XWBby9vW8H9Qk8ifdOyz5rQ+78wO2bbDbbSFtbu0YVO6KxHTo6HgwM9Ev27Nnl/vlzbs/Xr9/Mvh5xY/fIP0Zs6tev73Ekj6r9yeVydXfu2vPCzc319Y/oNEQAWk8WhoaGhTw+n4E+y4wHI0PDwsKiIoWLINgrCjU01BUuPqFXhCEIgg0NDQvFYjFNkcsYFjY2NmkzbGzmjggZvnXDxr+Svb287uCdBWwLcrlcDb0LikZNTa2NOSpWCIFAkPfu1evs6zdv/rSzs02OjomZEti79xll5X/69DkgJSV1+IH9e53QK9kwDBPwPFYQmBib5P2KnU86nV4lEovVaTRaU1vXrP6sIFYGZYIXb+JUdt1lbU2tTQ//7lcAaG6nKmZVB0U33lVWVjpZWij22PgRWJibZ0+ePGnJiBEhW//atPl9VlZ2UMeOnk+VLbYq6mu5HMb9Tj5foI2+6rGtvvmRhdxfhfbwjbGJcV51dY0d3nEDqVRKVjQWsXVh8xgaGhQKhAKttmQYFgYGBsWNjSxTVa/E9vX1uXkrMnKrUCjUgGGY8OVLgc+fs2Z+iylTUVnpdPfe/Q0nQo+ZohdqYBgmgJZ8/rf0l4GhQZGyhWq5XK6GTJgkEkmkq6tbUVNba6NI7lZWMr/tppgYm+QxK5mOeMcv0FA2DgBmslalr5VBS0urDoblBDKZLPiZSbq9IBKJYh8f70gfH+/IhITEUWfPnT919MihX+INaGpikqssvh6Px2PAMExoYUSpYADy+Xx6WNipS0ePHrZmoN6Vy2HCz+xu2trapGppadZnZmYGOzs7R+Vk5/SeOmXyQmXvIH2LeDtgn9fW1lqbmJjkfXsGQTD2Gw0NDQvr6xssVBnzbRnIqsjPmzdv7nB3d385dszoFrHt5DBMUGXhnkKh8IlERd41rdu/mWaMnDMyLORxubrtlXMANLeXUChst4xUBhMT47zHT5i4t9uicfr0mXOjR4/agNwshQCv7Rh0evWQwYP2DRo44GDoibArj588XT5xwvgVv4pmVaGjo1PJFwjoGhoajap4RvwdMDUxyc3Ly/dT9LyystLJsg15/HfB0NCwsKy83O2HeNHAoKiuvr7VbVUI0Ppf8zivV2mcg68nBxQ9rqmttUFuaTMxMc5Lz8hQGDezorK1JwGVSuX27t3rXO/evc7du3d/Xfi16/vWrV3Tt0262ou2vqOmxqZTp+8nHPA244xNjPOYTGYHRWU0z+1/H++IRCL146Enwg8e2N8BfdQYhuUtdKGvdCq1dUxMjPNUPWaOJ+sJBIJM0a1rWN3c0NCwsKqqyqEtfjMwNCiqj1LOw6osTrRl63T0bI4PCUEQrPAbftI7SRX9AQAAjI2N8qurq+3kcrkangOHVCols1gsE3QMJFVhYmycV1nJdMS7kb0lrfi6QEVlpXNbfWZoaFhYV6ea3GkPDAwMilkslokqdoTRVxqw8QlR9bfZlwQCQe7i4vzOxcX5XVl5uev69RtTu3fvHv4tNraK/Xnz5q3tnh7uz8eMGb2+BR0q6jQEPMHjYG+f8OHDx0FtFWBnZ5v08WP6QHQQZwRSqZRUUFDgjS7b1KTlOUc0sK5pDvb2CR+/XqHaHujo6DD19fHd5lWBTCYjMplVuEI3KTn5D2xcp4CAnheSkpL/YLHZRmVlZe6KvLoQfP78uUeXLp0fYJVmHp/PUNbepqYmuXy+gP7li+JgwqqAQCDI7ezskj78QNv+Spgo4IWqqip77Go2BEFwWXlr10UAmnfFSkpLPZEYOh06OMTHxsVPwGtLoVComZScMsK+xULjr1t40dTUbDBGGeLaWlp1WlqauMF58VwxAWg+RoqXnpSUNNLe3k7lBdJ/k3eZMni4u7+IjsYPUJqdndMbANUWEhTJsPbSQyaTBaampp8yM7OCVclPpVK5nh6ezxISE0fHxb8f79fNNwI9geTm5vp7eno8w3r98Hg8HWULyL8Ktja2Kdk5Ob0FAoEW3vOS4pIW18l36OAQH4eJC4fgy5cvPnw+n4EcIXB0coyJjWs7Xoeisc5msw3ZbLYh3jMFUIkP7O3sE1Xp+79rwdze3q5VTKafAZ1Or6ap05rS0/ED6sfExE52cLB//31xFai0K/vlS4GPrY1NKnoxCYBm3sQsdgLsbndbCOzd+8y7qOhpKSmpw7p6db2rajwnRXI+MTFpJDaeAfYb6XR6NZVK5SBBdn8GqsjPz7m5Pbp26dwqQHPzEQgVlC8CQWZsbIR7DFDRPICly8HeLuFjesaAH1F82yMjVd2gMDc3z2poaDAvURIjSCKRUAqLirp26tSxRcBRsVhMFYvF6kCBAk0gEGR2tq2Pwqg61ynjYVXKUFNTk9ra2KQoiwXUXpraC3t7+4QPHz8OwovtCMMwhMiCX12vKvLEzt4u6fPn3B6K5hql79rZJaWkpA7He9bE4eijjT49Pd0yCILgoqKizqqUXV5e7qoohktSUtIfSHtZW1unfflS4IO+4AGN6OiYKQ4ODgrbFi8W36+EIp1RKBRqZGZmBbceGy37rEMHh/iv8WxayQqpVEqOj38/7u/gHQQFhYVeFhbmWdi4dc260Pcx7+TkGPM+IWGMsri4RkZGX8RiMS0vP7+bsjohnBM4ADTzs4kJfuw5PFs0MzMruC0vG0sLi8yS0lLPxsZGE7znxcUlnfDSW9WvQPZzuVzdvPx8Pysry4/IN2AvNmirjF8NKpXKMzU1/ZyYlPQH3vP4+PfjrK1t0tp/Wkh1/RKA1vOT/dc+ayu+rYW5eVZ5eZkbOuYjGiUlLfVjVdEeO8LWzjYZfdlHi/qL21+/hbl5NhVztFtVXfdzbm4PvEsnVNZpvv7bojJ//+5XGxoazBUZegisrKzS7e3tEu/ff7AOa8BnZmb2lcMtBZeZmdknNovdKsBiYWFh14rKSmdY/t3V748/Rmx+8vTZUmWKCQD43iwymazFAheJTBKiy1ZWlqamZgM66jmC4uKSjnFxcROGDR+6E52uo6PDdHZ2irp06fJR/+7dr7Z1hprH4+tgV5VlMhkxOTklpC3X3LFjR689c+bs2fr6BvO2vkUZxo0ds+ba9Yi9dXV1Cldmm9E+g4tEIglhWLXga64uLm+LcQbrixcvF9BotCY5pr/y8vL90AE2AWhetLxyNfzgwAH9DyErsT16+F/m83mMZ8+ft4idI5PJiJcuXzni5dX1blseFapCFd4zMTbOQ98SA0Czt1FqatpQ7DE+AoEg+/z5c09subW1dVb3HzxcM3bsmLXfEttSWH/QWFbWh2QSSYjtF/y6VVemAwJ6XqiprbV5+uzZYvR3S6VSUvrXYIdKq/r+nS3q7Ns3+HhOzqfeHz58aPei0rixY9ZcvnLlcFvBYBH07t3rbHzc+/FRUVHTevVqGQcHb7zDMAwlJCaOQu8itXdxg0RSTaYZGRkWdunS+UHEjZu7sEo1s6rKQSyRUNGLB6NGjtz4/PmLhVijnM1mG547fzFs2tQp8xEZ1zc4KLSsrNzt5ctXc5Uthisa689fvFyoqaHRiOYp5Uafam00ZszodRE3buzCng3/WZBJJCHe0Vvst6NlgFgspkXHxEzG3izZHkAQBI8fP27l5ctXjmAVn5KSEs+Hjx6vnDhh/PIW76ggu3l8HgNvhzMxMXHUzy52+vn5XfuU86lXXFz8hMBA5V67XwEDAEBGemsPgbKyMre376JmjAgZvg1Jg3COdEAQBI8fN3b1+QsXT6Bj8eCiLV5Sgdd4PH6r9isvr3ApLy93RY9tEokkUrQzbW9nl4j1GpBIJJSEhMTRWF7Dc+338PB4rq5OYz989Hhle48z9+/f70h6Rnp/RQuVPwIqlcob+ceIzadPnzmnSH6KRCINuVxOwN7AmZySEiKVSknId6gyvzbjVywMq1bG2LGj14Zfu75P0aLDj6JZnretO1laWmR27Oj55OKly0exG7kvX72eyxcItAN69rz4A/WLlHs1tN0+JsbG+Z07d3oUHn5tf3svyfH19bnJ4XD0Y2PjWm1mpH9MH6Cnp1eG6CUEAkE++CaAGQAAF71JREFUduzotefOXwxDB9/GpRpAsFgioX3+nNsqNlBcfPw4oVCkicQB1NHRYfYNDgo9e/b8KZFIpI7Om5SUPCI//0u3QQMHHEDSWsv91hvrWJBIZCHekaa7d+9tSEVdFtPqOyAILiou7tyEiUMll8sJN2/e2uHj4x2JDjzdvKnQUj527dLlPo1Ga7pz995G7EUiERE3dtna2qS4ODsrvJ1aEcgkEu43YcHj8VrpQgAA8FXWfaOnc6dOj7S1tGuvX4/Yo4iPiESiZPy4savPnj13uq2xqEhvcHZyisLal1KplPQ+IWEMWrdycLBPMDc3y74VeXuLsuDSmpqaDcHBQSeuXA0/hKW7qanJgMVmGWM3aloBhqGKigoXHo/HQCfLZDLi1fBr+4ODg06gQ6q4uLi8LcYsOkilUtL79wljsfOHqjojAO3TRyeMH7fy2rWIvTU1LWNOVlRWOt2KvL1lypRJLWwxVekYMnjw3oyMzL5x8fHjlOXD20Czt7dLsrK0TL8VeXursj7T0NBg9e3b9/iVK+GHsPK0icPRZzWyTNqSy4rWF1S1I0KGD9v+/MXLBRUVFS1i+8IwDJWVlbm3Hci95XO5XE6Qy3/skjIej6eDPbJeVl7uitVpFIGIp8CrqalJVyxfOuzY8RPXkpKTR7i4OL9j0BlVIrFIPTkpZcSyZUtCkB3H6dOnzT1+LPT6gYOH7nl4eDzX09Utr6quss/NzfN3dHSMQRuXZDJZ0DOgx8WXL1/NVddQZwEAgEQsocXGxk0MDupzAq0sGBjolyyYP2/8/v0HH3h4uL+wsbZOo6nT2CwWy4TJrOowc8b0OQAAEH7t+j4IguT2X28rqaiocGaz2Ubo4MbduvlGvHn7dhafz2dIpVJy9+5+ra4VBqBZWdHT0ysVikSaV8Ov7Xd2cooWCoWaBYWFXukf0wfMmT17Kl6k9MDevc4eOHj4bltu/QAA4OLq/PbcuQsn9fR0y7Q0m6+WTk1LG+Lj7XW7rWjrPXv0uMTl8nQ3bPwrycfb67allWU6mUQWfExPH9izZ4+L2CseFcHBwT5h3NgxazZt3hLv4+0daW5hnkWhUHjVVdX2MpmMNGrUyL8AaP8um6enx7Ojx0IjEhOTRkplUpKLs3OUoiDVvr4+NyMjb29BC4sv+QW+unq65XQ6vRo7OXt5db27fcfON4MGDdxPIpJEVVVVDklJyX/Y2NqkDhw44CCST01NTbps6ZKQY8dDr+fkfOrV0dPzqVAk1ExMSBplamb6adzYMavR5f7MTuKdu/c2cjgcfSdHxxgIguDa2lrr0tIyD/TOx5gxo9c9ffZ8sbm5WTaSFhUVPS04OOgE+upKAJoFrZGx0ZewsFOXfHy8I8USMbW4uKRTcnLyiLFjRq8zQ93a8XcdabO3s0tqbGSZvn+fMAZAALa0sMhEgpr6+XW7/urV67n1X13TFd2M0B7aiESiZMP6tYGhoWHh795FT3ewt0ugqauz8/O/dBs+bOjOp8+eL26jCNy6qFQqb+XK5YOPHz9xLTomdrJjhw5xWtpatXwen5GRmdl32dIlIxRNmp6eHs8HDRx4YP2GjSndunWLMDczyyGRSYLKikpnCoXCGzp0yB50fnt7u0Q2m21kYGhQhN19c3ZyjH748NEqCwuLTOSmkOycnN6urq5vfsZL0MXF+e3DR49WJSQkjpLL5Wr29naJim4ymThh/PITJ05e2bV7z4sunTs/0NfXL2Gx2caJiYmjegX0vIDesTQyMiycP2/uxMNHjt7q2rXLfTs726Tqqmr7hITE0X2C+pz09vb6FuyaTCYL16xeOSD0RNjVpOTkP9zc3F7p6+mVNjY2mpaUlHScP3/eRACab80sKCjwRo/1srJydwAAMDI2+tJirEMQ3KbS1Qbs7GyTJ06YsHzzlq2x3l7ety0szLMoVAq3pqbWViwSqX9z523nIp6Hh/vzN2/fzkLkm5OjUwyDQa/aum17VPfu3cPpX+PzvHn7dpaTo2MMAM19HRZ26tKUKZMX9e/X99iPfpO3l9eduro6q02bt8T7+/tfMTExzisqLO6SnpHe/89ZM2a1CHiN4wKOB3s7u6QL5y+eePjw0SrkRqDikpKOpmZmnzKzMDtr7WwrdXVaU8dOHR/X1dVZqXpsHYIguVQqJV+8dPmou5vbK6FIqFFUWNQ17cOHwbNmzZjFYHwPSK1o7Pr6+txsaGwwW7tuw4fuft2umZqafiaSiKLSklJPPT29sr59g0MB+DXy09XF5e2lS1eOBvcNCiVABLlcLleLiY2d5O/f/apEIqF+bwt1tr29fcLTZ88W6zB0KhkMepWTk1MMAACMHDnyr4cPH662RN0c9v59wtieAT0u3r59ZzOmgVrttkMQBC+YP298WNipy5mZmcGenp7PdHV0KqRSKTkxKWnknDn4OgsAANBoNM7KFSsGHw8NvRYdbZXu0MEhXktLs47H4+tkZ2X3WbJk0chv7dyO/g8K6nOSz+fT163fmOrr433L0tIyQ41IFKelpg3t17/v0Q4ODu8tLCyyTp8+e7ZLl84PAGg+fpnz6VMve3v7RKTtsnNyej998mypX/du14hqRIlUKiU/efpsydSpLfUslY2fnzxOC0DzrbIjR47Y9Nemze99fHxuWZibZ5EpZD5yWcsfI0K2toumr+jYseOTE2FhVxDZ4uri8hbN72hMmTxp8Zkz585s3rIttls3nxsUCpWXnp7en8/nMxYvWjBGlaPaWFCpVK6To2PMkydPl+rqNutg6JtBVdWTJk4Yv/zChUuhGzZuSvL26nrHwMCgCAYAykhP79+/f/8jdgqCLaupqUmXLFk08vDho5EZGZn9XFyc32lpadUVFhV1aWpqMrSytExHz1P+3btfZTWyTNauW/fRz8/vmqmJSS6RRBQVFxV3NjY2zu/TJ/BbvBt7e7uEx0+eLCspLfE0NDAsampqMsj59KlXVVWVw8IF88ehN4FDQoZvu3I1/NCGjZuS/P27X6Vra9dk5+T0rqiodF62dPEIZBGUxWYbHTxw6F5gn96naVQaB4Zh6O69+xt8vL1wdSIEft18I54/f7HQ3c3tFYFAkHl5db0LwzD08NGjVatWrlCqD/j6eN/au3ffk379+h2lUamcuro6q5TU1GF0Or165ozps1vmxvfKWbhwwdjQ0BPh+flffLt06fxALpMRk5JTRtDp2tUzpk9XGO9FGfT09EoZDHrVmzdvZ6mrq7P09fVK8by1bG1sUktKSj3v33+w1tzcPBuA5s0CA0ODIrT3TzOd88eePn323Ma/Nid07tzpkbGRUb5AINBOTkkJWbN6VX8CgSD39+9+lcPl6m3YuCnJx9srErGH0jMy+nf387vWsaPnU2V8O2JEyNZ79+6vR3vzJCUmj+zu53ctvPj6PnTeP2fNnHn69NlzW7Zui+ncufNDPV3dMjksV0tNSRs2fvzYVciRrpDhw7afO3fh5Jat26O9vb1uGxsZfeHz+Yx376KmB/XpcxIJ4K0IUpmM1K9v32O7du99PmjQgAMkIklUXVNtl5ycGmJmZvpp6JDBLeKTjQgZvu3u3XsbKiq/L0YkJSX/4efX7XphUWFXdN726IztmR/d3d1eDRs2dNeWrduj/bv7hZubm2eXlJR0TE1NGzp50sSl2A18v26+ES9evpzv6eHxHEAA9vH2xr3YRV2d1rRmzar+oaEnwmNj4ye6uDi/09XRqairq7OqrauzQtYAFNE6a9aMWadOnzm3ecu22C6dOz3U09MrlcNytdTUtKFjx45Zi+glw4cN3Xn23PmTW5E+MzbO5wv49Ldv383sE9TnJAfjEICFk6NjzJ079/5KSEgcBcMwwcbGOtXY2PiLqnaEsbHxl+nTps7fvWffUx8f70g7W9tkEokkjIuLn+Dl1fWOmpqa0ni8x46HXrewsMhEToBlZWf3MTAwLEJfZKBqf7q4uLy9dOny0eDgoBPKdBpFgD58+DjAxcX5LZnc+hYHqVRKysvP9ysuKu7cxOEYqBEIUm8f70gsg3y9rrf3l4ICHz6Px9DU0qofNHDAwcNHjt7q0cP/MpZhmFVVDslJySOQW0iGDB60j8fjM6RSCQV9IwcAzfEh8vLyuheXlHYU/b/2zj04qSuP4xcCISEvDEmQxGASE4jyCOShBojmHU2cdna6O/WxfU+36s7uH7va7mN26+zsTne21e629mnb7ajV7bTWtia20USr5qHRaCBA4F7CBQIJQgiQhEAucO/+EW+HuYJm3Vq1vZ//bu4lnMc9v/M7P875/ubnU1KYTK9SqTiEizLOzs5m9vX1b5nyePIWGg7Ampub3iAGMs6fv/DE2NiYKD0jw7mprTVmWuz5+XmmyQRXlpYKLgwMXH7EaDSuodFoCI/HU0sk4s6UlBRvrEa8NjTUeunSwE+3P/eLp2/X4ACwINLr8Xhy8Wu5vPooh8MZtdlsQj6fHzcLA47bPbUMhEC5zWYXRiIReqlAcEEiEXdSqVQ0GAymQpBxbXQWAKPRuIbNZluJ6ZS9Ph8HNICKsbExcSgcZrAyMhw1NcqDeD0RBEkeGRlZX1ZW9jWxDMFgMBWEoGpiEMtoNK65NHD5ERqNhjQ1Nrx1qzOsGIZRrgwOPmwywZXh0ELf19au/0Cj1daXFBdfxHUK3nvv/Xd4PJ66qqry+JkzZ58NBoOp7Kwsa6mA3xNLewcAFrbwarW6OhiGK5hMpo/P5/fGyhAzMqJft2xZnjYtLe2mo2karba+qLBwMJ4wXCAQSOvr69/impwswP9WX1d7gGik5+bmMnp6+7a5XK4CAMMo1dVrP+ZyuQaz2SLDnTYMwyhq9XBzWZmkU6PV1quGVBupVGokb1meTiIWnyI6l16vd6nT5Srkx9iCrdFoG1asKBogamS5XJPL/f7ZTLzNwuFw4rBG0yiTSk9GPzcxMcH/5tz5pwAMoyiVisP5+QvHCTEMo5w5c/ZZh8NRws7KsuIL5Bvv1xg+5m5VtqmpqTyPx5sby7H0eDxci8UqDYfDiXnL8nTJycnTO3b80nHo4IeMeOKyGIZRBgevPlRRUf5lrDGNIEiSwQAqzRaLzO/3L6HT6UF59dr/cLncb4N+Wq2urqBg+TXi+PZ4PLkGA6iw2e3CcDicyM7MHFMqFYdjaY9ZrFZJEiNplsPJMRHvnT37zTPj4+Ol+LWsXNZevGLFJRCC5LjWF4qiCdeuDbXhC6xoQqEQQ6PVNhD7yWKxlPX29W+lUqmRutr179/qnDqKolS93rAOMhrXzs7MsOmJicFNbW0vz8xMZ2MYRiFm4JqZmWFrtbp6u92+Kjs72ywUCrtjplgHFuYJCDJWw2Zz+fT0dE5qSsqUskZ5KPooFYZhFJVKvQGCoGoEQZKzs7PNTU2NbxpAUMHLzx/Gx5h9fLwUwDAKcR4Ih8N0tXq4pbxc1k78frPZImUyk33Ecefz+XJAEFJYx8bEoVAoKSMj/XqNsuYgvpsxFAoxtFpdvVS6oEkQTTAYTAFBSEEU1h4dNVVdvHTpZ7SEhFBjY+NbbHamzWK1SgavDD6MhELJAAAArIwMR3Nz034qlYr6fL6c9vaO3bW16z8g1gnH4XAUI0gomceLnzEV5/p1Z5Fer1836XbzluXl6SQScSfxffT6fBzn9esrFjOXXBoYeMQYtRuNL+D3VlVWfj40pNoY3S5DQ6q4fsLVq9c2SSTiTmIignfePfD+6qrKz2QyWdzspTgIgiQ9+dQz/o8OH6SpVOoNWq22nkanz/Py84fFYtFpoh6W0+ksDAQC6fEEcCcnJ3kgCMntdvuqCIrScnKyTQq5/Ag+p8zMzLBtNrswetGME29O8Hg8uW63Ox9fLE3PzGSd+PLEtz9QJDIYc22trXunpqaWMRiJc9FZbfx+P+vrzlO/DgYCaSKRqKssKjU0giDJvb19WyccDj6GolSpVHpSIOD36HQjddHv363mIxRFqUajcS0Mm8s9Xm8uhUJBy2Wy9sXoSCIIkqw3GJQWi1WK20iFvPpItE3QaLQNRUVFl5kxBLfV6uFmPr+kl7jjyOWaXA5BUDXeB8JVq86IRMJuCoWCWaxWSc+FnsfwZ1lLWBMbWlpeM5ngSi6Xa0hNTfGgKEodvHr1IdOoqQrf4SUSCrtFImE3/jmdbqSWx+OpvtWKiII4D47ZbEJaQkIo2vYDQHz7CgAAAMNweVpa2iQxa6fX611qAEGFbcwmCoXDDBaLNbGuRnkQ75v5+XmmXm+oKYuRAjwQCKSNjppWR9cDAAAAhKDqy5ev/IRGoyHNTY1vEP21aDAMo0BG41qjcXRNJBKhFxYWDMZKKx/PnsbC7/ezOjtP/SoQCKQLhcIz+PiPN75vNfeazRapyWSqwrMiriwVnJdIJJ23C7QFg8EUrVbXYILhCmR+npnJzrS1NDe/DsPmipycbBNxTLpcrgIQgqrH7eMrIyhKW7qUA8mrq4/idmpoSLWx4+TJ3z6/e1fbufMXnrzucBQzmUxfYWHhoEgk7IqXPMVstkhBEFTcsDFDYrHoNDFQp9fra4aHNU0RFKUBAADk5eXpapSKw/h9GIbLU1PT3NGZK1EUpXZ1de9wuVwFHA5ntLGx4W273b7yr397qXv/6//ixQsGfnrssz2RcJje1ta6t7v7zHOzs7OZrCWsiZLi4oslJSX9xHZVq4ebBQJ+TyytLxRFE3S6kVqTyVRJT6QHS4pL+ouLF36Qj8ZkgisyMtKdRA2bWO+D1+fjnD7dtRNBkORymaw9ll0FAAC4cmXwYYPBoMSvi0uKL65ZvfoYcb4BgIX3y2SCK2EYrph0u3mJdHpAoZAfIfo5U1NTeSAIycdsNlEkEqEL+CW9ZWVlX1GpVDQQCKQZjaNr4mVFQxAkqa+/f8vE+IQARdEEsVh8WiQSdqvVw82xyjM6aqqCYbjCPTWVTwEATCwRn1pZWno+uv3xsQkaQIXP5+NQExLCrRs3vBoORxLnAnMZ+TeCabGAYbg8MzPThqJoQldX9w4EQZJZS1gTpYLSC/GCsaFQiNHb17cVr4NIJOoSi0Wn1erhFmIdLFarpLe3b9vtfMbRUVMVi8WaiOXzQZBxbVYW20K0T5OTk7yREf16p9NZlJubqxeLxadi2WUMwyhdXd3bnU5nUXZODtzc1PhmvPbA6weCkBw2m8tnZ2fZ6enpzhql4hBuC/QGgzKXyzXEys5O7DMAWJDZWLmy9FzMPgMhuc/rXUqlUiOtrRv3hcORxLk5PwtfA83PzzMNBlBJ9AvHbDZhz4WexwAKBatdv+7f0XPMYtcRHo8nV6PRNtw4opuwSrjqrEwqPalSqVtizSM4brc7v7//4qOzfn8mACxsTGhr3bgPF0u/lT94k08zPZ194kT78/j9RAZjblNb6ytu91R+tE8z4XCUhMPhRPxdxucCCobdPamVV/bu+yJWQOmHBIZhlJdf2Xti27atu6J3kJB8d+ABJfzXZZIfB1qtrm7vvn1fvHfg3SV3cgabhITk3uDz+XL2vfrP43te/LNyMbs1QqEQ44knn5478tGhOxLBJCEhISGiUqk2tHec3PXHP/y+8V6XJR5dXd3bnS5X4dYtm1+I98yxY5+9GA6HE4liuSQkJCT3C4s6T/n/cC+zTX0fdJz86je5XK6BDCbdRe5itjmS+xMMwyjHj3/+p/q6ugNkMImE5MEBQZCk1/e/cXTLls0v3M1MoSQkJCQPOiAIyevrahejM0dCQkJy33JHwk0kC8eF2ts7dmm02oYXnt9926wfJCQkN4NhGOW11/Z/LJdXH12xomggFAolTUw4+O0dHbvS0tNcmzc/+rt7XUYSEpLF4fF4uG+/8+6HMpm0o1Qg6Fns58jAEwkJyXfP/W9Xdu7c/vhtHyLtIwkJyX1Owp49e+7aP0dRNCE/P18T62zjg84nn3z6l0m3e/nOHdsfZzAYgXtdnh8yGIZRudyl0K30mEgeTCgUCpCRkXG9v//i5m/OnXt68OrVh6wWa5lYLD79821bd9+JwCgJCcn3D4ZhlJf+/o9OpUJ+pKW5+X8+noxhGFUoXHX2bpSNhITkxwcGYFQmk+krKCi4ST/zQQJDMSo7i23lcDg3aTSSkJCQ3A/8F4Dtcsmue59rAAAAAElFTkSuQmCC"
                class="image"
                style="width: 43.93rem; height: 0.74rem; display: block; z-index: 10; left: 7.36rem; top: 24.71rem;" />
            <img src="data:image/png;base64,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"
                class="image"
                style="width: 42.19rem; height: 0.74rem; display: block; z-index: 10; left: 7.35rem; top: 26.48rem;" />
            <p class="paragraph body-text"
                style="width: 43.10rem; height: 3.55rem; font-size: 0.75rem; left: 7.29rem; top: 23.67rem; text-align: left; font-family: 'pro', serif; color: #58595b; font-weight: 300;">
                <span class="position style"
                    style="width: 0.37rem; height: 0.89rem; left: 0.00rem; top: 0.02rem; transform: ScaleX(1.05);">T</span>
                <span class="position style"
                    style="width: 1.02rem; height: 0.89rem; left: 0.37rem; top: 0.02rem; transform: ScaleX(1.05);">hat</span>
                <span class="position style"
                    style="width: 2.42rem; height: 0.89rem; left: 1.56rem; top: 0.02rem; transform: ScaleX(1.05);">
                    withdra</span>
                <span class="position style"
                    style="width: 1.09rem; height: 0.89rem; left: 3.98rem; top: 0.02rem; transform: ScaleX(1.05);">wal</span>
                <span class="position style"
                    style="width: 1.00rem; height: 0.89rem; left: 5.24rem; top: 0.02rem; transform: ScaleX(1.05);">
                    ma</span>
                <span class="position style"
                    style="width: 0.35rem; height: 0.89rem; left: 6.24rem; top: 0.02rem; transform: ScaleX(1.05);">y</span>
                <span class="position style"
                    style="width: 1.09rem; height: 0.89rem; left: 6.76rem; top: 0.02rem; transform: ScaleX(1.05);">
                    not</span>
                <span class="position style"
                    style="width: 0.82rem; height: 0.89rem; left: 8.02rem; top: 0.02rem; transform: ScaleX(1.05);">
                    be</span>
                <span class="position style"
                    style="width: 1.81rem; height: 0.89rem; left: 9.01rem; top: 0.02rem; transform: ScaleX(1.05);">
                    made</span>
                <span class="position style"
                    style="width: 0.59rem; height: 0.89rem; left: 10.99rem; top: 0.02rem; transform: ScaleX(1.05);">
                    in</span>
                <span class="position style"
                    style="width: 0.36rem; height: 0.89rem; left: 11.75rem; top: 0.02rem; transform: ScaleX(1.05);">
                    a</span>
                <span class="position style"
                    style="width: 0.39rem; height: 0.89rem; left: 12.28rem; top: 0.02rem; transform: ScaleX(1.05);">
                    K</span>
                <span class="position style"
                    style="width: 3.44rem; height: 0.89rem; left: 12.70rem; top: 0.02rem; transform: ScaleX(1.05);">aamiyaabu</span>
                <span class="position style"
                    style="width: 0.39rem; height: 0.89rem; left: 16.31rem; top: 0.02rem; transform: ScaleX(1.05);">
                    K</span>
                <span class="position style"
                    style="width: 0.89rem; height: 0.89rem; left: 16.71rem; top: 0.02rem; transform: ScaleX(1.05);">ids</span>
                <span class="position style"
                    style="width: 0.46rem; height: 0.89rem; left: 17.77rem; top: 0.02rem; transform: ScaleX(1.05);">
                    A</span>
                <span class="position style"
                    style="width: 2.21rem; height: 0.89rem; left: 18.23rem; top: 0.02rem; transform: ScaleX(1.05);">ccount</span>
                <span class="position style"
                    style="width: 0.67rem; height: 0.89rem; left: 20.61rem; top: 0.02rem; transform: ScaleX(1.05);">
                    pr</span>
                <span class="position style"
                    style="width: 0.82rem; height: 0.89rem; left: 21.28rem; top: 0.02rem; transform: ScaleX(1.05);">ior</span>
                <span class="position style"
                    style="width: 0.24rem; height: 0.89rem; left: 22.27rem; top: 0.02rem; transform: ScaleX(1.05);">
                    t</span>
                <span class="position style"
                    style="width: 0.42rem; height: 0.89rem; left: 22.51rem; top: 0.02rem; transform: ScaleX(1.05);">o</span>
                <span class="position style"
                    style="width: 1.05rem; height: 0.89rem; left: 23.10rem; top: 0.02rem; transform: ScaleX(1.05);">
                    the</span>
                <span class="position style"
                    style="width: 1.90rem; height: 0.89rem; left: 24.32rem; top: 0.02rem; transform: ScaleX(1.05);">
                    matur</span>
                <span class="position style"
                    style="width: 0.41rem; height: 0.89rem; left: 26.22rem; top: 0.02rem; transform: ScaleX(1.05);">it</span>
                <span class="position style"
                    style="width: 0.35rem; height: 0.89rem; left: 26.64rem; top: 0.02rem; transform: ScaleX(1.05);">y</span>
                <span class="position style"
                    style="width: 1.03rem; height: 0.89rem; left: 27.16rem; top: 0.02rem; transform: ScaleX(1.05);">
                    dat</span>
                <span class="position style"
                    style="width: 0.38rem; height: 0.89rem; left: 28.18rem; top: 0.02rem; transform: ScaleX(1.05);">e</span>
                <span class="position style"
                    style="width: 0.14rem; height: 0.89rem; left: 28.56rem; top: 0.02rem; transform: ScaleX(1.05);">.</span>
                <span class="position style"
                    style="width: 0.92rem; height: 0.89rem; left: 28.87rem; top: 0.02rem; transform: ScaleX(1.05);">
                    Ho</span>
                <span class="position style"
                    style="width: 0.57rem; height: 0.89rem; left: 29.78rem; top: 0.02rem; transform: ScaleX(1.05);">w</span>
                <span class="position style"
                    style="width: 0.38rem; height: 0.89rem; left: 30.35rem; top: 0.02rem; transform: ScaleX(1.05);">e</span>
                <span class="position style"
                    style="width: 0.36rem; height: 0.89rem; left: 30.73rem; top: 0.02rem; transform: ScaleX(1.05);">v</span>
                <span class="position style"
                    style="width: 0.61rem; height: 0.89rem; left: 31.08rem; top: 0.02rem; transform: ScaleX(1.05);">er</span>
                <span class="position style"
                    style="width: 0.14rem; height: 0.89rem; left: 31.66rem; top: 0.02rem; transform: ScaleX(1.05);">,</span>
                <span class="position style"
                    style="width: 1.05rem; height: 0.89rem; left: 31.97rem; top: 0.02rem; transform: ScaleX(1.05);">
                    the</span>
                <span class="position style"
                    style="width: 1.51rem; height: 0.89rem; left: 33.19rem; top: 0.02rem; transform: ScaleX(1.05);">
                    Bank</span>
                <span class="position style"
                    style="width: 1.00rem; height: 0.89rem; left: 34.88rem; top: 0.02rem; transform: ScaleX(1.05);">
                    ma</span>
                <span class="position style"
                    style="width: 0.35rem; height: 0.89rem; left: 35.87rem; top: 0.02rem; transform: ScaleX(1.05);">y</span>
                <span class="position style"
                    style="width: 1.46rem; height: 0.89rem; left: 36.39rem; top: 0.02rem; transform: ScaleX(1.05);">
                    appr</span>
                <span class="position style"
                    style="width: 0.42rem; height: 0.89rem; left: 37.85rem; top: 0.02rem; transform: ScaleX(1.05);">o</span>
                <span class="position style"
                    style="width: 0.36rem; height: 0.89rem; left: 38.26rem; top: 0.02rem; transform: ScaleX(1.05);">v</span>
                <span class="position style"
                    style="width: 0.38rem; height: 0.89rem; left: 38.61rem; top: 0.02rem; transform: ScaleX(1.05);">e</span>
                <span class="position style"
                    style="width: 2.42rem; height: 0.89rem; left: 39.17rem; top: 0.02rem; transform: ScaleX(1.05);">
                    withdra</span>
                <span class="position style"
                    style="width: 1.09rem; height: 0.89rem; left: 41.58rem; top: 0.02rem; transform: ScaleX(1.05);">wal</span>
                <span class="position style"
                    style="width: 0.82rem; height: 0.89rem; left: 0.00rem; top: 1.79rem; transform: ScaleX(1.05);">
                    be</span>
                <span class="position style"
                    style="width: 0.21rem; height: 0.89rem; left: 0.99rem; top: 1.79rem; transform: ScaleX(1.05);">
                    f</span>
                <span class="position style"
                    style="width: 0.65rem; height: 0.89rem; left: 1.19rem; top: 1.79rem; transform: ScaleX(1.05);">or</span>
                <span class="position style"
                    style="width: 0.21rem; height: 0.89rem; left: 1.86rem; top: 1.79rem; transform: ScaleX(1.05);">f</span>
                <span class="position style"
                    style="width: 0.79rem; height: 0.89rem; left: 2.06rem; top: 1.79rem; transform: ScaleX(1.05);">eit</span>
                <span class="position style"
                    style="width: 0.81rem; height: 0.89rem; left: 2.84rem; top: 1.79rem; transform: ScaleX(1.05);">ed</span>
                <span class="position style"
                    style="width: 0.14rem; height: 0.89rem; left: 3.65rem; top: 1.79rem; transform: ScaleX(1.05);">.</span>
            </p>
            <p class="paragraph body-text"
                style="width: 49.50rem; height: 0.90rem; font-size: 0.75rem; left: 7.29rem; top: 27.22rem; text-align: left; font-family: 'pro', serif; color: #58595b; font-weight: 300;">
                <span class="position style"
                    style="width: 0.23rem; height: 0.89rem; left: 0.00rem; top: 0.02rem; transform: ScaleX(1.05);">r</span>
                <span class="position style"
                    style="width: 1.51rem; height: 0.89rem; left: 0.23rem; top: 0.02rem; transform: ScaleX(1.05);">each</span>
                <span class="position style"
                    style="width: 1.05rem; height: 0.89rem; left: 1.91rem; top: 0.02rem; transform: ScaleX(1.05);">
                    the</span>
                <span class="position style"
                    style="width: 1.50rem; height: 0.89rem; left: 3.13rem; top: 0.02rem; transform: ScaleX(1.05);">
                    desir</span>
                <span class="position style"
                    style="width: 0.81rem; height: 0.89rem; left: 4.63rem; top: 0.02rem; transform: ScaleX(1.05);">ed</span>
                <span class="position style"
                    style="width: 0.83rem; height: 0.89rem; left: 5.61rem; top: 0.02rem; transform: ScaleX(1.05);">
                    tar</span>
                <span class="position style"
                    style="width: 1.05rem; height: 0.89rem; left: 6.44rem; top: 0.02rem; transform: ScaleX(1.05);">get</span>
                <span class="position style"
                    style="width: 0.65rem; height: 0.89rem; left: 7.66rem; top: 0.02rem; transform: ScaleX(1.05);">
                    as</span>
                <span class="position style"
                    style="width: 0.79rem; height: 0.89rem; left: 8.49rem; top: 0.02rem; transform: ScaleX(1.05);">
                    ag</span>
                <span class="position style"
                    style="width: 0.23rem; height: 0.89rem; left: 9.27rem; top: 0.02rem; transform: ScaleX(1.05);">r</span>
                <span class="position style"
                    style="width: 1.19rem; height: 0.89rem; left: 9.49rem; top: 0.02rem; transform: ScaleX(1.05);">eed</span>
                <span class="position style"
                    style="width: 1.06rem; height: 0.89rem; left: 10.86rem; top: 0.02rem; transform: ScaleX(1.05);">
                    bet</span>
                <span class="position style"
                    style="width: 0.57rem; height: 0.89rem; left: 11.93rem; top: 0.02rem; transform: ScaleX(1.05);">w</span>
                <span class="position style"
                    style="width: 1.19rem; height: 0.89rem; left: 12.49rem; top: 0.02rem; transform: ScaleX(1.05);">een</span>
                <span class="position style"
                    style="width: 1.05rem; height: 0.89rem; left: 13.85rem; top: 0.02rem; transform: ScaleX(1.05);">
                    the</span>
                <span class="position style"
                    style="width: 1.51rem; height: 0.89rem; left: 15.07rem; top: 0.02rem; transform: ScaleX(1.05);">
                    Bank</span>
                <span class="position style"
                    style="width: 1.21rem; height: 0.89rem; left: 16.76rem; top: 0.02rem; transform: ScaleX(1.05);">
                    and</span>
                <span class="position style"
                    style="width: 1.05rem; height: 0.89rem; left: 18.14rem; top: 0.02rem; transform: ScaleX(1.05);">
                    the</span>
                <span class="position style"
                    style="width: 1.31rem; height: 0.89rem; left: 19.36rem; top: 0.02rem; transform: ScaleX(1.05);">
                    cust</span>
                <span class="position style"
                    style="width: 1.68rem; height: 0.89rem; left: 20.66rem; top: 0.02rem; transform: ScaleX(1.05);">omer</span>
                <span class="position style"
                    style="width: 0.14rem; height: 0.89rem; left: 22.31rem; top: 0.02rem; transform: ScaleX(1.05);">.</span>
            </p>
            <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAoAAAAKCAYAAACNMs+9AAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAAGElEQVQYlWOMiIz+z0AEYCJG0ahC6ikEAFFSAh9YFx1pAAAAAElFTkSuQmCC"
                class="image"
                style="width: 0.40rem; height: 0.37rem; display: block; z-index: 0; left: 6.25rem; top: 28.37rem;" />
            <p class="paragraph body-text"
                style="width: 50.55rem; height: 0.89rem; font-size: 1.10rem; left: 6.25rem; top: 28.12rem; text-align: left; font-family: pro, serif; font-weight: 300;">
                <span class="position" style="width: 0.39rem; height: 0.89rem; left: 1.05rem; top: 0.01rem;">
                    <span class="style"></span>
                    <span class="style"
                        style="font-family: 'pro', serif; font-size: 1.00rem; transform: ScaleX(1.05);">
                    </span>
                    <span class="style"
                        style="font-family: 'pro', serif; font-size: 0.75rem; color: #58595b; transform: ScaleX(1.05);">K</span>
                </span>
                <span class="position style"
                    style="width: 3.44rem; height: 0.89rem; font-size: 0.75rem; left: 1.46rem; top: 0.01rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">aamiyaabu</span>
                <span class="position style"
                    style="width: 0.39rem; height: 0.89rem; font-size: 0.75rem; left: 5.07rem; top: 0.01rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    K</span>
                <span class="position style"
                    style="width: 0.89rem; height: 0.89rem; font-size: 0.75rem; left: 5.48rem; top: 0.01rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">ids</span>
                <span class="position style"
                    style="width: 0.46rem; height: 0.89rem; font-size: 0.75rem; left: 6.54rem; top: 0.01rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    A</span>
                <span class="position style"
                    style="width: 2.21rem; height: 0.89rem; font-size: 0.75rem; left: 6.99rem; top: 0.01rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">ccount</span>
                <span class="position style"
                    style="width: 0.46rem; height: 0.89rem; font-size: 0.75rem; left: 9.37rem; top: 0.01rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    is</span>
                <span class="position style"
                    style="width: 0.36rem; height: 0.89rem; font-size: 0.75rem; left: 10.00rem; top: 0.01rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    a</span>
                <span class="position style"
                    style="width: 2.39rem; height: 0.89rem; font-size: 0.75rem; left: 10.36rem; top: 0.01rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">vailable</span>
                <span class="position style"
                    style="width: 1.36rem; height: 0.89rem; font-size: 0.75rem; left: 12.92rem; top: 0.01rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    only</span>
                <span class="position style"
                    style="width: 0.21rem; height: 0.89rem; font-size: 0.75rem; left: 14.44rem; top: 0.01rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    f</span>
                <span class="position style"
                    style="width: 0.65rem; height: 0.89rem; font-size: 0.75rem; left: 14.64rem; top: 0.01rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">or</span>
                <span class="position style"
                    style="width: 1.77rem; height: 0.89rem; font-size: 0.75rem; left: 15.46rem; top: 0.01rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    childr</span>
                <span class="position style"
                    style="width: 0.80rem; height: 0.89rem; font-size: 0.75rem; left: 17.22rem; top: 0.01rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">en</span>
                <span class="position style"
                    style="width: 1.40rem; height: 0.89rem; font-size: 0.75rem; left: 18.20rem; top: 0.01rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    belo</span>
                <span class="position style"
                    style="width: 0.57rem; height: 0.89rem; font-size: 0.75rem; left: 19.60rem; top: 0.01rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">w</span>
                <span class="position style"
                    style="width: 0.77rem; height: 0.89rem; font-size: 0.75rem; left: 20.34rem; top: 0.01rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    16</span>
                <span class="position style"
                    style="width: 0.35rem; height: 0.89rem; font-size: 0.75rem; left: 21.28rem; top: 0.01rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    y</span>
                <span class="position style"
                    style="width: 1.27rem; height: 0.89rem; font-size: 0.75rem; left: 21.62rem; top: 0.01rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">ears</span>
                <span class="position style"
                    style="width: 0.63rem; height: 0.89rem; font-size: 0.75rem; left: 23.06rem; top: 0.01rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    of</span>
                <span class="position style"
                    style="width: 1.17rem; height: 0.89rem; font-size: 0.75rem; left: 23.86rem; top: 0.01rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    age</span>
                <span class="position style"
                    style="width: 0.14rem; height: 0.89rem; font-size: 0.75rem; left: 25.02rem; top: 0.01rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">.</span>
            </p>
            <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACMAAAAfCAYAAABtYXSPAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAAgklEQVRYhe3OsQ2DMBQE0DOyhF2jLMFASGGtCCkTgMRALEFS59PgdIiCqz/FvQleeA3vgpuIANA/uzbV6euVsM2acZqXCAA5pTXn/PHKhIAdACqvwBVlGGUYZRhlGGUYZRhlGGUYZRhlGGUYZZhbZSIA/MwepfjFbLPmyIzTvHhFzv7hbBjV2KRapwAAAABJRU5ErkJggg=="
                class="image"
                style="width: 1.31rem; height: 1.13rem; display: block; z-index: 0; left: 3.41rem; top: 29.70rem;" />
            <svg viewbox="0.000000, 0.000000, 4.000000, 3.700000" class="graphic"
                style="width: 0.40rem; height: 0.37rem; display: block; z-index: 10; left: 6.25rem; top: 31.86rem;">
                <path fill="#58595b" fill-opacity="1.000000" d="M 3.994 0 L 0 0 L 0 3.694 L 3.994 3.694 L 3.994 0 Z"
                    stroke="none" />
            </svg>
            <img src="data:image/png;base64,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"
                class="image"
                style="width: 39.20rem; height: 1.61rem; display: block; z-index: 10; left: 7.29rem; top: 31.73rem;" />
            <p class="paragraph body-text"
                style="width: 53.40rem; height: 2.09rem; font-size: 1.10rem; left: 3.40rem; top: 28.75rem; text-align: left; font-family: pro, serif;">
                <span class="position" style="width: 0.19rem; height: 0.95rem; left: 1.91rem; top: 1.03rem;">
                    <span class="style"></span>
                    <span class="style"
                        style="font-family: 'pro', serif; font-size: 1.00rem; transform: ScaleX(1.05);">
                    </span>
                    <span class="style"
                        style="font-family: 'pro', serif; font-size: 0.75rem; color: #58595b; transform: ScaleX(1.05);">I</span>
                </span>
                <span class="position style"
                    style="width: 0.44rem; height: 0.95rem; font-size: 0.75rem; left: 2.11rem; top: 1.03rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">n</span>
                <span class="position style"
                    style="width: 0.26rem; height: 0.95rem; font-size: 0.75rem; left: 2.54rem; top: 1.03rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">t</span>
                <span class="position style"
                    style="width: 0.65rem; height: 0.95rem; font-size: 0.75rem; left: 2.80rem; top: 1.03rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">er</span>
                <span class="position style"
                    style="width: 1.09rem; height: 0.95rem; font-size: 0.75rem; left: 3.45rem; top: 1.03rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">net</span>
                <span class="position style"
                    style="width: 1.61rem; height: 0.95rem; font-size: 0.75rem; left: 4.71rem; top: 1.03rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    Bank</span>
                <span class="position style"
                    style="width: 1.06rem; height: 0.95rem; font-size: 0.75rem; left: 6.34rem; top: 1.03rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">ing</span>
            </p>
            <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAoAAAAKCAYAAACNMs+9AAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAAGElEQVQYlWOMiIz+z0AEYCJG0ahC6ikEAFFSAh9YFx1pAAAAAElFTkSuQmCC"
                class="image"
                style="width: 0.40rem; height: 0.37rem; display: block; z-index: 0; left: 6.25rem; top: 33.62rem;" />
            <svg viewbox="0.000000, 0.000000, 4.000000, 3.700000" class="graphic"
                style="width: 0.40rem; height: 0.37rem; display: block; z-index: 10; left: 6.25rem; top: 32.74rem;">
                <path fill="#58595b" fill-opacity="1.000000" d="M 3.994 0 L 0 0 L 0 3.694 L 3.994 3.694 L 3.994 0 Z"
                    stroke="none" />
            </svg>
            <p class="paragraph body-text"
                style="width: 50.55rem; height: 1.28rem; font-size: 1.10rem; left: 6.25rem; top: 32.97rem; text-align: left; font-family: pro, serif; font-weight: 300;">
                <span class="position" style="width: 0.37rem; height: 0.89rem; left: 1.04rem; top: 0.40rem;">
                    <span class="style"></span>
                    <span class="style"
                        style="font-family: 'pro', serif; font-size: 1.00rem; transform: ScaleX(1.05);">
                    </span>
                    <span class="style"
                        style="font-family: 'pro', serif; font-size: 0.75rem; color: #58595b; transform: ScaleX(1.05);">T</span>
                </span>
                <span class="position style"
                    style="width: 0.80rem; height: 0.89rem; font-size: 0.75rem; left: 1.41rem; top: 0.40rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">he</span>
                <span class="position style"
                    style="width: 1.88rem; height: 0.89rem; font-size: 0.75rem; left: 2.39rem; top: 0.40rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    usage</span>
                <span class="position style"
                    style="width: 0.63rem; height: 0.89rem; font-size: 0.75rem; left: 4.44rem; top: 0.40rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    of</span>
                <span class="position style"
                    style="width: 1.05rem; height: 0.89rem; font-size: 0.75rem; left: 5.24rem; top: 0.40rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    the</span>
                <span class="position style"
                    style="width: 0.36rem; height: 0.89rem; font-size: 0.75rem; left: 6.46rem; top: 0.40rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    F</span>
                <span class="position style"
                    style="width: 2.30rem; height: 0.89rem; font-size: 0.75rem; left: 6.78rem; top: 0.40rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">aisaNet</span>
                <span class="position style"
                    style="width: 1.07rem; height: 0.89rem; font-size: 0.75rem; left: 9.26rem; top: 0.40rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    will</span>
                <span class="position style"
                    style="width: 0.82rem; height: 0.89rem; font-size: 0.75rem; left: 10.50rem; top: 0.40rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    be</span>
                <span class="position style"
                    style="width: 3.19rem; height: 0.89rem; font-size: 0.75rem; left: 11.49rem; top: 0.40rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    construed</span>
                <span class="position style"
                    style="width: 0.43rem; height: 0.89rem; font-size: 0.75rem; left: 14.85rem; top: 0.40rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    b</span>
                <span class="position style"
                    style="width: 0.35rem; height: 0.89rem; font-size: 0.75rem; left: 15.28rem; top: 0.40rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">y</span>
                <span class="position style"
                    style="width: 1.05rem; height: 0.89rem; font-size: 0.75rem; left: 15.80rem; top: 0.40rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    the</span>
                <span class="position style"
                    style="width: 1.51rem; height: 0.89rem; font-size: 0.75rem; left: 17.02rem; top: 0.40rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    Bank</span>
                <span class="position style"
                    style="width: 0.65rem; height: 0.89rem; font-size: 0.75rem; left: 18.71rem; top: 0.40rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    as</span>
                <span class="position style"
                    style="width: 0.65rem; height: 0.89rem; font-size: 0.75rem; left: 19.53rem; top: 0.40rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    m</span>
                <span class="position style"
                    style="width: 1.69rem; height: 0.89rem; font-size: 0.75rem; left: 20.17rem; top: 0.40rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">y/our</span>
                <span class="position style"
                    style="width: 3.64rem; height: 0.89rem; font-size: 0.75rem; left: 22.04rem; top: 0.40rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    acceptance</span>
                <span class="position style"
                    style="width: 0.63rem; height: 0.89rem; font-size: 0.75rem; left: 25.84rem; top: 0.40rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    of</span>
                <span class="position style"
                    style="width: 1.05rem; height: 0.89rem; font-size: 0.75rem; left: 26.64rem; top: 0.40rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    the</span>
                <span class="position style"
                    style="width: 0.37rem; height: 0.89rem; font-size: 0.75rem; left: 27.83rem; top: 0.40rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    T</span>
                <span class="position style"
                    style="width: 0.61rem; height: 0.89rem; font-size: 0.75rem; left: 28.15rem; top: 0.40rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">er</span>
                <span class="position style"
                    style="width: 0.94rem; height: 0.89rem; font-size: 0.75rem; left: 28.76rem; top: 0.40rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">ms</span>
                <span class="position style"
                    style="width: 1.21rem; height: 0.89rem; font-size: 0.75rem; left: 29.88rem; top: 0.40rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    and</span>
                <span class="position style"
                    style="width: 0.45rem; height: 0.89rem; font-size: 0.75rem; left: 31.26rem; top: 0.40rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    C</span>
                <span class="position style"
                    style="width: 2.98rem; height: 0.89rem; font-size: 0.75rem; left: 31.70rem; top: 0.40rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">onditions</span>
                <span class="position style"
                    style="width: 3.25rem; height: 0.89rem; font-size: 0.75rem; left: 34.85rem; top: 0.40rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    applicable</span>
                <span class="position style"
                    style="width: 0.24rem; height: 0.89rem; font-size: 0.75rem; left: 38.28rem; top: 0.40rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    t</span>
                <span class="position style"
                    style="width: 0.42rem; height: 0.89rem; font-size: 0.75rem; left: 38.52rem; top: 0.40rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">o</span>
                <span class="position style"
                    style="width: 1.05rem; height: 0.89rem; font-size: 0.75rem; left: 39.11rem; top: 0.40rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    the</span>
                <span class="position style"
                    style="width: 0.36rem; height: 0.89rem; font-size: 0.75rem; left: 40.33rem; top: 0.40rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    F</span>
                <span class="position style"
                    style="width: 2.30rem; height: 0.89rem; font-size: 0.75rem; left: 40.66rem; top: 0.40rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">aisaNet</span>
                <span class="position style"
                    style="width: 1.21rem; height: 0.89rem; font-size: 0.75rem; left: 43.13rem; top: 0.40rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    and</span>
                <span class="position style"
                    style="width: 0.78rem; height: 0.89rem; font-size: 0.75rem; left: 44.51rem; top: 0.40rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    an</span>
                <span class="position style"
                    style="width: 0.35rem; height: 0.89rem; font-size: 0.75rem; left: 45.29rem; top: 0.40rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">y</span>
            </p>
            <img src="data:image/png;base64,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"
                class="image"
                style="width: 12.48rem; height: 0.58rem; display: block; z-index: 0; left: 7.33rem; top: 34.38rem;" />
            <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAoAAAAKCAYAAACNMs+9AAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAAGElEQVQYlWOMiIz+z0AEYCJG0ahC6ikEAFFSAh9YFx1pAAAAAElFTkSuQmCC"
                class="image"
                style="width: 0.40rem; height: 0.37rem; display: block; z-index: 0; left: 6.25rem; top: 35.38rem;" />
            <p class="paragraph body-text"
                style="width: 46.50rem; height: 2.66rem; font-size: 1.10rem; left: 6.25rem; top: 34.26rem; text-align: left; font-family: pro, serif; font-weight: 300;">
                <span class="position" style="width: 1.01rem; height: 0.89rem; left: 1.04rem; top: 0.88rem;">
                    <span class="style"></span>
                    <span class="style"
                        style="font-family: 'pro', serif; font-size: 1.00rem; transform: ScaleX(1.05);">
                    </span>
                    <span class="style"
                        style="font-family: 'pro', serif; font-size: 0.75rem; color: #58595b; transform: ScaleX(1.05);">I/w</span>
                </span>
                <span class="position style"
                    style="width: 0.38rem; height: 0.89rem; font-size: 0.75rem; left: 2.05rem; top: 0.88rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">e</span>
                <span class="position style"
                    style="width: 0.78rem; height: 0.89rem; font-size: 0.75rem; left: 2.61rem; top: 0.88rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    ha</span>
                <span class="position style"
                    style="width: 0.36rem; height: 0.89rem; font-size: 0.75rem; left: 3.38rem; top: 0.88rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">v</span>
                <span class="position style"
                    style="width: 0.38rem; height: 0.89rem; font-size: 0.75rem; left: 3.73rem; top: 0.88rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">e</span>
                <span class="position style"
                    style="width: 1.05rem; height: 0.89rem; font-size: 0.75rem; left: 4.29rem; top: 0.88rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    the</span>
                <span class="position style"
                    style="width: 0.23rem; height: 0.89rem; font-size: 0.75rem; left: 5.51rem; top: 0.88rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    r</span>
                <span class="position style"
                    style="width: 1.62rem; height: 0.89rem; font-size: 0.75rem; left: 5.73rem; top: 0.88rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">equir</span>
                <span class="position style"
                    style="width: 0.81rem; height: 0.89rem; font-size: 0.75rem; left: 7.35rem; top: 0.88rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">ed</span>
                <span class="position style"
                    style="width: 2.46rem; height: 0.89rem; font-size: 0.75rem; left: 8.33rem; top: 0.88rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    mandat</span>
                <span class="position style"
                    style="width: 0.38rem; height: 0.89rem; font-size: 0.75rem; left: 10.78rem; top: 0.88rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">e</span>
                <span class="position style"
                    style="width: 0.24rem; height: 0.89rem; font-size: 0.75rem; left: 11.34rem; top: 0.88rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    t</span>
                <span class="position style"
                    style="width: 0.42rem; height: 0.89rem; font-size: 0.75rem; left: 11.58rem; top: 0.88rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">o</span>
                <span class="position style"
                    style="width: 2.07rem; height: 0.89rem; font-size: 0.75rem; left: 12.17rem; top: 0.88rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    operat</span>
                <span class="position style"
                    style="width: 0.38rem; height: 0.89rem; font-size: 0.75rem; left: 14.24rem; top: 0.88rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">e</span>
                <span class="position style"
                    style="width: 0.65rem; height: 0.89rem; font-size: 0.75rem; left: 14.79rem; top: 0.88rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    m</span>
                <span class="position style"
                    style="width: 1.69rem; height: 0.89rem; font-size: 0.75rem; left: 15.43rem; top: 0.88rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">y/our</span>
                <span class="position style"
                    style="width: 3.28rem; height: 0.89rem; font-size: 0.75rem; left: 17.30rem; top: 0.88rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    account(s)</span>
                <span class="position style"
                    style="width: 2.47rem; height: 0.89rem; font-size: 0.75rem; left: 20.74rem; top: 0.88rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    opened</span>
                <span class="position style"
                    style="width: 0.60rem; height: 0.89rem; font-size: 0.75rem; left: 23.39rem; top: 0.88rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    at</span>
                <span class="position style"
                    style="width: 1.05rem; height: 0.89rem; font-size: 0.75rem; left: 24.16rem; top: 0.88rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    the</span>
                <span class="position style"
                    style="width: 1.51rem; height: 0.89rem; font-size: 0.75rem; left: 25.38rem; top: 0.88rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    Bank</span>
                <span class="position style"
                    style="width: 1.09rem; height: 0.89rem; font-size: 0.75rem; left: 27.06rem; top: 0.88rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    link</span>
                <span class="position style"
                    style="width: 0.81rem; height: 0.89rem; font-size: 0.75rem; left: 28.16rem; top: 0.88rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">ed</span>
                <span class="position style"
                    style="width: 0.24rem; height: 0.89rem; font-size: 0.75rem; left: 29.14rem; top: 0.88rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    t</span>
                <span class="position style"
                    style="width: 0.42rem; height: 0.89rem; font-size: 0.75rem; left: 29.38rem; top: 0.88rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">o</span>
                <span class="position style"
                    style="width: 0.36rem; height: 0.89rem; font-size: 0.75rem; left: 29.97rem; top: 0.88rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    F</span>
                <span class="position style"
                    style="width: 2.44rem; height: 0.89rem; font-size: 0.75rem; left: 30.30rem; top: 0.88rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">aisaNet.</span>
                <span class="position style"
                    style="width: 0.17rem; height: 0.89rem; font-size: 0.75rem; left: 32.91rem; top: 0.88rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    I</span>
                <span class="position style"
                    style="width: 0.79rem; height: 0.89rem; font-size: 0.75rem; left: 33.25rem; top: 0.88rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    ag</span>
                <span class="position style"
                    style="width: 0.23rem; height: 0.89rem; font-size: 0.75rem; left: 34.03rem; top: 0.88rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">r</span>
                <span class="position style"
                    style="width: 0.76rem; height: 0.89rem; font-size: 0.75rem; left: 34.26rem; top: 0.88rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">ee</span>
                <span class="position style"
                    style="width: 1.27rem; height: 0.89rem; font-size: 0.75rem; left: 35.19rem; top: 0.88rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    that</span>
                <span class="position style"
                    style="width: 0.65rem; height: 0.89rem; font-size: 0.75rem; left: 36.63rem; top: 0.88rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    m</span>
                <span class="position style"
                    style="width: 0.35rem; height: 0.89rem; font-size: 0.75rem; left: 37.27rem; top: 0.88rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">y</span>
                <span class="position style"
                    style="width: 0.36rem; height: 0.89rem; font-size: 0.75rem; left: 37.79rem; top: 0.88rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    F</span>
                <span class="position style"
                    style="width: 2.30rem; height: 0.89rem; font-size: 0.75rem; left: 38.12rem; top: 0.88rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">aisaNet</span>
                <span class="position style"
                    style="width: 2.56rem; height: 0.89rem; font-size: 0.75rem; left: 40.59rem; top: 0.88rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    account</span>
                <span class="position style"
                    style="width: 1.07rem; height: 0.89rem; font-size: 0.75rem; left: 43.33rem; top: 0.88rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    will</span>
                <span class="position style"
                    style="width: 1.36rem; height: 0.89rem; font-size: 0.75rem; left: 44.57rem; top: 0.88rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    only</span>
                <span class="position style"
                    style="width: 0.82rem; height: 0.89rem; font-size: 0.75rem; left: 1.05rem; top: 1.77rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    be</span>
                <span class="position style"
                    style="width: 1.52rem; height: 0.89rem; font-size: 0.75rem; left: 2.03rem; top: 1.77rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    used</span>
                <span class="position style"
                    style="width: 2.05rem; height: 0.89rem; font-size: 0.75rem; left: 3.73rem; top: 1.77rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    subjec</span>
                <span class="position style"
                    style="width: 0.24rem; height: 0.89rem; font-size: 0.75rem; left: 5.79rem; top: 1.77rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">t</span>
                <span class="position style"
                    style="width: 0.24rem; height: 0.89rem; font-size: 0.75rem; left: 6.20rem; top: 1.77rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    t</span>
                <span class="position style"
                    style="width: 0.42rem; height: 0.89rem; font-size: 0.75rem; left: 6.44rem; top: 1.77rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">o</span>
                <span class="position style"
                    style="width: 1.05rem; height: 0.89rem; font-size: 0.75rem; left: 7.04rem; top: 1.77rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    the</span>
                <span class="position style"
                    style="width: 0.36rem; height: 0.89rem; font-size: 0.75rem; left: 8.26rem; top: 1.77rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    F</span>
                <span class="position style"
                    style="width: 2.30rem; height: 0.89rem; font-size: 0.75rem; left: 8.58rem; top: 1.77rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">aisaNet</span>
                <span class="position style"
                    style="width: 0.37rem; height: 0.89rem; font-size: 0.75rem; left: 11.03rem; top: 1.77rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    T</span>
                <span class="position style"
                    style="width: 0.61rem; height: 0.89rem; font-size: 0.75rem; left: 11.34rem; top: 1.77rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">er</span>
                <span class="position style"
                    style="width: 0.94rem; height: 0.89rem; font-size: 0.75rem; left: 11.96rem; top: 1.77rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">ms</span>
                <span class="position style"
                    style="width: 1.21rem; height: 0.89rem; font-size: 0.75rem; left: 13.07rem; top: 1.77rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    and</span>
                <span class="position style"
                    style="width: 0.45rem; height: 0.89rem; font-size: 0.75rem; left: 14.45rem; top: 1.77rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    C</span>
                <span class="position style"
                    style="width: 2.98rem; height: 0.89rem; font-size: 0.75rem; left: 14.90rem; top: 1.77rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">onditions</span>
                <span class="position style"
                    style="width: 1.21rem; height: 0.89rem; font-size: 0.75rem; left: 18.05rem; top: 1.77rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    and</span>
                <span class="position style"
                    style="width: 1.70rem; height: 0.89rem; font-size: 0.75rem; left: 19.43rem; top: 1.77rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    other</span>
                <span class="position style"
                    style="width: 3.25rem; height: 0.89rem; font-size: 0.75rem; left: 21.30rem; top: 1.77rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    applicable</span>
                <span class="position style"
                    style="width: 0.24rem; height: 0.89rem; font-size: 0.75rem; left: 24.73rem; top: 1.77rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    t</span>
                <span class="position style"
                    style="width: 0.61rem; height: 0.89rem; font-size: 0.75rem; left: 24.97rem; top: 1.77rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">er</span>
                <span class="position style"
                    style="width: 0.94rem; height: 0.89rem; font-size: 0.75rem; left: 25.59rem; top: 1.77rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">ms</span>
                <span class="position style"
                    style="width: 1.21rem; height: 0.89rem; font-size: 0.75rem; left: 26.70rem; top: 1.77rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    and</span>
                <span class="position style"
                    style="width: 3.33rem; height: 0.89rem; font-size: 0.75rem; left: 28.08rem; top: 1.77rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    conditions</span>
                <span class="position style"
                    style="width: 3.15rem; height: 0.89rem; font-size: 0.75rem; left: 31.58rem; top: 1.77rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    published</span>
                <span class="position style"
                    style="width: 0.43rem; height: 0.89rem; font-size: 0.75rem; left: 34.90rem; top: 1.77rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    b</span>
                <span class="position style"
                    style="width: 0.35rem; height: 0.89rem; font-size: 0.75rem; left: 35.33rem; top: 1.77rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">y</span>
                <span class="position style"
                    style="width: 1.05rem; height: 0.89rem; font-size: 0.75rem; left: 35.85rem; top: 1.77rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    the</span>
                <span class="position style"
                    style="width: 1.51rem; height: 0.89rem; font-size: 0.75rem; left: 37.07rem; top: 1.77rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    Bank</span>
                <span class="position style"
                    style="width: 0.14rem; height: 0.89rem; font-size: 0.75rem; left: 38.60rem; top: 1.77rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">,</span>
                <span class="position style"
                    style="width: 0.65rem; height: 0.89rem; font-size: 0.75rem; left: 38.91rem; top: 1.77rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    as</span>
                <span class="position style"
                    style="width: 3.05rem; height: 0.89rem; font-size: 0.75rem; left: 39.74rem; top: 1.77rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    amended</span>
                <span class="position style"
                    style="width: 0.44rem; height: 0.89rem; font-size: 0.75rem; left: 42.96rem; top: 1.77rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    fr</span>
                <span class="position style"
                    style="width: 1.06rem; height: 0.89rem; font-size: 0.75rem; left: 43.39rem; top: 1.77rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">om</span>
                <span class="position style"
                    style="width: 1.44rem; height: 0.89rem; font-size: 0.75rem; left: 44.62rem; top: 1.77rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    time</span>
            </p>
            <div class="group" style="width: 0.40rem; height: 0.37rem; display: block; left: 6.25rem; top: 38.03rem;">
                <svg viewbox="0.000000, 0.000000, 4.000000, 3.700000" class="graphic"
                    style="width: 0.40rem; height: 0.37rem; display: block; z-index: -10; left: 0.00rem; top: 0.00rem;">
                    <path fill="#58595b" fill-opacity="1.000000" d="M 3.994 0 L 0 0 L 0 3.694 L 3.994 3.694 L 3.994 0 Z"
                        stroke="none" />
                </svg>
            </div>
            <div class="group" style="width: 40.65rem; height: 1.58rem; display: block; left: 7.29rem; top: 37.09rem;">
                <svg viewbox="0.000000, 0.000000, 23.550000, 5.300000" class="graphic"
                    style="width: 2.35rem; height: 0.53rem; display: block; z-index: -10; left: 0.02rem; top: 0.00rem;">
                    <path fill="#58595b" fill-opacity="1.000000"
                        d="M 23.38 4.568 L 23.002 4.568 L 22.868 4.717 L 22.86 5.111 L 22.994 5.276 L 23.38 5.276 L 23.506 5.111 L 23.506 4.717 L 23.38 4.568 Z"
                        stroke="none" />
                    <path fill="#58595b" fill-opacity="1.000000"
                        d="M 20.724 1.323 L 20.046 1.46566 L 19.5071 1.874 L 19.1514 2.51859 L 19.023 3.37 L 19.1458 4.13542 L 19.4974 4.73612 L 20.0528 5.12852 L 20.787 5.269 L 21.448 5.269 L 21.834 5.103 L 21.976 5.024 L 21.9505 4.946 L 20.834 4.946 L 20.2764 4.85202 L 19.818 4.55487 L 19.5073 4.0318 L 19.393 3.26 L 22.181 3.26 L 22.1889 2.961 L 19.409 2.961 L 19.4724 2.433 L 19.479 2.378 L 19.842 1.638 L 21.5969 1.638 L 21.4756 1.51353 L 20.724 1.323 Z"
                        stroke="none" />
                    <path fill="#58595b" fill-opacity="1.000000"
                        d="M 21.882 4.73612 L 21.669 4.835 L 21.385 4.946 L 21.9505 4.946 L 21.882 4.73612 Z"
                        stroke="none" />
                    <path fill="#58595b" fill-opacity="1.000000"
                        d="M 21.5969 1.638 L 21.6588 1.874 L 21.796 2.378 L 21.803 2.961 L 22.1889 2.961 L 22.1407 2.53547 L 21.924 1.9735 L 21.5969 1.638 Z"
                        stroke="none" />
                    <path fill="#58595b" fill-opacity="1.000000"
                        d="M 13.207 1.409 L 12.852 1.409 L 12.8677 1.60462 L 12.876 1.709 L 12.884 5.19 L 13.262 5.19 L 13.262 2.733 L 13.2804 2.63 L 13.286 2.599 L 13.3498 2.394 L 13.4443 2.10052 L 13.4513 2.079 L 13.23 2.079 L 13.2246 1.921 L 13.2151 1.646 L 13.207 1.409 Z"
                        stroke="none" />
                    <path fill="#58595b" fill-opacity="1.000000"
                        d="M 15.3693 1.646 L 14.979 1.646 L 15.275 2.10052 L 15.302 2.15 L 15.302 5.19 L 15.68 5.19 L 15.6876 2.599 L 15.711 2.504 L 15.743 2.394 L 15.8334 2.15 L 15.562 2.15 L 15.396 1.669 L 15.3693 1.646 Z"
                        stroke="none" />
                    <path fill="#58595b" fill-opacity="1.000000"
                        d="M 17.6465 1.646 L 17.389 1.646 L 17.72 2.15 L 17.72 5.19 L 18.098 5.19 L 18.098 2.961 L 17.9649 2.15 L 17.9568 2.10052 L 17.6465 1.646 Z"
                        stroke="none" />
                    <path fill="#58595b" fill-opacity="1.000000"
                        d="M 16.861 1.323 L 16.46 1.323 L 16.207 1.433 L 15.822 1.756 L 15.68 1.921 L 15.577 2.15 L 15.8334 2.15 L 15.892 1.992 L 16.263 1.646 L 17.6465 1.646 L 17.6182 1.60462 L 17.2104 1.37742 L 16.861 1.323 Z"
                        stroke="none" />
                    <path fill="#58595b" fill-opacity="1.000000"
                        d="M 14.995 1.323 L 13.853 1.323 L 13.49 1.646 L 13.246 2.079 L 13.4513 2.079 L 13.459 2.055 L 13.861 1.646 L 15.3693 1.646 L 14.995 1.323 Z"
                        stroke="none" />
                    <path fill="#58595b" fill-opacity="1.000000"
                        d="M 11.615 1.409 L 11.237 1.409 L 11.237 5.189 L 11.615 5.189 L 11.615 1.409 Z"
                        stroke="none" />
                    <path fill="#58595b" fill-opacity="1.000000"
                        d="M 11.599 2.57572e-14 L 11.253 2.57572e-14 L 11.119 0.157 L 11.119 0.504 L 11.237 0.653 L 11.607 0.653 L 11.725 0.504 L 11.725 0.157 L 11.599 2.57572e-14 Z"
                        stroke="none" />
                    <path fill="#58595b" fill-opacity="1.000000"
                        d="M 9.322 1.717 L 8.944 1.717 L 8.944 4.489 L 9.007 4.812 L 9.18 5.009 L 9.322 5.19 L 9.55 5.276 L 10.054 5.276 L 10.228 5.237 L 10.3357 5.19 L 10.3554 5.19 L 10.3248 5.009 L 10.3153 4.953 L 9.472 4.953 L 9.322 4.654 L 9.322 1.717 Z"
                        stroke="none" />
                    <path fill="#58595b" fill-opacity="1.000000"
                        d="M 10.306 4.898 L 10.212 4.93 L 10.07 4.953 L 10.3153 4.953 L 10.306 4.898 Z" stroke="none" />
                    <path fill="#58595b" fill-opacity="1.000000"
                        d="M 10.401 1.409 L 8.306 1.409 L 8.306 1.717 L 10.401 1.717 L 10.401 1.409 Z" stroke="none" />
                    <path fill="#58595b" fill-opacity="1.000000"
                        d="M 9.322 0.575 L 8.944 0.716 L 8.944 1.409 L 9.322 1.409 L 9.322 0.575 Z" stroke="none" />
                    <path fill="#58595b" fill-opacity="1.000000"
                        d="M 4.357 1.323 L 3.63775 1.46394 L 3.66947 1.46394 L 3.11162 1.84288 L 2.71661 2.47261 L 2.58121 3.26 L 2.57708 3.284 L 2.569 3.331 L 2.70239 4.13314 L 3.06912 4.74638 L 3.61905 5.13817 L 4.302 5.276 L 4.94061 5.15928 L 5.25948 4.961 L 4.326 4.961 L 3.78484 4.83559 L 3.347 4.48875 L 3.05391 3.96453 L 2.9509 3.331 L 2.95345 3.26 L 3.02563 2.73445 L 3.0298 2.70408 L 3.28463 2.16838 L 3.72114 1.78473 L 4.349 1.638 L 5.31704 1.638 L 5.0735 1.46394 L 4.357 1.323 Z"
                        stroke="none" />
                    <path fill="#58595b" fill-opacity="1.000000"
                        d="M 5.31704 1.638 L 4.349 1.638 L 4.98744 1.79773 L 5.417 2.20112 L 5.65906 2.73445 L 5.73168 3.26 L 5.72733 3.331 L 5.62004 3.96453 L 5.32194 4.48875 L 4.87659 4.83559 L 4.326 4.961 L 5.25948 4.961 L 5.52362 4.79675 L 5.94858 4.16984 L 6.10017 3.331 L 6.10866 3.284 L 6.113 3.26 L 5.98607 2.47261 L 5.628 1.86025 L 5.31704 1.638 Z"
                        stroke="none" />
                    <path fill="#58595b" fill-opacity="1.000000"
                        d="M 1.016 1.717 L 0.638 1.717 L 0.638 4.489 L 0.701 4.812 L 0.875 5.009 L 1.016 5.19 L 1.245 5.276 L 1.749 5.276 L 1.922 5.237 L 2.02967 5.19 L 2.04932 5.19 L 2.01937 5.009 L 2.0101 4.953 L 1.166 4.953 L 1.016 4.654 L 1.016 1.717 Z"
                        stroke="none" />
                    <path fill="#58595b" fill-opacity="1.000000"
                        d="M 2.001 4.898 L 1.906 4.93 L 1.765 4.953 L 2.0101 4.953 L 2.001 4.898 Z" stroke="none" />
                    <path fill="#58595b" fill-opacity="1.000000"
                        d="M 2.095 1.409 L -3.55271e-15 1.409 L -3.55271e-15 1.717 L 2.095 1.717 L 2.095 1.409 Z"
                        stroke="none" />
                    <path fill="#58595b" fill-opacity="1.000000"
                        d="M 1.016 0.575 L 0.638 0.716 L 0.638 1.409 L 1.016 1.409 L 1.016 0.575 Z" stroke="none" />
                </svg>
                <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAABDwAAAAUCAYAAABh2i2rAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAgAElEQVR4nOx9dXgTS/f/bLTelAp190KpABVciru73nvRe7GL6+XidpEW16LFKRQt1Ki7UHdJ2qZp0qTxZPf3R1nYLkmaIpf3/X3fz/PkebKzszOzOzPnnDlzzhkIQRCgLjIzM0dXVlZ5T5w4YY/aD30lYBgmtrS0mMAwQgQAAC0tzRZNTU3ej64XAAAOHDz04o/fV87Q0tJq+TfqU4QTJ0+FzZ41c72RkVE1AACkpqVNqGfUO48dO+bQj6qTwWA4P3r8ZOvyZUvn/6g6fgREIpH2sWPHH2/ZsinoZ7fl/yLevIlcRtWg8vv36xf6s9vyn4iWlhaTO2F39xcVFfWVyeSUZUuXzHdzc41V9/nDh48+XbL0t4V6urpNP7Kd/xcAwzCRy+MZyWVyMpVKEWhpabUQCAT5z26XKjx4+GiHo4N9So8ePV6qyieRSDR4vFYj9FpPT5dJJpPFP76F/z0oLCrqm56eMW72rJkbfnZbKioqfKJjYhctXDB/5c9uy89EeXmFb2xs7IIFC+b//rPbgsXjx0+2WFtb5/j4eD/72W35HzqPR48eb7O1tcn09vaO+Nlt+dE4ePDw8xUrls/W0dFmq8qXlZU98vHjJ1u5PK6xnp5+484d2/pDEKT+IvAbUVZW3uv9+/g58+fPXfVv1fnfAj6fT3vx8tXqoqKivkxmky2VQhGs/H3FTCtLyw/fWvadsLv7PLt3f+3u7hb9HZr6HwGxWKzV2srvQiBAsJaWFodCoQg7Gsuktev+LMImCARCfYlErEWj0RjY9OXLls1t4XJNGPX1Tj+i8Vjk5OYGhYScuWlmZlZEIhKlAAAgk8koFpYW+YsXLVz2owXU0tJSf7lcTvqRdWCRmpY2ITUlbdKSJb8uIhKJMgAAqKio9JVKpRponhZOi2l9Q4Pjj2yHWCzWrqqq9vqRdfwIwDBCLC4pCfjZ7fi/iqamJhtNTU3uz27HfyJaW/kGN27cPLZixfI5AAAAwzABQRBCZ8ooKyvrLZfJyD+mhZ+xZs26kvXr/xxjbm5W1HHu74uoqOjFVVVVXt9r0dPc3GyxdduOtDOng83QtKvXQk8mJibNMDY2rqBSKYKWlpauTGaTrbm5eaGXV4/nw4cFBdNotPrvUf/3RD2D4WxsbFSpKs/Va6Enk5NTppiadi0lEtp4CIFIkLm5usZOmDB+778p1P4ng9/K71JXV+f+o8ovLCrq+/Llq1W//rL4V21tbY6qvAKBUL+muqb7j2rLfwsEQoF+dU3tf9x3qK9vcNLX12/42e34H74OjPp6J4MuBnX/Vn0CgUD/wsVL54OChp52d3OL+bfqBQCA0rIyPxhWvWZJTEqaJpVINXft2tEXAABkMhn53+YLfAGfVlNb0+3frPM/DZlZWaPi4xNmL/nt10XohoRIJNJ+/SZyxZTJk3ah+SorK72NDA2rv0eddXV1bnZ2tunfoyxVkEgkmufOX7jUv1/f0I42aNTF7dt3DmhqanInTBi/DwAAuDye0aZNW7LJZLLIsEuXWhiGiQ2NjQ5SqUTD2dk5fkD//ld79er5SJGegHTs6BEXbEJ0TMzC/PyCgYp2+Wvraj2+xwt0BKFQqOfs7BS/bu2aidj0iooKn2uh10/8/7YjUlpa5peQmDhj8eKFS1GFx//QaUD/doUymYxCIpEk/3a9PxP/F9/5W5CbmzuMSqXy0WsCgQADAGBFef8vf9v8goKBeXkfhv7IXV42m22+YMG8lQH+/nfRNLlcTmpkMu0iIp6vW/fnhsLly5fO9fXxefqj2vDVQFTTN35ra5eZM6dvwltZpaWlj3/yJHwzKiz8DwAgHXzLb0F1dY1ncnLKlFkzZ2zAKjz+L8/t7wUYhokIgkDfW0ZCEASSy+Xk//XPfyf+U/qPz+cbJCUlT3N1cYn7txUe6uDN68gV8+fP/QO9JpFI0h9Z3/9onmKUl1f0TExMmr5g/vyVqMIjKjr6Fxtr62xsPltb28yf08Kvh0gs1k5MTJpuZWmZ970UHnjIZTIyLJeTToSccsCmCwRCvZKS4sDrN24ee/MmcvmqVb9P1dXVZWHzdGqn8WfDzs4uIzEhcQaXyzX+2W35npg6ZfLOc2dPm1CpVMHPbsv/oB6ePn22IeL5i7U/ux3/JkJDrx9PTEya/rPb8d8EVnOzJUWNeS2TySgrf1/1XbT5/4349ZfFvx49ctil45zfF0QiUWZmalryy+JFSzduXD8yJOT0zdS0tAn/djt+FHr29H2SkZk1prGx0e5nt+X/AoYMHnT+wvmzhiYmJhVoWnpGxtgLFy+d/5nt+o8HgnSohNq2fUdKc3Oz5feuOjj49K38/IKB37vc/+HfwclTwXcKCgr7/+x2GBsbV144f9Zw6NAhZ39G/UgHc0hdWeR7IDU1beKly1fO/Bt1/bdh/Lix+8+fO2OEdT8qLyvvpaGp8a+EbPiR0NPVbTp/7qzRmDGjD//bdWtpaXJ79Ojx8sD+fV5m5mZFe/cdiOTz+TRsnq9220AQBOJwOGZUKpWvTqwLgUCgr6mpyf0eJlRSqYyKvZbL5SQ2m22OTTM0NKxRVJdAINQTCPg0CpUq0NXRYf0nmPqSSCTJ99SEIggC8fl8AwiCYLxZLQzDBJFIrKOl9eNdECQSiQaXyzVBr4lEoszAwICuKC+XxzOSiMVaWlpaLeqMJzS/tnYb0eiI2KMQCoW6fD7fAL2mUChCPT09prL8AoFAXyAQ6Ovp6TVSKBQRtn4dHZ1mder8WrDZbDO5XE7W09NjUigUYUf5hUKhLplMFnWkuZfJZBQOh2NKIBDkNBqtXl0XMS6P16GiEYZhApvNttDR0WF1pMBDEAQSCoV6PypWTktLiwnqFqajo9OsoaHRqiifXC4nSaVSDWX3VQGGYaJIJNL51ndAEARqbW017CifUCjUFYlEuvr6+g0d9ZtUKqUiCEJQZ+x8DWQyGUUqlVLVia0klUqpLS0tXQEAwMDAgI7fpaVQKCIKBYgUPYvSESKRKP343gotZL4Vzk5OiWPHjj145szZ0O4hwWYaGhp8ZXlR/od1fcTTCBTouxOJRJm+vn59R+3Hvq+BgQEDre+rXwxBIDabY44uwtXhl62tfAORSKgLAACamppcPB9Rd259C9r4mICmra3FUcWnUX4HAITgfdgRBIHYbLY5DMPET+5K3/ItOwCRSJTh+QKfzzeA1XCR5bS0dCURidLO8hUYhgkCgYBGIBDkeDoEwzBRLBZrdTRHP45nU7lcTlbU3yhQ3oFeQxABNjTsUqsob2trG59V1Cfo3OmMywiX2zH/kUgkGgiCEDqzecTldbyBhs4ZZXMci6+RsWAYJnA4HDMYholoGo1GYyjj5TAME9lsjhmJRJTo6+s3KiuXx+MZisVibUU0F4COeTAq/wAAAJVK5eN3TFE5DAAAtLS0Oeq8c2fGkDrgqTEuOtN/6tIdANriLiAIQtDW1mZDEIR8q0woEAj01ZEl+Hw+TSgU6mloaPI6ituhLmQyGVkikWp21IcymYwsEol0SSSSRBnd5/P5BtixrAydoXmdXUO2trZ2EYlEOgAAoGpd8flbarSq0w6RSKT9cc3WoYUMykexc5lEIklJJFI7+spqZn+VIrej+a0u+Hw+TVNTk6uObIXOeWVrJ1XjkcvlGkskEk11acXXgEQiSebMnrVu/YZNH+7ff/gXNl7MVyk8ysrKe7169fr3+o/xPFxcXeKGDB503tTUtBSbTy6Xk3Jz84KSkpKnsZpZVkZGRlW+Pj7h3t5eEd/SORAEPg348PCnG2Pj4ubr6uo2QaBtItDpdNft27cOtLCwKACgLSjW69dvVvr59br/4OHjHRQyWQQAAJaWFh/69esX6uTkmKSqPplMRg45feZG927dIgcPHnQBf/9ZxPN1CAwT8QFFmUym7V+798T+vXuXP36hH/H8+VoYRohjx4w+XFxSEhAR8XzdmtWrpnztNwGgzR1JJBLrNDY22mdkZI41MjKs2rZ1yxAA2hZKiYlJM7JzcoYLBUJ9GxvrrICAgDv29nYK/boEAqHejp07k3b/tStAEaG4ei30pL2dXXr//v2u4e/BMEy4cPHShfLyip7aWlqfJjadwXAJPnXCGlXuvH8fP7u5udmSSqXy496/n0chty3MXFyc3/fr1y8UH0sAhmFCxPMX66Kion7R0tJq0dTU5IpFYu0ZM6dv6ujb8Pl82unTZ6+zOWxzDepnIi0UiXT379vjg80rkUg07z94uCs9LX08mUIRamhotDY2NtovX7Z0npaWFufM2XPXWj4qDN6/j58DAACzZs3Y4O3l9VxR3Vu2bkvfsGH9KBpOuBMIhHrbd+xMPnrkkBuadvJU8J0xo0cffhcV9Wt1dY0nhUwWNTQ2OOjr0+oXzJ/3u6OjQwq2DARBoNjYuPlPnz3bQCSSpEKhUI+mr18/Z86sdc7OzgnYvBs3bs5Zt27N+IsXL5+XyWQUAADQ1tFuDgwIuBMQ4B+m7NtlZmWNunXrziEWi2VVUFAwIPzps40AALBs6ZL52PGTmZU1KiYmdmEzq9kSIkCwp6fnq2FBQ0PwQpJIJNLOzMwak5aWPoHL4xpbmJsX9O7d+4Gbm2uMKmb24MHDnTQajTFkyOAvdktPnz4b2r9/v2vdunm8BaDNl/7EiVN3LS0t8gEAoLGx0X7e3LmrfX19wtFn+Hw+LSYmdmFBQeEAiVSiaW9nl9a3b58bKN1QhTo63fXatdCTjY1MeyqVypeIxVoDBvS/Mm7c2IOoIuLvPXuj6urobnK5nJybmxcEAABLl/y20MHBPhVb1vkLFy8UFxf3kcvlpPUbNuUBAICRoWH1xo3rR6F5JBKp5pPwp5uys7JHymQyiq6ublPfvn1uKOo3NpttHhn5dml5eUVPiADBzk5OCQMG9L+CLp6/FaWlZb2v37jxD5fLMyYRiVIYQQijRo74Z/DgQRfw/VdHp7teuXz1NKu52dLQsEsNLIdJXU27ljo5OSbKpDLqsGFBIQAAkJKSOik/P38Q6tIik8nIO3buSlq2dMn8K1dDg9FFqj5Nv75v3z43fpTbyZjRo448ffpsQ1pa+oS+ffvcVJQnKyt75M1btw9ra2uzCRAEAwAAp4VjOmb06CMof0AQBNq0eWvWqj9+n3b58pUzqPCnp6/X2Ccw8FavXj0f4cutrKzyunr1WjCPxzMy6GJQJ5VKNbp16xb5re/Uwm1TNAHQFojxfXz8HD3dz4IKnU533blzez8zM7NiANp83/fs2fcOpb8sFstq+LBhp4YPHxYMAAAfPuQPCg45fROdWw0NjQ6LFy1Y1qNHj5e374TtNzM1LRk4cMBlbBvKyyt8T54KDjuwf28PvCLp9p2w/WZmpsUDBwy4AkCbEHf9+s1/CouK+unoaDfzeDyjHp6eL2fPnvUnduGemJg0vampyUYgEOgnJCbN0NfXb9j9185AANr4Xej1G8fz8wsGduliUEcikSQkIkni59/7nqpvFZ+QMLOstMxv3ry5q7HpIpFIZ9PmrVl//L5yOp5nxickzCwvq+g1d+7stUxmk01wSMitv3bt7CMQCPR37todz+fzDUQikQ46twcPHnhh5IgRJ9DnGQyGc8TzF2urq6p7AACAnb1d+oD+/a8o480o3ryJXEYkEmWVlZXeObl5wyzMzQvWr183FoA23vI+Pn5OXl7eUJFIpGNra5vZJzDglo2NTTuzaalUSr177/7fKSmpk2k0/XoKhSJsbeV3Wbly+awLFy5d2LVzez80782btw5nZeeM1NXR+UTPa2trPY4cPeyKBlROz8gYW1FR6UskEqXR0TGLNDU1eAf27/MCoC3I3M2bt45k5+SMMDDoUkcmk8QQBMEDBvS/ouo9Hz58tD0xKXk6h8Mx27f/4GtUfjh86MCnWAB5eR+G3Lp9+5BEItFEYIRAJBGlEydO+BvrvoZHXNz7ueFPn21kMpl2Fy5evKCh0Ta2NqxfN8bY2LgSzZeYmDQ9ISFxZktLS1cCkSjz9vaKGDF82Em8UkUoFOomJ6dMzcrOHingC2jW1lY5/v7+YXiejUd8QsLMhw8f79DT02WiMmwTi2U9f97cVVh+BQAARUXFfcLC7u5rZrMtaDQag8fjGZmbmRWtW7fmk1Vaa2trl1u37hwqKCgYoKWtxSESSVIWi2W1ZcumIAtz80IA2nhwekbGuPT0jPE8Hs/IwsIi36937/vYYNpsNtt8119/xxkbGVVBBAjmsDlmvf163586ZfJOANpiDBw8dCQCpQNMZpPt5MmTdvXr2+eGsncNvX7jn7y8vKE62p8XlrW1tR7Hjh11QhdKySkpkwsKCgcsmD/vD/zzT59FrCcSidJRI0ccb5N7ItYzmUy78xcuXkQX3xs3/DkKDfYPAAAJCYkzEhOTZqD95+Pt9WzEiOEn8RsBIpFIOyzs3r6MzIyx2to6zVwu18TZ2Slh3ry5q7CyW2FRUd/kpJSp5uZmhc8iItYDACH/HDviDEGQfM+efe8WL1641MzMrDg1LW3C3bv3vzjgAZbLSTCCEP45dsQZgDZel5OTOzwpOXkqm822MDY2rujp6/vEy6vHC/ymRmxc3Lyn4c82Ekkkia6uDksoFOpNmzp1m7LvDQAAubl5Q0Ov3zjOYrGsDh08/JxEJou7djUp+3Pd2vEAAFBVXe0ZGnr9BIvVbEWhkIVSqYwaNHTImREjhp/ALoCvXbt+wsurx/PXr9+srKPT3Xr6+jyZM2f2OmxdPB7PcPffe2P4fL6BWCzWRmleUNDQ08OChp5G89HpDJfnz5+vra6u8QQAAHsH+9SBA/pfwbtxIAgCFRYW9YuPj5/d0NjoQNPXr/fy9oro3avXQ1UBuWtr69z3Hzjw2vzjeG9qYlmPHTP6MMqfI9++WyIRi7V0dHVYL1++WqWtrc3msDlmMrmcPHr0qKNDBg86j5dl6uvrHSPfvltaU1PTnUKhCNxcXWMHDhxwGb8+EgqFumFhd/fl5OYO09DQaKWQKUJmE9N27Zo1Ex0c7FMrKip87t9/+Nf69evGVlfXdD8VHHK7sbHR/uzZ81ex7s8AQSAAQQiWzgGg3vxWB3K5nBQe/nRTbGzcfE0tTS6XyzO2sLDIX7Bg3u9mpqYl+H548yZy+YuXr1ZpaWm2aGlptXA4Laa/LF605PbtOwc3b944DKWHh48cDZ88adJfKP969y7qV4lUqiGVSjWSk1OmoOtvN3e36AH9+13FWkN+L1CpVMGECeP23r4ddnDevDmrP/UlgiDtflHR0QtDTp+5hk9H7+36a3fsnbC7e+RyOQFNh2EYOnky+LZYLNbEpt26dfuAXC4nYsuAYRi6e/febkXlo7+k5OTJR44ee4RPj4x8+9ulS5dPwzAMoWnFxSX+2LYgCAKuXQs9/ur1m+XodXl5uc/WbdtTzp47fwnbRgRBwLXQ6/80NjbaYtMW//Irm8vlGiIIAuRyOfHkqeBbN27eOoStF/srKi4O2LxlWxo+/fGT8E1btm5LffHi5R/4e5u3bEsrKyvriSAI+PAhf+Cuv3bHYu+vWr22lE6nO6PXb95ELj1/4eJ5Vd/t7dt3vx4+cvRJ+NNn67HpPB6vy+PHTzbj89fU1Lq/j4+fiV5XVFR4b9i4ORu9Pn785N3Xr98swz8nFAp1fv11SRP6jRR+k6KiQHza33v2vi0oKOiHXsfExM7fu3f/m9t3wvbhx1NwyOnrQqFQG/v8mTPnrpw8FXwLm97S0mJ8/MTJsLnzFghVfRupVEouLS3rhU9fumwFvYXLNUKvW1tbaes3bMx9+OjxVuxY4XK5hjxeqwF6fePmrUOPn4RvUlUn+luydHl9c3OzOT6dz+frL1i4mIdNO3Dg0PNz5y5cTEhMnIb9HllZ2cOXLFnWkJ9f0B8/1g8cOPScx+N1QfMWFhb1WbpsBSM5OWVSu3YsWdZw6fKVkPr6egdsekJC4vT4+IQZHb3HqeCQm7GxcXPx6bdu3T6wb//Bl2/eRC7FpgsEAt1TwSE3sfNGIpFQ74Td3YMvo7W1lRYR8XyNqvqvhV7/5+mziHWK7u0/cPBFRkbGaPR6y9ZtqSmpqRMwY1YbO14bGhrsXr16vQJfTkFBQb+cnNyhqtpRVFwcsPL3P6oKCgr6oe/G5XINDx46/OzosX8eYt/36bOIdddCr//T0beVSCTU2XPmSRWOnyXLGi5cvHS2ro7ugk2PjY2bm5SUPAWb9uFD/kDsd8DmxdITRb/Vq9eW4OvA/1JSUyesXrOuuKqqujuaxmKxLLZv35l49WroCWzeiooK71Wr15YWFhb1waaHP322ftv2HUnPIiLWfmpf3Ps5J08F38LmW/zLb83nL1w8j587r1+/WZadkxOkqp0sFsti6bIVDGzasX+O38fOK2W/rdu2pzx/8WKVsvuVVVWeQqFQB5uWlJw8WVH7L166fIbJZFpj06OiohelpaePxabl5xf0X7V6bWlJSWlvbPrjx082r169tiQqOnqhqjYHB4fciImNnYdPZzKZ1rNmz5WxWCwLdOzi+eWVK1dPvYl8uwS9PnHy1B3sPJNKpeTm5mYz9HrDhk05mZlZI9FrgUCgi9LG1NS08QcOHo7AtyM09MaxLVu3pSYkJE7HpsMwDK38/Y+qxkamDYIggMPhmPz+x+qKmJjY+ajsIBaLNW/dun1g7do/C7G0//37+FmHjxx9cu/+g13YMkUikdb6DRtzIyKer8HKH6WlZb22btuesv/AwRfKvmNTE8ty6bIVDPw3ev8+ftaWrdtSb9y4eRj/zJGjxx6lpKRORBAE1NfXO/z+x+oK7P2Y2Nh5wcEhN/DP5eV9GLRx05bMK1evnZRIJFTsvXPnL1xgczhdVfX58xcvVh0+cvQJno6x2WxTRXSyrKzcF0sT5XI54a/de6LD7t77WyaTkdD0ujq6y6HDR8LX/bk+H/t8UVFRIF4GOnkq+FZScvJk9Do1NW38oUNHnt64eesQNp9UKqVs3rIt7eGjx1uxfVJZVeW5c+df7/Hyj6LfipV/VONlNQRpk3s2btqcVV/fYI+mMRgMx3V/rs9/9Ojxlo7K3bN3X2R2ds4wfPqZM+euHDp8JBw/Zpubm80vXLx0FpvG5/P1Hz58tA1fBp3BcFI0L/H9gu//V6/fLMfzjVevXq/YtHlrRnV1dTe0H2AYhrD8vL6+3mHV6rWlUdHRC6VSKRlNZzKZ1lKplPJxPmko4sEtXK4RVla9ejX0BLYf5XI5oamJZYle7927/010dMwC9FosFmtwOBwTVe+qaAz9c/zEvdTUtPHodWxs3NxTwSE3FT1/J+zunvsPHu7Apv399953inh2yOkz1w4fOfoET/NZLJbFxUuXz2DTxGKx5oYNm3KePAnfiH4nqVRKef7ixaqly1bQsfSvoLCw7779B15dvHT5DP5d1q5bX1BdU+Oh7P1hGIZCTp+5hso6MAxDN2/dPoinN3K5nHD//oOd2LT7Dx7u+HvP3rdYeVUkEmmdPnP26vwFi1o7ohd/rFpTRmcwnLBpubl5Q/5Ytbq8tLSsF/ouHA7H5O89e9/i14Hnzl24eOzY8Qe5uXlDOppTUVHRi06fOXsVn56dkxO0ecvW9GvXQo+j3xn9nT13/hL23RAEAffvP9iJ57UIgoB79x/sUrYeQxAEHDly7DGWLkqlUgqbzTZFr1++fLXy+PGTd2/cvHUI2w4mk2m9cdPmLDxPiYt7P7uiotILX8/zFy9WYdvc3NxstmbtuqIXL17+gZ3TbDbbFOVdxcUl/lu3bU/BlrNz1+64D/n5A7BpPB6vy6LFv3KwaerMb4Xf4+ixR1g6DcMwtG//wZeXr1wNFolEWh/HHDExMWnqr78tZVZUVHhjn7985WrwiZOn7mD7gsdrNThy9NijX39bysS2Zeu2HclFxcUB6PWrV69X7Nt/4NXDh4+2YftMLpcTTp4KviUWizVUjaVbt24fwNLx5uZmsyVLljV0NAZLSkr8ZsycjWDnVqdjeNTU1HSfNHHC31jNHwRBiLW1VU5hYdGn3YCcnJzhpqamJXgNJQRBCLOpyaapqclaVT0lxSWBBw4eeoH+jh7755FQKNSbP3/eH1jNm5OTYxLeDEdfX7+BQWe08wcvKyvvNX7c2P14ra63l1dEfHzCLEVtQBAEunT5yhldHR3WrJkzNirbfXZ0cEhpbm62xJrpAdC2MzBt6pTtSckpU7HpLBbLqrW11dDOTvUOTqcBAaS0tMxvzOhRR7DJj5+EbwkI8L+Dz25hYV4Q9S76V0SJie+gwQMvvH0X9Rv+fkpq6iTPHp6v8Dv3WOCtCwAAgEaj1dMZ7fultKys95TJk3bhx5OdrW3Gh/z8wWhaYWFhv8qqKq8Vy5fNwe4Q6unpMXv37vVAWTtQkEgkKX5nHQAA9PX1GusxJw/dCbu7v09g4K2JE8bvxY4VXV1d1vcyHVQJCELYbLY5dmcKgiCkRw/PV/Pnz/vjxs2bR2EYJgAAQHFJSUBubl7QunVrxqOmeBAEIS4uzvFrVv8x+Vro9RMikUgbW7yFuXlB165dy7BpvXr1fBgTG7vwW5rN5XJN8L6rmpqaPCKRKG1qYn2a65Fv3y7t0cPzi2BG2tranJzc3GFSqZSKv/c1gGGEaGJs/ElzrKGh0c4c9/Hj8K2BgYG38M/Z29unRjx/vg6fjkImk5EvXbx8buWKFbNcXV3jUJqgq6vLWrtm9SQGo945IyNz7Pd4ByysLC3z8BZP/v5+d99FRf+CXsMwTGj7vj1e4J+3tbXJfPXq9TcFBBUIhHqhoddPbNq4fqS1tVUumt6lS5e6zZs3DktKTp5WXl7hi7bl0uUrZ35ZvGiJi4tzPLac0aNGHmWxmq06cskRCAT6Hu7u7/DWcYGBAbcjI98u+5Z3UYWuXbuWqrpvY22dgzfl1dfXb2DgaJtMJqM6OjgkY3cbAarH+Q8AACAASURBVEDb/24peg3DMPFaaOjJZUuXzMfvBo8dO+YgXyBo54eqLjgtLV2Dg0/fmjRp4u4uXbrUAdDmtvMFv6TR6rH8EobhdnOHRCJJsdZBMAwTjU0+39fU/GxW3a2bR2RJSUmA+KOZO5o/IyNj7MQJE/YkJSe344WVlZXe+vr6DcbGRlUAAHDj5q2jI0cOP96/f79r6PigUCjCmTNnbLKzt0t/+tG6DAAAAASQ/PyCgZMmTvgbW2ZExPN13Tw83o4aNfIf7BhzcLBP7YjnGhp2qTUwMKirrKxsZ/WXkJA4c9rUKdtT09InYvmhRCLRLCkuCVRE09RBdXW15/hxY/fjdyu7eXi8TUtLVxlLBgIQUlNT0z0Is3MKAACPHz/Z2icw4AvaZmtrk4WlAVFR0b8YGNDo06ZO2Y61uDU3NyuysbHJxs9PZ2fnBLwMhJe1IAhCcvPygqZNnbIdm+/16zcrbG1ssiZOGL8XW66NtXWOk5Njoqr3VAU2m23+4OHDnVu2bB7atatJOZpuampaum3rlsERz1+s+5b4NXK5nIy3ojMwMKA3NzdbYnlr+NNnG/0VWNuZdu1aGhsTt0CZjAUAAPb2dun4/tfX12v3XRsaGhyeRTz/c/u2rQOtrKzy0H6AIAhB+Tkqr86YPm3zwAEDrmBN7o2MjKpRy5jXr9+s9PH+8qhdPV3dpozMzDGo9SeMtKcDBEJ71xM8HaBQKCJV7jUAKB5DNH39ejzt/F6Qy2ES3sqnS5cudSwWy0oikWiiaY8eP9narVu3yHHjxh5EvxOJRJKMHDHixIAB/a/evhN2AM0LAQjJy/swZPLkSbs66xL/JvLtMoFAQBs5csRxAABIS0ufYGNtnY2nyQQCAabTGa7omqKhocEhOjp68fo/143FHk9PpVIFQUOHnhaLxdqgk5BIJBqXLl85s3bN6okODvap6Lvo6+s3blj/5+iiouK+Hz7kD/r0AAQQmUxGQa1ovxZVVdU9xo8ftw/vxu/u7h6VkZ4x7nO+qh4QBMGK3GakUim1tLTMT1kd+LFJIpEk2BPYIAhC0jMyxk6dMnknth1GRkbVmzZuGPHy5ctV6BpVJBLp5OblBdna2mTh6zExMSmPi4ubh16HXr9xfNSoUcdGjBh+EjunaTRavSoXWXWg7vxWBzGxsQvIZJJ4wfx5v6OWGQQCQe7v73dv5ozpm65eu34SpVnFJSUBhQWF/VeuWD4b2xc6OtrsPoGBt3g8nlFHslxVZZXX+PHj9mHnC4FAgC3MzQtKSkp/yAmbiuS4Tis8PNw93ikyJaJSqfzq6mpP9PpdVPQvjkpcRXR1dFk5ObnDVdVjY2uT9esvi39Df3Nmz/qTLxDQzpw9d62jo3FJZFK79kEQhBgaGtbgXW4AAICqQeVX19R44tMRAKCbt24fRhAEmjdv7ipVhI1AIMi9vb0isrKyP5mg19bWuWtoaLR6enq+amlp6drc3GyB3ktPzxjn17vX/e8dPwQCEGJuZlaELRdBECgzM2u0IrMhCIIQLo9rzGQybRWV183D461QKNAvL6/oiU2PjY2bP3jwoE4HYSOR2vcLgADi4uLyXtFEbRtPn/vl7buo30aNHHFckY+Zs9OXyhX12/R5LItEIp2kpORpI0YMP6HqmR8NDw/3d4rS/fx632ezOeZMZpMtAADExMQuHD5i+ElFvoTOzs4JhoaGNQWFhQM+JUIQougcbhKJJKXT6a6qhLKOoEzgp1Kp/Oqaz3QhOjp2kYP9l4qntuYRYFVMrDOAIIDglWsoRCKRdmVVlZciBRaFQhFVVFT6YoUhLMrLy3tRqVQ+fhEPACogDT+Bujl9T3j28HyFTyOTyeK6ujp3tN9QU3JFc0RXT4+ZiaFPX4Ps7OyRjg6OyXiFGQBti97BgwZeSEhoUx4zGAyXNpeML4UjAoEAd/PoWGiCIAhx93CPwqfjacP3hlgs1sa6vakDMkmheS3i7v5l+ykUirAWczRfaVlZbxKJLFY0pggEAqzugjA2Jm7Bg4ePdty+fefAiROn7q5Zs67U2toqB68Q+LLtOH4JIETlAgSCEPyGAgoNDQ2+i7NzfH7+Z0G5sLCon7WNdbaXV48XpaWl/qhfNQAApKVnjPfz87sHQJvyIDMza/TgQV+6jQLQ5m70Pv7z3IIAhCjaVHn77t2SkSNH/qOoDGcnpw55ha+vT3hmZtZo9JrH4xky6hnOnp6erwwNu9SUlZX3Qu/l5uUNdfdwj/raODk2NjbZilzNqBrt5SmFgCDEDMfvYRgmfviQP1hRmQQCQc5kNtlyPsbSiY2Lmz961Mhjioru3q3bG3Xajx87AAKIsbFxBV5GfPv23ZKRo0Yo7hNn5wTkK0/OSU5OmdKrV6+H2EUgChqNVt+7d68HySmpX+0qrIyvkclkUW1t7ac5nJqaNhFvBg5AGw0TCAX6DEa9c2fqxdOTN28ilwcFDT2tyu+9pqa2G5vNMffz631fVdkxsXEL7O3t0pTdLy8v7wkAABAACKO+3lmpXAABpXSgMyCpcE34VijtPxJZXFPzuf+io2MWjx498qiivCNHDD+ekpI6BY3XBEEA0dbWZuNdlDtCeXmF78uXL1ctXfLbQnTOvouK/sXR0SFZUX4tbS1Obt6HoQC09dmAAQOuKIpNY2dnm0Emk8Wgk/JbQUHhAGNjo0q8mxsAbXLQsGFDQ9rRWwhCzM3N1HaZUAY7O7t0RYoxDSqVX/XRrQ+ANvlW0SYlAADo6ekys7KzRyqtRI2x6ezklKiIbtNotPqePXs+QjeuMrOyRik7HlZPV4+JrvtYrGbL4uKSwEEDB1xSVe/XQt35rQ6io2IWjxk96oiiNWi/fn2v1zMYzk1NTTYAABAX937eR/emL5Qanp7dX3dYGQQhbm5uMYrk0rb1dwd87ishEol1KBSKEFtvp2N4kClkpQF/mE1tCzEA2gTe69dv/KNIEKytq3PX09NVGigSAAAoFIrA0NCwBps2fdrUbdHRMYvu3A47sGbNqskAfPbxiomJXVhHr3NDEITQxGyyCQgIaGfRoKmhPAJuYyPzix2AiGcRf0ZGvl0afOqEtTqBXHx9fMLj4t7PQ32XExITZ/bt2+cGBEGIn1/v+ykpqZNHjBh+EgAA0tLTx+N3QL4X9Gn69djrFi7XpLm52fLw4aMKfd6bmppseLxWI0UKEQKBAA8aOPDiu6ioX1HCw2Q22bDZHHM318++noogk8nIWVnZo2Lj4uaz2WxzuVxOrq9vcHR2bi9waqqITMzE9AudTncdPWqUQoZEoZDVEjZFIpF2SkrqlPiEhFlCoUhXIpFo0un0T/EzamtrPSwsLPL/zdNy8MIEBAEEq5nGgkAgyC0tLT4wGAyXrl1NymtqarsNUaF4cnR0SKbT6a7Y2CKonzIeAoFQn8/nG3xt0C0yWQVd+KiggWGY2NDQ4HD8+EmFBLuivLwnj8cz+pr6AWh/3OS8uXNW/3P85IPo6JjFQ4cOOePt5RWBKobq6xucmpqabJTNCT6fT+PxWg0VBVKrqantZqfCt97BwT7lWy0pFIGi5PvyeDwjsVisraGh0UpnMFyKi4v7KHovqUxK/ZZvCwAANbW13exUnOXu6OiQ/Pp15AoA2r6TpaXlB2V5SWSSGPVXVwVldPtHnjrCoDNcugw1qFOVh05nuMTExCzMLygYCAEIaeFyTbS1tb4I9KiMvjU3sy3QY/vodLqrtbVVjrK61F1M29raZKI7spZWVnkzZ07fiKfrCIJABYWF/aOjYxYxGAwXlF/26fM5XsnUqVO2Hzx06EV6esa4oUFDzvj17n0fSxPnz5/7x8mTwWFv375bMjRoyBmvHj1eYC0EUIWBt7d3BAAAJCQkzOzbp89NEokk8fT0fJWRkTkmMLCNP6enZ4xbs7qNl9fV1bmZmZkWK6O/1tbWORxOi6lEItFEvwkNx+/agvqKdYyMFAuoFCqlQ9ru6+MTfuHixQuTJ0/6CwAAUlJSJ/v17n0fgiAkwN8/LCk5eRpqiZOeljHer/fXC6Dq0E1VoNFo7RQbTGaTTTObbaGMtrW0tJjyeDwjfT29xrq6OncLi7b4C4rahZ+fMAwT8/I+DImJiV3YyGy0g2GY2NjItB89qv1CEd8nMpmMzGputlLmX06hdNwnylBTW9vNxflLRSEKRweH5KLi4j5fWz6ZrDzAJZPZZOvo6Jjc2trahcViWSv75g0NjQ5ttLe9hR4WlZVVXjExsQtLSkv9IQggbDbH3MrSMg+9X11d4zmhg7hF1dXVng72n3fqFUEul5OYTKbdsWPHv4gh9LEd3jxeqxEAAIwbN/bAwYOHX2zavDUrKGjo6cCAgNtYhcvsWTPXHz5y7GlCYuLMoUOHnOnp6/tEVVwFANrGUG5ublBMbNwCJpNpi46hcWPHHFT1XDt0YmH/hUIOAyaTaevgYJ/K4/EMAWiz/FCUT19fv5FGozEaGxvt0ThHBrh51xEEAoF+yOkzN5YvWzoPK2MxGAyXy1euhZCIxC82raprarqbmbWNGTqd7tpHgUUqAG2BkkkkYqcPPWjj56pkGYeU5OT2ykKaQefeWxGUyTIAAMBs+rz5SmcwXMorKn1RmQKLxsZGezcFm3coZkyftuXQ4SMRKSmpk4OChp7u1avnw3bBaiFIqZwNAADWVla56IYZg85wSUlNm1RZWeWNz8fn8w1E4jYFfnVNtaednW3G9z42G4U681sdIAgC1dbVetja2mYouk8ikSQ2traZdXV0N2Nj48ra2lqP/v2+jNX4Ma8YgDZlmKo6VZ1Aw1Sw/v4eYNQznLsYtJfjOq3wgABQ62NzOC2my5ctnaenp6fQxE1T8+sitEIfg8QB0NZx166FnqTTGa7Tpk3damtrk0kikaTPnkX8yWI1W2Ge6vQA4fP5BuPHjT1w5uy5q+vWrpnQkdKjWzePyIsXL52XyWQUIpEoTU1JnbTzY8Avfz+/u1euXg0ZMWL4SYFAoN/YyLR3cFAdyOprAEEQgh94LRyOKY1GYyxevHCpsudUnVIyYED/K+s3bMyfM3vWOk1NTd779+/nDho08KKqAS6RSDSOHvvnSZcuXWqnTp2y3cLcvIBAIMBnzp672q69negXDodjpqOjrWoxrpIJtrS0mOzbf/BNT1+fJ4sXLVxqbGxcCUEQsnXb9k8aZDaHY6alpflDTgzpDBAYUWp5RSaRxWgARC6Xa0JVcToCmUwWyTAnGv3IE4nUoQsCgUAfgiBk0aKFy7CBh7H4XiffuLq6xgWfOmGVnp4x7vXrNysvX756etmyJfM9u3d/w+FwzCwszAtUzQms+SMWXC7XRINKVfHNKSKp7Pu45bRHx33XwuGYerh7vJsyZdJOhSV8Y/9zW1pM9K2slO5qkckUkUzeZg7N5XJNNFQomQkYOq4MP+MELZFIpM1sarJxcHBUuOsGAADx8QmzHj58tGP27Fl/Tpw4YY+GhkZraWmp36XLV9sdw6du+zlsjlmH416NHXBrG+vsfv36XldaBIJAV65cDWlobHSYNnXqNhsb6ywSiSR9+vTZBg6n5ZM7prm5WdE/x4465ebmDX377t2SG9dvHpszZ/Y6NEB1Nw+Pdx/n1vjnz1+svXz5ypkVy5fPQa3HvL29nj1+Er4FQRBILpeT8j7kD5n/MQChv5/f3ci3b5cGBgbcYTKbbAgEghx1Rfg4ZpTOLQiCYCKRKJXJZBQKhSKEIAjB8xBOS4upjo52s+pTXVR/Sxsb62wul2fMaeOd9QmJiTMXLliwAgAAevfu9WD7jp3Js2bO2AAAgD7kfxg8b96cVarKUwVldFCtZwH4kt+3cEyNjIyqOuD3jVKpVEMslmgpU7gQCO3np1wuJ4WcPnNDLpeTJk6YsMfKyjKPSCTKbt66fQjB8F4IfNknPB7PiEql8tU9DawzaGlp6dou0B8OZDJZJP/oovE1UIevtbS0dNXT02tU9c11FVigoHjx8uWqmJi4BXNmz/pz5szpmygUijA9PWPcm8jIT257nBaOaUeyCYfDMetIrm5tbe1CJBKlqtqK0qIuXbrUHTiwr0dhYVG/d+/e/RYWdnffxAnj94wcOeI4BEGInZ1dxskT/9hmZ+eMjHz7bsnVq6HBvyxetERRQGYA2sbQqeCQ2xAEwRPGj9tnZWWVRyAQ5Nev31RoZfQ9oA4N7oinA/BRlkLHEQQhoBO8CUEQ6PyFixeHDB503tGxPV/hcDim6/9cO1ZZv6EBMTlsjpm2avm30+ByuSaqxgsF+87gI735irXUF1CXL3JaTKdPn7rVxtr6CwsUANosPZU9a2VllXf8n2MOObm5w96+fbfkWuj1E/Pnz/2jT2DgbTSPKotmEvmznM1paTH19/e7q8xyg/hRWcVhc8y0NH/MiYMAqDe/1YFMJqOIxRItVacVYfue28I1UaawQNfFquaZujqD743q6hpPZ5zV7FcfS9sRTExMykVisbatEq3p90BWdvbIgsKi/gf27/XCuXEQEPBtR89Nmz5tq462dvPhI8eehoc/3TRhwvh9qvJraGjwHRwcUgqLivpqaWpyu5p2LUUZh42NdXYrr9WQxWJZFRYW9fP18Qn/UcI8niAZG5tUNDc3W9JoNMbXHOdoYGDAcHN1i0lISJw5ePCgCwmJSTO2b9sySNUzL1++WqWnq8tc8tuvi7HpCIwQ1BHcFcHY2KSiicWyxvvDA9C2a9DR8zdv3T4cGOB/Z/z4cfvbtQlBCOiOgZmpWfH3MNHEAz06Fw9lBFfV+zQymXYWFuYFAABgZmpaXM+od1a2c0avo7sNHjwQax7+U49g1tbWZhMIBDmZTBKrUrKpel7ZPUWLGDKZLPb397vn7+93LykpeeqlS5fPnTj+j72JiUl5c3OzpbIdHVUwNTMtTkxMmqHsfh29zs3a2lrpbv2PhImJSXlBQeGAr3kvdWBmblbEqFduml1XV+dmZdUW28PU1LREVQwCmUxO6YzQ+G/h0eMn27y9vSKUxetpbW3tcu78hUvBwSetsGb0MIIQvtYlzMTEpDw947PvMh7q0Dd1kJGZOaaktMx/396/fdu5QSjglwQCQd6jh+erHj08X5WVlffavmNnMvZELgqFIgoI8A8LCPAPi4t7P/fS5Stn0NOmDAwMGHp6eo21dXXuTU1NNu7ublHozq+Hh/u78xcuXBIIhHqpqamTsOa5ZmZmRXQV9Le5mW2hra3NbhcVH6cwMDI0rOZwWkxhGCYqWmCr8y0hCEJ8fLyf5eTkDu/evdsboVCoh55Eoaenx+xq0rWstKzMD5bLSba2thnf4pv9rQsI/PMmJiblLBbLSg0aINXX129gsZqt0PgpWMhksnbzM+79+7mtvFbDzZs3DsPLWtgddwgCCL5P9PX1G8RisRbWMgcLGIGJnTXHR2FmZlbEYDCU0yQ6/RNN+lEwMjKq4nA4Zvr6+g2d3d1lMBjODx482nk65JQ5dgECIzABy9PMTM2K6XSGC/4UCyzMzMyKURcIZdDT02PK5XIShUIVqBOTDIIgxM3NNdbNzTW2jk533bp1e3pgn8BbqDsHiUSS+vr6hPv6+oTn5OYGnTwZHKZM4RETG7tALBJrY08fAwAABIHb0U5VfL7T40QNhaKJiUk5q7nZCrW4w9+Xy+WkpqYmG9TaAgIQ0hlF5evXb1bIZXIyGrcDi64mJuVisUSro5PhTExMylmYWGhYIAgCwTBC7Cz/MTczK8ovKBig7H5dHd2tneVhJxU934quJiblQoFQ72vlGSKRKPP28nru7eX1vKiouM+evfveYRUeqngBs7HRDj3hxcTEpLypqcmmo3aYmZkWv30X9dvXtFUdqDO/1QGZTBYbGBjQGxsb7RSFeQCgzQsD7XtTU9OSxoZGBysFFrsyNZTJP2PjSiAQ6kVEPP/z999XtJPVOx3DQ90B7+TomIT1g/2e0PpoOlxYWNTP1/dL5QFfIKB9wYQ7CQgAhEAgwMuXLZkXFR2zODc3r8OB5uvrE56dlT0yMSl5Gtb8DIIgxM/f715KauqklNTUSX5+vb7ZB0tJo78QNrS0NLkmxsYV+fkFKpUUqjB4yKDzMbFxC0pKSgKsraxyO1qsFhYV9fP19X2CT+fz+QbtiHInJoKjg31KWmraREX3qqqqvTp6vkhBmxAEgdra1CZYmJp2LRGJxTolJaX+6rZLHZibKTZlrcH4AKOAAITU1tV5KMpfW1vnzufzDdAj85ydnRLex8fPVpSXzWabFRYV9VNmtva9oQ5RgyAIcfwGuqDsO8IwTKirq3NX9SzWT9bExLhcLBJr19TUfPH9O4KDvX1qfn7+INQPHo/3cfFzvyUInzKoQ8Ps7OzSy8rKere28g2+d/0AAODk5JSYmpo6CRuQEgWCIND7+Pg56Ls7ONinVFVX91AUHwiGYcKH/PxBamj+/1VGWVVd7fn27dul06YpP+avpLTU38nJMQkfM0DAF9AA+NI9TZ167ezt0vPyPgzFBxj+1C416Js6KCws6tfT1+fJF/ySzzdQtZD4GNBOqbJckQ/6J16YmDQdywtJJJLUy8vreWZW5ujUtLSJfr0/80IjI6MqBEEIBQWF/RXV8/79+7nOTk6f5xb0pYUDmUwWW1iYF2RmZSmMV6PILFkRfH18wrOys0cmp6ROxrvH+vv73U1JTpmSkpo2yf9j/JGvxjcIgxAEKVQuaGpqcotLSjoMBOfp2f1VbFzcfEX38vI+DMGO34+ylsKx86VbZvs8BAIBtrezS09NS1OoAFW3TxTB2dkpISExcaaixYtMJqMkJSZNd8KOmU5CHb5GpVIF5ubmBejR451BUVFxX0/P7q/xu618Pt8AS09cXJzfx8cnKOT1KOzt7VLLysp6qzoQAIIgxMnJMSkzq/M82MLcvFBL60u3PRSOKqziAPg4hnp+KRe28vkGWCshVXEiFMlMqqCOQpFMJoutra2zE5OSpym6n5SUPM3a2iqnfaw09eZteXmF76vXr39fgonbgYWjmn3h4GCfkpqmWP6tr693UsSTO4Kjo2NSVlb2KD6f/0VQbJSfO+PmzrdYpHW2DHW/jVpl4XgUBABSq2QsyeVyUlpa+gQnx7YYlI6ODsm5ObnDZDIZWVUd1tbWOQ0NDY51dXVuqvJ9LdSZ3+rC2dkpQRk9KSlpi7NlZNSmCP/IJ+Ypyvvhw+eDJf5TgCAIdCcsbL+jg0MyPvRCpxUe6u5IjB8/dn90dMzi0tLS7xKEEAWCIAQ0loNAIKDhTXxkMhklPS19PJaAfgt0dXVZq/5YOf3suXNXWSyWlaq83t5ez/I+5A/58CF/sI+Pdzt/ywB/v7DMjKwxdDrD1dFRcTDX7wFFRHXGzOmbrl0LPdnS0mLyNWX28PR8xWY3W7x48Wr1oPYWAwoh4H/ZLy0tLSb5BQUDv7ZfxowZfTg+IXEWGlALi9LSUr+O4njw+XwDfJvKysp6M5lNNujuJolEks6cOX3jhYsXL3TU1xQyWYR8PC2lI7h7uEVVVlW1W7TAMEyIjY2br2gXMj+/YCD+tBKRSKQdev368Rkzpm1Gnxk5csTx4uLiPolJSe0YtVgs1rp46fK5MaNHHcEGr/tWTSuZTBYhiBJ3GzXLnj5t6tawu/f2fk0MBkdHxyQ6ne6KT09KSp4ml8vJ2LbhhXC5XP6JWZFIJOmUqVN2XLp89YxAINDvTBtMTEwq+vfrd+3K5aunJRKJBvZe3Pv4OYx6hvOwoKCQzpQJQNuOOoFAkCvbpVGn74yNjar8A/zDQq9fP94Rc/4aODs5JTo6OCbfvHnrCHaBgSAI9PTpsw1UKpWPxjPQ1tbmjB83dv/pM2dDUR9pFOkZGePwJvM/EzKZjPzkSfjmvXv2vVu2bOk8VWfZK6JtAACQlJw89WsDL5qZmpZ4efV4HhZ2bx++/+vq6tzkchkZRtSjNaqgiF9KpVJqenrGOGzbFcwdEjYNf1+GmVsofH18wnNycodXVVV7ubm1FzoC/P3DkpKSp4nFEi3sDhOBQIBnzpyx8crVqyF4hWJZWXmv128il8+cOX0jNl2RPDJ16pTtd+7c3c/lco3btVMmI7fFLen4W7q5ucaUlZX1zkjPGBcYEHAbe69nr56PcnPzggoLC/t7efV4rqwMFGQyWQQro5vfCDxdgCAImTVzxsYrV66F4OcdHlMmT9oVGfl2aU5O7jBsemsr36CouKRd3AuBQEDTxAXMFAqFullZ2SPb8XQFbkYAADBl6uQd9+892M1ms82w6XK5nESvq3NT5/uQyWQRjHP37Onr+0RLU6vl4aPH27HjEoZhwu3bYQccHB1S8ONPUblKx4Sai7OZM6ZvCr1+/Tj+pL6OwBfwv5iTCIJAycmpU7DvExQ09DSdTnd99er1SmU8wsDAgDF8WFDw+fMXLylaxKKYPm3aljt3wvYrC1aPbQf+Gv4YuFPRfblcNc9pk8E02r2rQCDUy8nJHY5VuBobG1cKBAJ9fPm1tXXuFRUVvvixolIuUROzZ81cf+dO2P76+npHbDqjvt7p7r17exbMn/cpLhekQNGqCHw+nxYScvrm8mXL5iqzppk0ccLfr169/r2yslKl0m/QoIGXKiurvDMzM79QAOTm5gXRaLT6zn4DS0uLfF9fn/Cr10JP4eWFt2/fLeHzBbQBA/pfQdMUuRAqA5lMFilzzVa3jKChQ04XFBQOSM/I6PSpd6rkPxT19fVODQ2N9vjnwp8+22hnb5eOxi10dXF5b2pqWvLg4aOdsAqZX0tLq2XihPF7zp27cPlr11qqoO78VgdTp0zZ/urV69/x6/OWlhaTS5evnFm0aMFy1CNgyJDB52tr6zwiI9+2c4OTyWSU7Jyc4R3OhX/RwoNRX++0d+/+t9VV1T2WLPl1Edq2ysoqr/SMjLGdd2lRkwHQaLT6NWtWTToVfPqWm6trrL2Dfaq2thaby+WaVFZWA2DAlgAAE6BJREFUeS9bumSBqufpdXS3589frIE+CsYSiVQzLy9v6OTJk3ah5qXu7m5RN2/ePqKvp9eIap5T09ImBgYG3G4X8OsbP7i9vX3axAkT9vxz/OT9nTu29VcWmMnAwIBBIhElpqamJfjAa5aWlh/YHI5ZNw+Pt1/jWqIO2vxnv+wfH2/vZyxWs9WWrdvTAwP871hYWuSTyWRRbU1tNx1dHdboUaNU+lASCAR5//79ryYkJM70UHDqAB5u7m7RYXfv7RUKhXokEkmCAASKi3s/b/CgQRek0s+LxM74dtFotPply5bMO37i1D0fH++nTo6OSVQqlZ+aljax7b9GK4IgkLLJ5+7uHnX58pUzAwcNuAQBCJHJZJSk5ORp/v5+96QS6ac29evb93orr9Vw2/adKX69e923trHOppApwqzs7FH9+vYN7fHxtAxfX98n10JDT5qZmRVLpVKqt7dXhLa2tsLdj2FBQSH37j/YjV1c5+bkDXN3d4tOS02biG93924ekXv37n876mMEfTqd7pqUnDLV19cnvG+fPjfQfBoaGq3r/1w39lTw6VsZGZlj3d3corlcrkliUtL0bt26RY4ZM/owth3fqp339/O7+/zFi7VUKpUvFou1+/QJvIma8Krbl/b2dunz5s5Z/dfuv+N69er10NrKKpeqQW1tbGTai0QinZkzpm9W9qyxsVGVna1tRtz7+DkEAiQHoM0PtbCgqL+Pj/dT2UeBSy6Xk3b/vSemT58+N/U/xhB6FxX1q4uLSxxa1uBBAy9yW1pMNm3emhUYGHDb3MysiEgkSsvKy3vZ2thkYc338Zg2berWq9dCT+3Y+Vdin8CA29ra2uy8Dx+GNDWxrNesXjW5o8BtikAkEmW9evZ8FB7+dFPXrl1LdXR1WN08PLCn9aj1fWdMn7Y5NPTGia1bt6f7+fW+Z2xiUgEBgOTk5g4bPGjgRVdX1zhVzz97FrFeka+wj7f3Mzc319hffln029lz56/s/ntvtJ9fr/tEIkmamZE5BgAAVixfNgdL21AT3k2bt2Q7OjommRgbV7S0cLtCBAj2cPd41xGj/BGmkCnJqVPYbI45gsAEJrPJlk6nu9bXNzi5ODvF79q1sw/+6F88HBzsU65dCz359FnEejNT02IAAKiorPSxtrbOKf4iOKL67Z87Z86akNNnbuzff/C1j4/3U0Mjw2oOm2OWmpo2qX+/ftdgGPlmtxZ3d7eoO3fu7tfR1WGhvsYpqamT+gQG3GpubrZE8x05cizc1c01Fg2Amp6eMQ49Zlwmk1F2794T069f31DU0u/Nm8jlrq4u7caVlZVlXkNjg0OvXr0e4pW6bm6uMcEhp28OHz7sFL6NffsE3mxmsax27NiV1K9vn+tdu3YtKysv75WX92HIsmVL5mNdGiHwpYUDAAB49ejxoq62zn3nrt3xgQH+d6xt2nzA37yJXD5o4MBLUdHRi/HP4EEmk8X2dvZpPB7PCB+8WE9Xt4lmYECnUikCTU3FQaDbv69bzOPH4VuTkpKnIghCsLe3S0NPOvommqxkfvj7+91tZjdbbN6yLbNPYMAtc3PzQhKZJK6qqvYyMjSsHjasTSHbpUuXuk2bNowIPhVyW0dXh2VjY5NFIpEkxUXFfYKGBYVERET8iZbp7u4e9eRx+BYikSilUtpkm4TExJkDB/S/IpVKMTxdCf91c4sZOXLE8b9274kLCPC/Y2tjkwVBEBz59u3SQQMHXop4/mJtR68bGOB/58XLl6vdXF1jSSSSBLXu/f33lTNCQk7fLCkp9ff19QmXy2Xk1JS0STQDGmPRwoUdHl8dEBBwJyo6ZrFIJNaRyWSUPn0w1rlqzuEePTxfjR416ujWbdvTAgIC7lhaWOSTKWRhXR3dXYNKbR03bqzCoJxurq6xTx6Hb7Gxts7u0qVtnOUXFAz0cHeLwrq5kclk8caN60cGh5y5mZqWNrF79+5vjAwNq9kcjlllZaXPyhXLZwMAwMSJE/aEhd3du2nzlmx/P7+7lpaWHyACBCcmJk+fN3f2GjMzs2JHR4eU2bNmrt/11+73vXv1fmBlbZlLpVL5jQ2NDhKpVGP6Rwu302fOhpqZmhaj7hYFhYX99Wm0ek0NDR6CINCevfve9ezp+7iLQZupf3xCwiw8HcDCw9096vGjJ9sgACGoa1N8QsKsgQMHXJZgxhCRSJSNGDH8xKtXr3/X/Xi4gVwmp0THxC4cMWLEiTbrF0z/BfrfiYqK/kUoFOp90X9q8hA3N9fYqVMn7/h7z76owMCA21ZWlnk11TXd09LSJ8ycMWOjnZ0dxlJWvTKvXr0WTCQRpenp6ePT09PH4+9PnTplh6GhYc0ff6ycfuyfEw+7dfOItLO1zdDS1uJwOBwzOp3h+usvi38DoC1w9do1qyadPBV8JzUtfYKri8t7LS0tTmFRUT8SiSQxNjau+BrXx7lz5qy5dOnyuV1/7Y739/cP09DQaM3JyRne2srvsuqPldOxLlqdkdfd3d2inj57tiEpKXkqDMNER0eH5E8BtNWkeRoaGvwN6/8cHRwScuv9+/g5Ls7O8bp6ukwBX0DLyckdvnbt6onK+vfAwcMvPD27vTYybOMXKSmpk/Fjs6dvz8dnzpwN7T+g31U9XT1mc3OzZXZOznAEQQhLcevTJUt+XXTu3IXLu3fvifX28X5mZGhYLZfLyalpaRPmzZ2zBrW6HjFi+EmhSKS7Zeu2DD8/v3vW1lY5RAJRlpaWPmHs2NGH8DFcOouJEyfsCbt7b8+mzVuz/P1638PO77lzZq/tSH5B0bWrSfny5cvmHj9x6p6vr0+4g719akNDg0NSUvK0oKChp3v17PkYzUsikSTbt20ZdCo45Pbbd1G/OTjYp2hqanJLiksCx40beyAy8q1KGvsjYngIRSLd8PCnG8kUskgkFOnSGQyXujq6m1gk0hkaNOTM0CFDzmLl8DthYfuzs3NGQAjSvi1NTU3WPB7PqP0E7/heQ0OjvUgs0rHB+a8LhULdoqLivlVVVV5CkUhXU0OD17dv3+uKTkBAwWazzfPyPgwRikS6aBpNX7/e3d0tGhvcDUEQ6NmziPXYnZz+/ftdo9FojIaGBkd0cAkEAv3yigpf3AICANDmk11dU9Pd3c0tBk3LzMoa1b1bt0isPx+CIFBOTs5wa2trhcfIoaiqquqhpaXVgk4ALCorK711dfWY+Hfn8nhGdDrd1dXF5T2alpubN9TJyTEJDeLW2NhoJxAI9RWdBY2iqanJmsvlGdsrOUWCxWq2LC4pDqytrfOQy+VkIyOjqr59Am+gPsh8Pp9WWVnl7aHgKMj7Dx7uounr1w8dOuSssvpRSCQSjQcPH+1EdwMIRKJs5MgRx6USiaZEItFEGSiL1WzZ0sIxtbe3/+KINCaTacvn8w3wPqtcLtc4L+/DkKrq6h6wXE5ydHRM9vPrfT8nNzeom4fHO2WB0Zqbmy1evHi5Gr3W0NTkjRk96kh9Q4OjjrZOM75PWCyWVXFJSQD6rVxcnN/38PR8iV3Q5X34MDg7K3skhUoVjBwx/ISqwIMwDBOTkpKnVtfUeMplMjLa7qys7JGent1foeUePnI0fED//ldtbGyyomNiFslkMkrXrl3L3NxcY5TtPIvFYq0PH/IHV1VVeenp6TW6ubnFKCJ82dk5w11dXeIUnYKQmZk5unv37m86Oss7NS1tQnFRcR8tLa2W0aNHHaFQKKLa2jp3IpEgQyOYY1FVXe2pQdVoRQMTouByucZFRcV9q2tqukulUg09Pb3G/v36hqoTtLS8vLxndnbOCIFAoK+hqckbN3bMQTqD4aKjrc1GT3aqqqrqkZ6eMU4ilWoC0HaawbCgoSF4ZWNDQ6N9SUlJAJ3BcIVhmGhublYY4O8fpo7Sory8wre4pCRQ/P/au/OYtu47AODvmSPmMrURGGNDwGAug40JtyHgEcLdrWsidYtSTVMX7Y9JWatK1ZQmm9RtnbZm6VJ16ZFNTdNl2ppSmqMNEbk4Q7jCYRv7+QbjA/yMjQ9szHv7g77IfTUQdSKE6vf5j/vHO3/X9/v1+WIy0tPHCgr43eTfbzabs/z+1ai0tK3jyFdWVmJu3Og67na76Tk5OX0lX2//nZiYaMrLy7sbKsnU2Nh4m1AouEGOHdcbDAKNWlNqtVq5OATB2TzeQFGR8MvNJlunpqYP+Hy+kGEVHA5bSqzG4zgOK5RKMVFCOJPLHc7NzendqPPh9/upOp2u2OlcTtyzZ4+7oIB/663Tf/1CKBB0EYMvm82W6nA4mMHPgtGxsXZyBRAIWl+9HRsbby8JsT066G9GyeXyWqFQ+KgsIYKoKvQGgxDDsDAKDGMxMTF2OoNu5GZkjGyWwIvs/v2hw2q1uoz4ODcvt6dYJLo2MTHZVFQk/Ir4/Gb31Ojo2LMiUdG14POBYRhlZkaxH1GpKlzLywmRkZHe9va2P9uXllgUGMZClQMmaDTafTRa3EKoHEcEYvXKFbTyv792/0fPxMebLRZrJlF5xGKxcoeGhg57vN54CFqvlNPc3HSGOEY6nU40OjbeTgx0GQzGXMOB+nPk869Wa0oZDPpcqPclgqgqmMwk9UbhkWazOWtmRrHfhqKcVA5nWiAo7CLnykBRlI2idjbRbjKTyZQtk8nrzF+XsReLqy6xWCyFRqMt2WrVH4LWV4uwtbXwUPH1xvn5XAqFskYuRbqyshKjRJAqQWHhN8q6Ggyzhf39/UcgGMbr6mr/yUpORpxOZ+K8yZQT/N4nLC0tJS8sLKbzeBvvBt2qT7C4uJimVCJVRqMxfw3DwplJSWqxuOoSOY8GjuOw2WLJmjfO5+E4DmfnZPcjCFL56aeX3/jTm38sgqD191fH550nfUTYFQzjDQfqz0VERPgcDmcS0YYlh4NptVgyiQkyMovFkimVySQmkzkbwnG4vLzs8t69ex8qlUhVqH5HMAzDwrpu3vwVakM5KSkpMxJJ3T+CvyaTy2s1ak1pRGTECi+LN7jRdRFKb2/fUYPBIKDRaAttba1/gWEY12q1xbGxcbZQOU5UKnUZg0E3kuP67XZ7ikKhFM8ZjfxAIBCZwGDM1tRUX9wsGe/du/d+HrwNXiQSXefxsgYVSqWY3GcNBAIRCKKq1Op0xU6nMykmJsZeUy2+SE6ybTKZslUqdTlRaUJUJPySx+MNBt+jDocjSalExMQ7OD6eZqmprvmY2I2Aoih7YPD+Cy6XKwGC1iueNDc3nyEqtRiNxrzh4ZHnVny+WAiCoNjYWFtzU+PfNspjsra2Ft7R8fmpRyXfYRg/2NDwblgYJbDsciWQxw4LCwvp94ceHHY6HEkwhYK1NDedCQQCkR6PN578Pu3p7X1x1jBbSD5/cXFxi6GeiRudPxRF2VKZXGK1WDJZLJZCICi8Se6TuFxuusFgEOaHqBIyPS2t53K5w9HRUU6pVCbxer20UMcCgiCouFh0lXj+ezyeeIVCUa3XG4QrPl9sdFSUo6am+iKdTp8P/hmPx0uTSqX1Wp2ueNXvp7JYLKVEUndeqUSq0tJSJzebgJ2cmmrI5vEGydcijuOwSqUuV6lV5YHVwJ6MjIxRPj//WwsSer1eSKVGLZP7chvR6/XC/oHBn1IolDVJXd15JjNJ43A4ksxmCy9UCXa73c6y2Wxp5EkBv99PVSiU1Tq9XuR2u+kR4eG+qqrKf4fqbxJMZjPvwdCDQ8QYMjoqytHS0nyGeBd3d9/6pUajLTl69MjLt+/c+cWSfYlFo9EWMjMzH+Tl5d4L1ZfBcRxWq9VlGq1uH4qiHBiCcIFA0BWq72O1WjMQRFVpnJ/PwzAsrKCA383Pz78DwzAeasw5MzNTk8Jmy4NDZQOBQOTU1FQDUe2M/P+pEFXFZvd3MJVKVZ6QkDBLvp6Wl5cTZDK5ZG5ujp+YmKgrKOB3b5arBEVRtt5gEK4F1iJSUznTFEpY4PivX9Ze+tfFRztfZHJ5bVpq6hRx32w0loOgx+sfk8cXfr+fOjw88mO3x/MMBK3vto+NjbVxOGwpk8lUhzoGw8Mjz82bTDnfmvAAADKfzxf9xu//cPfUyddrH7dEIvDdEBMeZWWlHTvdFgDYDjiOw6+88qry0KHnfxu8GgcAwNPhypWrr01MTjadfP3Ed879BQAA8DQiJjyOHXvppZ1uy2728OFE87t/P/fJhx+8t2n45NNiW+JKge8PDMMoH3x4/nxrS8tpMNmx/XYiozEAPEkDg4MveL1eWknJvs6tvxsAgCfJ4/HSvrrRdfxA/da7OQEAAHYd0M/+v2EYRuns/OJEff0P3t/ptjyubStLC+x+LpebfuHChXfiaTRrRUX5f3e6PQAA7B63bt0+htrt7Opq8SeREZFeux1lj48/bO3p7XvxxInf1IcKrQIA4MlwuVyMc++9/1Fba+tbyclMlcfjidfrDcLPPuv4XVNT49nKyor/7HQbAQAAgJ2D4zj89ttnL9fUVH/M5WaMrK6uUudNppyrV669lpiUqD186PlTO93GxwVCWoANnT59ppObyR3+4bPtb25XolXgm/r6+o+kp6ePE4l5AWC3crnc9OvXr7+q0WpLAoFAJAzBOCeVM93YePAdcu4DAACeLBzH4YGBwZ+MjIz+yLm8ngeNRqNZy0pLO8BkBwAA31dqtaYUtaPs4OScwMakUpnkXk/Pz2w2WxoEQRCVSl3m8/m3mxoPnt1NY8P/AYDbvuPnaR+mAAAAAElFTkSuQmCC"
                    class="image"
                    style="width: 40.65rem; height: 0.74rem; display: block; z-index: -10; left: 0.00rem; top: 0.84rem;" />
            </div>
            <p class="paragraph body-text"
                style="width: 50.55rem; height: 1.58rem; font-size: 1.00rem; left: 6.25rem; top: 37.09rem; text-align: left; font-family: 'pro', serif;">
            </p>
            <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAoAAAAKCAYAAACNMs+9AAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAAGElEQVQYlWOMiIz+z0AEYCJG0ahC6ikEAFFSAh9YFx1pAAAAAElFTkSuQmCC"
                class="image"
                style="width: 0.40rem; height: 0.37rem; display: block; z-index: 0; left: 6.25rem; top: 38.89rem;" />
            <p class="paragraph body-text"
                style="width: 46.50rem; height: 2.30rem; font-size: 1.10rem; left: 6.25rem; top: 38.16rem; text-align: left; font-family: pro, serif; font-weight: 300;">
                <span class="position" style="width: 1.77rem; height: 0.89rem; left: 1.04rem; top: 0.53rem;">
                    <span class="style"></span>
                    <span class="style"
                        style="font-family: 'pro', serif; font-size: 1.00rem; transform: ScaleX(1.05);">
                    </span>
                    <span class="style"
                        style="font-family: 'pro', serif; font-size: 0.75rem; color: #58595b; transform: ScaleX(1.05);">Upon</span>
                </span>
                <span class="position style"
                    style="width: 0.23rem; height: 0.89rem; font-size: 0.75rem; left: 2.99rem; top: 0.53rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    r</span>
                <span class="position style"
                    style="width: 0.81rem; height: 0.89rem; font-size: 0.75rem; left: 3.21rem; top: 0.53rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">eg</span>
                <span class="position style"
                    style="width: 2.54rem; height: 0.89rem; font-size: 0.75rem; left: 4.02rem; top: 0.53rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">istration</span>
                <span class="position style"
                    style="width: 0.24rem; height: 0.89rem; font-size: 0.75rem; left: 6.73rem; top: 0.53rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    t</span>
                <span class="position style"
                    style="width: 0.42rem; height: 0.89rem; font-size: 0.75rem; left: 6.97rem; top: 0.53rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">o</span>
                <span class="position style"
                    style="width: 0.36rem; height: 0.89rem; font-size: 0.75rem; left: 7.56rem; top: 0.53rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    F</span>
                <span class="position style"
                    style="width: 2.30rem; height: 0.89rem; font-size: 0.75rem; left: 7.89rem; top: 0.53rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">aisaNet</span>
                <span class="position style"
                    style="width: 1.21rem; height: 0.89rem; font-size: 0.75rem; left: 10.36rem; top: 0.53rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    and</span>
                <span class="position style"
                    style="width: 0.36rem; height: 0.89rem; font-size: 0.75rem; left: 11.74rem; top: 0.53rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    F</span>
                <span class="position style"
                    style="width: 1.79rem; height: 0.89rem; font-size: 0.75rem; left: 12.07rem; top: 0.53rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">aisaM</span>
                <span class="position style"
                    style="width: 1.57rem; height: 0.89rem; font-size: 0.75rem; left: 13.86rem; top: 0.53rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">obile</span>
                <span class="position style"
                    style="width: 1.07rem; height: 0.89rem; font-size: 0.75rem; left: 15.60rem; top: 0.53rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    will</span>
                <span class="position style"
                    style="width: 1.24rem; height: 0.89rem; font-size: 0.75rem; left: 16.84rem; top: 0.53rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    also</span>
                <span class="position style"
                    style="width: 0.82rem; height: 0.89rem; font-size: 0.75rem; left: 18.25rem; top: 0.53rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    be</span>
                <span class="position style"
                    style="width: 0.23rem; height: 0.89rem; font-size: 0.75rem; left: 19.24rem; top: 0.53rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    r</span>
                <span class="position style"
                    style="width: 0.81rem; height: 0.89rem; font-size: 0.75rem; left: 19.47rem; top: 0.53rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">eg</span>
                <span class="position style"
                    style="width: 0.70rem; height: 0.89rem; font-size: 0.75rem; left: 20.27rem; top: 0.53rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">ist</span>
                <span class="position style"
                    style="width: 0.61rem; height: 0.89rem; font-size: 0.75rem; left: 20.97rem; top: 0.53rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">er</span>
                <span class="position style"
                    style="width: 0.81rem; height: 0.89rem; font-size: 0.75rem; left: 21.58rem; top: 0.53rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">ed</span>
                <span class="position style"
                    style="width: 0.21rem; height: 0.89rem; font-size: 0.75rem; left: 22.56rem; top: 0.53rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    f</span>
                <span class="position style"
                    style="width: 0.65rem; height: 0.89rem; font-size: 0.75rem; left: 22.76rem; top: 0.53rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">or</span>
                <span class="position style"
                    style="width: 0.36rem; height: 0.89rem; font-size: 0.75rem; left: 23.58rem; top: 0.53rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    F</span>
                <span class="position style"
                    style="width: 1.57rem; height: 0.89rem; font-size: 0.75rem; left: 23.91rem; top: 0.53rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">aisaP</span>
                <span class="position style"
                    style="width: 0.36rem; height: 0.89rem; font-size: 0.75rem; left: 25.45rem; top: 0.53rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">a</span>
                <span class="position style"
                    style="width: 0.35rem; height: 0.89rem; font-size: 0.75rem; left: 25.81rem; top: 0.53rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">y</span>
                <span class="position style"
                    style="width: 0.37rem; height: 0.89rem; font-size: 0.75rem; left: 26.32rem; top: 0.53rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    S</span>
                <span class="position style"
                    style="width: 0.61rem; height: 0.89rem; font-size: 0.75rem; left: 26.69rem; top: 0.53rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">er</span>
                <span class="position style"
                    style="width: 1.25rem; height: 0.89rem; font-size: 0.75rem; left: 27.33rem; top: 0.53rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">vice</span>
                <span class="position style"
                    style="width: 1.21rem; height: 0.89rem; font-size: 0.75rem; left: 28.76rem; top: 0.53rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    and</span>
                <span class="position style"
                    style="width: 1.27rem; height: 0.89rem; font-size: 0.75rem; left: 30.14rem; top: 0.53rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    that</span>
                <span class="position style"
                    style="width: 0.37rem; height: 0.89rem; font-size: 0.75rem; left: 31.58rem; top: 0.53rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    if</span>
                <span class="position style"
                    style="width: 1.01rem; height: 0.89rem; font-size: 0.75rem; left: 32.12rem; top: 0.53rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    I/w</span>
                <span class="position style"
                    style="width: 0.38rem; height: 0.89rem; font-size: 0.75rem; left: 33.13rem; top: 0.53rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">e</span>
                <span class="position style"
                    style="width: 1.74rem; height: 0.89rem; font-size: 0.75rem; left: 33.68rem; top: 0.53rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    apply</span>
                <span class="position style"
                    style="width: 0.24rem; height: 0.89rem; font-size: 0.75rem; left: 35.60rem; top: 0.53rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    t</span>
                <span class="position style"
                    style="width: 0.42rem; height: 0.89rem; font-size: 0.75rem; left: 35.84rem; top: 0.53rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">o</span>
                <span class="position style"
                    style="width: 0.78rem; height: 0.89rem; font-size: 0.75rem; left: 36.43rem; top: 0.53rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    an</span>
                <span class="position style"
                    style="width: 0.35rem; height: 0.89rem; font-size: 0.75rem; left: 37.20rem; top: 0.53rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">y</span>
                <span class="position style"
                    style="width: 0.91rem; height: 0.89rem; font-size: 0.75rem; left: 37.72rem; top: 0.53rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    ser</span>
                <span class="position style"
                    style="width: 1.25rem; height: 0.89rem; font-size: 0.75rem; left: 38.65rem; top: 0.53rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">vice</span>
                <span class="position style"
                    style="width: 0.65rem; height: 0.89rem; font-size: 0.75rem; left: 40.08rem; top: 0.53rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    or</span>
                <span class="position style"
                    style="width: 0.67rem; height: 0.89rem; font-size: 0.75rem; left: 40.91rem; top: 0.53rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    pr</span>
                <span class="position style"
                    style="width: 1.62rem; height: 0.89rem; font-size: 0.75rem; left: 41.56rem; top: 0.53rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">oduc</span>
                <span class="position style"
                    style="width: 0.24rem; height: 0.89rem; font-size: 0.75rem; left: 43.19rem; top: 0.53rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">t</span>
                <span class="position style"
                    style="width: 1.28rem; height: 0.89rem; font-size: 0.75rem; left: 1.05rem; top: 1.41rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    elec</span>
                <span class="position style"
                    style="width: 0.48rem; height: 0.89rem; font-size: 0.75rem; left: 2.34rem; top: 1.41rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">tr</span>
                <span class="position style"
                    style="width: 2.40rem; height: 0.89rem; font-size: 0.75rem; left: 2.80rem; top: 1.41rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">onically</span>
                <span class="position style"
                    style="width: 0.90rem; height: 0.89rem; font-size: 0.75rem; left: 5.37rem; top: 1.41rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    thr</span>
                <span class="position style"
                    style="width: 1.69rem; height: 0.89rem; font-size: 0.75rem; left: 6.26rem; top: 1.41rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">ough</span>
                <span class="position style"
                    style="width: 0.36rem; height: 0.89rem; font-size: 0.75rem; left: 8.12rem; top: 1.41rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    F</span>
                <span class="position style"
                    style="width: 1.79rem; height: 0.89rem; font-size: 0.75rem; left: 8.45rem; top: 1.41rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">aisaM</span>
                <span class="position style"
                    style="width: 1.57rem; height: 0.89rem; font-size: 0.75rem; left: 10.24rem; top: 1.41rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">obile</span>
                <span class="position style"
                    style="width: 3.66rem; height: 0.89rem; font-size: 0.75rem; left: 11.98rem; top: 1.41rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    application,</span>
                <span class="position style"
                    style="width: 1.05rem; height: 0.89rem; font-size: 0.75rem; left: 15.81rem; top: 1.41rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    the</span>
                <span class="position style"
                    style="width: 1.51rem; height: 0.89rem; font-size: 0.75rem; left: 17.03rem; top: 1.41rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    Bank</span>
                <span class="position style"
                    style="width: 1.07rem; height: 0.89rem; font-size: 0.75rem; left: 18.72rem; top: 1.41rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    will</span>
                <span class="position style"
                    style="width: 2.69rem; height: 0.89rem; font-size: 0.75rem; left: 19.96rem; top: 1.41rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    consider</span>
                <span class="position style"
                    style="width: 1.49rem; height: 0.89rem; font-size: 0.75rem; left: 22.83rem; top: 1.41rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    such</span>
                <span class="position style"
                    style="width: 3.52rem; height: 0.89rem; font-size: 0.75rem; left: 24.48rem; top: 1.41rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    application</span>
                <span class="position style"
                    style="width: 0.65rem; height: 0.89rem; font-size: 0.75rem; left: 28.18rem; top: 1.41rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    as</span>
                <span class="position style"
                    style="width: 0.65rem; height: 0.89rem; font-size: 0.75rem; left: 29.00rem; top: 1.41rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    or</span>
                <span class="position style"
                    style="width: 0.59rem; height: 0.89rem; font-size: 0.75rem; left: 29.66rem; top: 1.41rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">ig</span>
                <span class="position style"
                    style="width: 2.20rem; height: 0.89rem; font-size: 0.75rem; left: 30.24rem; top: 1.41rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">inating</span>
                <span class="position style"
                    style="width: 0.44rem; height: 0.89rem; font-size: 0.75rem; left: 32.62rem; top: 1.41rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    fr</span>
                <span class="position style"
                    style="width: 1.06rem; height: 0.89rem; font-size: 0.75rem; left: 33.05rem; top: 1.41rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">om</span>
                <span class="position style"
                    style="width: 2.02rem; height: 0.89rem; font-size: 0.75rem; left: 34.29rem; top: 1.41rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    me/us</span>
                <span class="position style"
                    style="width: 1.21rem; height: 0.89rem; font-size: 0.75rem; left: 36.48rem; top: 1.41rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    and</span>
                <span class="position style"
                    style="width: 0.65rem; height: 0.89rem; font-size: 0.75rem; left: 37.86rem; top: 1.41rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    as</span>
                <span class="position style"
                    style="width: 2.02rem; height: 0.89rem; font-size: 0.75rem; left: 38.68rem; top: 1.41rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    legally</span>
                <span class="position style"
                    style="width: 1.61rem; height: 0.89rem; font-size: 0.75rem; left: 40.87rem; top: 1.41rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    valid;</span>
                <span class="position style"
                    style="width: 1.21rem; height: 0.89rem; font-size: 0.75rem; left: 42.66rem; top: 1.41rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    and</span>
                <span class="position style"
                    style="width: 0.37rem; height: 0.89rem; font-size: 0.75rem; left: 44.04rem; top: 1.41rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    if</span>
                <span class="position style"
                    style="width: 1.49rem; height: 0.89rem; font-size: 0.75rem; left: 44.58rem; top: 1.41rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    such</span>
            </p>
            <img src="data:image/png;base64,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"
                class="image"
                style="width: 41.97rem; height: 1.55rem; display: block; z-index: 0; left: 7.33rem; top: 40.65rem;" />
            <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAoAAAAKCAYAAACNMs+9AAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAAGElEQVQYlWOMiIz+z0AEYCJG0ahC6ikEAFFSAh9YFx1pAAAAAElFTkSuQmCC"
                class="image"
                style="width: 0.40rem; height: 0.37rem; display: block; z-index: 0; left: 6.25rem; top: 42.49rem;" />
            <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAoAAAAKCAYAAACNMs+9AAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAAGElEQVQYlWOMiIz+z0AEYCJG0ahC6ikEAFFSAh9YFx1pAAAAAElFTkSuQmCC"
                class="image"
                style="width: 0.40rem; height: 0.37rem; display: block; z-index: 0; left: 6.25rem; top: 44.36rem;" />
            <img src="data:image/png;base64,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"
                class="image"
                style="width: 13.07rem; height: 0.73rem; display: block; z-index: 10; left: 7.31rem; top: 43.25rem;" />
            <img src="data:image/png;base64,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"
                class="image"
                style="width: 30.60rem; height: 2.49rem; display: block; z-index: -10; left: 7.31rem; top: 45.03rem;" />
            <p class="paragraph body-text"
                style="width: 46.04rem; height: 4.91rem; font-size: 1.10rem; left: 6.25rem; top: 40.91rem; text-align: left; font-family: pro, serif; font-weight: 300;">
                <span class="position" style="width: 0.37rem; height: 0.89rem; left: 1.04rem; top: 1.32rem;">
                    <span class="style"></span>
                    <span class="style"
                        style="font-family: 'pro', serif; font-size: 1.00rem; transform: ScaleX(1.05);">
                    </span>
                    <span class="style"
                        style="font-family: 'pro', serif; font-size: 0.75rem; color: #58595b; transform: ScaleX(1.05);">T</span>
                </span>
                <span class="position style"
                    style="width: 0.80rem; height: 0.89rem; font-size: 0.75rem; left: 1.41rem; top: 1.32rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">he</span>
                <span class="position style"
                    style="width: 1.51rem; height: 0.89rem; font-size: 0.75rem; left: 2.39rem; top: 1.32rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    Bank</span>
                <span class="position style"
                    style="width: 0.46rem; height: 0.89rem; font-size: 0.75rem; left: 4.07rem; top: 1.32rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    is</span>
                <span class="position style"
                    style="width: 1.09rem; height: 0.89rem; font-size: 0.75rem; left: 4.70rem; top: 1.32rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    not</span>
                <span class="position style"
                    style="width: 0.23rem; height: 0.89rem; font-size: 0.75rem; left: 5.96rem; top: 1.32rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    r</span>
                <span class="position style"
                    style="width: 3.40rem; height: 0.89rem; font-size: 0.75rem; left: 6.18rem; top: 1.32rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">esponsible</span>
                <span class="position style"
                    style="width: 0.21rem; height: 0.89rem; font-size: 0.75rem; left: 9.75rem; top: 1.32rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    f</span>
                <span class="position style"
                    style="width: 0.65rem; height: 0.89rem; font-size: 0.75rem; left: 9.94rem; top: 1.32rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">or</span>
                <span class="position style"
                    style="width: 0.78rem; height: 0.89rem; font-size: 0.75rem; left: 10.77rem; top: 1.32rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    an</span>
                <span class="position style"
                    style="width: 0.35rem; height: 0.89rem; font-size: 0.75rem; left: 11.54rem; top: 1.32rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">y</span>
                <span class="position style"
                    style="width: 2.20rem; height: 0.89rem; font-size: 0.75rem; left: 12.06rem; top: 1.32rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    misuse</span>
                <span class="position style"
                    style="width: 0.63rem; height: 0.89rem; font-size: 0.75rem; left: 14.43rem; top: 1.32rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    of</span>
                <span class="position style"
                    style="width: 5.32rem; height: 0.89rem; font-size: 0.75rem; left: 15.23rem; top: 1.32rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    communications</span>
                <span class="position style"
                    style="width: 1.34rem; height: 0.89rem; font-size: 0.75rem; left: 20.72rem; top: 1.32rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    sent</span>
                <span class="position style"
                    style="width: 0.24rem; height: 0.89rem; font-size: 0.75rem; left: 22.24rem; top: 1.32rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    t</span>
                <span class="position style"
                    style="width: 0.42rem; height: 0.89rem; font-size: 0.75rem; left: 22.48rem; top: 1.32rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">o</span>
                <span class="position style"
                    style="width: 0.65rem; height: 0.89rem; font-size: 0.75rem; left: 23.07rem; top: 1.32rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    m</span>
                <span class="position style"
                    style="width: 0.35rem; height: 0.89rem; font-size: 0.75rem; left: 23.71rem; top: 1.32rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">y</span>
                <span class="position style"
                    style="width: 0.23rem; height: 0.89rem; font-size: 0.75rem; left: 24.23rem; top: 1.32rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    r</span>
                <span class="position style"
                    style="width: 0.81rem; height: 0.89rem; font-size: 0.75rem; left: 24.45rem; top: 1.32rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">eg</span>
                <span class="position style"
                    style="width: 0.70rem; height: 0.89rem; font-size: 0.75rem; left: 25.26rem; top: 1.32rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">ist</span>
                <span class="position style"
                    style="width: 0.61rem; height: 0.89rem; font-size: 0.75rem; left: 25.95rem; top: 1.32rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">er</span>
                <span class="position style"
                    style="width: 0.81rem; height: 0.89rem; font-size: 0.75rem; left: 26.56rem; top: 1.32rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">ed</span>
                <span class="position style"
                    style="width: 2.21rem; height: 0.89rem; font-size: 0.75rem; left: 27.54rem; top: 1.32rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    mobile</span>
                <span class="position style"
                    style="width: 0.28rem; height: 0.89rem; font-size: 0.75rem; left: 29.93rem; top: 1.32rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    /</span>
                <span class="position style"
                    style="width: 1.70rem; height: 0.89rem; font-size: 0.75rem; left: 30.38rem; top: 1.32rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    Email</span>
                <span class="position style"
                    style="width: 1.45rem; height: 0.89rem; font-size: 0.75rem; left: 32.25rem; top: 1.32rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    addr</span>
                <span class="position style"
                    style="width: 0.97rem; height: 0.89rem; font-size: 0.75rem; left: 33.69rem; top: 1.32rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">ess</span>
                <span class="position style"
                    style="width: 1.21rem; height: 0.89rem; font-size: 0.75rem; left: 34.83rem; top: 1.32rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    and</span>
                <span class="position style"
                    style="width: 0.24rem; height: 0.89rem; font-size: 0.75rem; left: 36.22rem; top: 1.32rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    t</span>
                <span class="position style"
                    style="width: 0.42rem; height: 0.89rem; font-size: 0.75rem; left: 36.45rem; top: 1.32rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">o</span>
                <span class="position style"
                    style="width: 1.75rem; height: 0.89rem; font-size: 0.75rem; left: 37.05rem; top: 1.32rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    ensur</span>
                <span class="position style"
                    style="width: 0.38rem; height: 0.89rem; font-size: 0.75rem; left: 38.79rem; top: 1.32rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">e</span>
                <span class="position style"
                    style="width: 1.27rem; height: 0.89rem; font-size: 0.75rem; left: 39.34rem; top: 1.32rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    that</span>
                <span class="position style"
                    style="width: 0.69rem; height: 0.89rem; font-size: 0.75rem; left: 40.78rem; top: 1.32rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    all</span>
                <span class="position style"
                    style="width: 2.33rem; height: 0.89rem; font-size: 0.75rem; left: 41.65rem; top: 1.32rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    measur</span>
                <span class="position style"
                    style="width: 0.68rem; height: 0.89rem; font-size: 0.75rem; left: 43.97rem; top: 1.32rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">es</span>
                <span class="position style"
                    style="width: 0.59rem; height: 0.89rem; font-size: 0.75rem; left: 44.82rem; top: 1.32rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    ar</span>
                <span class="position style"
                    style="width: 0.38rem; height: 0.89rem; font-size: 0.75rem; left: 45.40rem; top: 1.32rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">e</span>
                <span class="position" style="width: 0.46rem; height: 0.89rem; left: 1.04rem; top: 3.09rem;">
                    <span class="style"
                        style="font-family: 'pro', serif; font-size: 0.75rem; color: #58595b; transform: ScaleX(1.05);">
                    </span>
                    <span class="style" style="font-family: 'pro', serif; font-size: 0.75rem; color: #58595b;"></span>
                    <span class="style"
                        style="font-family: 'pro', serif; font-size: 0.75rem; color: #58595b; font-weight: 400; transform: ScaleX(1.05);">
                    </span>
                    <span class="style"
                        style="font-family: 'pro', serif; font-size: 0.75rem; color: #58595b; transform: ScaleX(1.05);">A</span>
                </span>
                <span class="position style"
                    style="width: 1.67rem; height: 0.89rem; font-size: 0.75rem; left: 1.50rem; top: 3.09rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">ccess</span>
                <span class="position style"
                    style="width: 0.24rem; height: 0.89rem; font-size: 0.75rem; left: 3.34rem; top: 3.09rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    t</span>
                <span class="position style"
                    style="width: 0.42rem; height: 0.89rem; font-size: 0.75rem; left: 3.58rem; top: 3.09rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">o</span>
                <span class="position style"
                    style="width: 0.82rem; height: 0.89rem; font-size: 0.75rem; left: 4.17rem; top: 3.09rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    be</span>
                <span class="position style"
                    style="width: 0.67rem; height: 0.89rem; font-size: 0.75rem; left: 5.16rem; top: 3.09rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    pr</span>
                <span class="position style"
                    style="width: 0.42rem; height: 0.89rem; font-size: 0.75rem; left: 5.82rem; top: 3.09rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">o</span>
                <span class="position style"
                    style="width: 1.76rem; height: 0.89rem; font-size: 0.75rem; left: 6.24rem; top: 3.09rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">vided</span>
                <span class="position style"
                    style="width: 0.24rem; height: 0.89rem; font-size: 0.75rem; left: 8.17rem; top: 3.09rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    t</span>
                <span class="position style"
                    style="width: 0.42rem; height: 0.89rem; font-size: 0.75rem; left: 8.41rem; top: 3.09rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">o</span>
                <span class="position style"
                    style="width: 1.05rem; height: 0.89rem; font-size: 0.75rem; left: 9.00rem; top: 3.09rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    the</span>
                <span class="position style"
                    style="width: 3.21rem; height: 0.89rem; font-size: 0.75rem; left: 10.22rem; top: 3.09rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    Nominees</span>
                <span class="position style"
                    style="width: 0.87rem; height: 0.89rem; font-size: 0.75rem; left: 13.61rem; top: 3.09rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    list</span>
                <span class="position style"
                    style="width: 0.81rem; height: 0.89rem; font-size: 0.75rem; left: 14.47rem; top: 3.09rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">ed</span>
                <span class="position style"
                    style="width: 0.59rem; height: 0.89rem; font-size: 0.75rem; left: 15.46rem; top: 3.09rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    in</span>
                <span class="position style"
                    style="width: 1.05rem; height: 0.89rem; font-size: 0.75rem; left: 16.22rem; top: 3.09rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    the</span>
                <span class="position style"
                    style="width: 0.23rem; height: 0.89rem; font-size: 0.75rem; left: 17.44rem; top: 3.09rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    r</span>
                <span class="position style"
                    style="width: 2.15rem; height: 0.89rem; font-size: 0.75rem; left: 17.66rem; top: 3.09rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">equest</span>
                <span class="position style"
                    style="width: 0.80rem; height: 0.89rem; font-size: 0.75rem; left: 19.98rem; top: 3.09rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    (in</span>
                <span class="position style"
                    style="width: 1.39rem; height: 0.89rem; font-size: 0.75rem; left: 20.95rem; top: 3.09rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    case</span>
                <span class="position style"
                    style="width: 0.63rem; height: 0.89rem; font-size: 0.75rem; left: 22.51rem; top: 3.09rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    of</span>
                <span class="position style"
                    style="width: 3.59rem; height: 0.89rem; font-size: 0.75rem; left: 23.30rem; top: 3.09rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    businesses)</span>
                <span class="position style"
                    style="width: 1.98rem; height: 0.89rem; font-size: 0.75rem; left: 27.07rem; top: 3.09rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    unless</span>
                <span class="position style"
                    style="width: 1.05rem; height: 0.89rem; font-size: 0.75rem; left: 29.22rem; top: 3.09rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    the</span>
                <span class="position style"
                    style="width: 2.56rem; height: 0.89rem; font-size: 0.75rem; left: 30.44rem; top: 3.09rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    account</span>
                <span class="position style"
                    style="width: 2.05rem; height: 0.89rem; font-size: 0.75rem; left: 33.17rem; top: 3.09rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    holder</span>
                <span class="position style"
                    style="width: 2.28rem; height: 0.89rem; font-size: 0.75rem; left: 35.40rem; top: 3.09rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    advises</span>
                <span class="position style"
                    style="width: 1.05rem; height: 0.89rem; font-size: 0.75rem; left: 37.85rem; top: 3.09rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    the</span>
                <span class="position style"
                    style="width: 1.51rem; height: 0.89rem; font-size: 0.75rem; left: 39.07rem; top: 3.09rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    Bank</span>
                <span class="position style"
                    style="width: 0.63rem; height: 0.89rem; font-size: 0.75rem; left: 40.75rem; top: 3.09rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    of</span>
                <span class="position style"
                    style="width: 0.78rem; height: 0.89rem; font-size: 0.75rem; left: 41.55rem; top: 3.09rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    an</span>
                <span class="position style"
                    style="width: 0.35rem; height: 0.89rem; font-size: 0.75rem; left: 42.32rem; top: 3.09rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">y</span>
                <span class="position style"
                    style="width: 2.36rem; height: 0.89rem; font-size: 0.75rem; left: 42.84rem; top: 3.09rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    change</span>
                <span class="position style"
                    style="width: 0.59rem; height: 0.89rem; font-size: 0.75rem; left: 45.38rem; top: 3.09rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    in</span>
            </p>
            <div class="group" style="width: 0.40rem; height: 0.37rem; display: block; left: 6.25rem; top: 46.05rem;">
                <svg viewbox="0.000000, 0.000000, 4.000000, 3.700000" class="graphic"
                    style="width: 0.40rem; height: 0.37rem; display: block; z-index: -10; left: 0.00rem; top: 0.00rem;">
                    <path fill="#58595b" fill-opacity="1.000000" d="M 3.994 0 L 0 0 L 0 3.694 L 3.994 3.694 L 3.994 0 Z"
                        stroke="none" />
                </svg>
            </div>
            <div class="group" style="width: 0.40rem; height: 0.37rem; display: block; left: 6.25rem; top: 46.95rem;">
                <svg viewbox="0.000000, 0.000000, 4.000000, 3.700000" class="graphic"
                    style="width: 0.40rem; height: 0.37rem; display: block; z-index: -10; left: 0.00rem; top: 0.00rem;">
                    <path fill="#58595b" fill-opacity="1.000000" d="M 3.994 0 L 0 0 L 0 3.694 L 3.994 3.694 L 3.994 0 Z"
                        stroke="none" />
                </svg>
            </div>
            <svg viewbox="0.000000, 0.000000, 12.900000, 11.100000" class="graphic"
                style="width: 1.29rem; height: 1.11rem; display: block; z-index: 10; left: 2.20rem; top: 48.22rem;">
                <path stroke-width="0.246000" fill="none" d="M 0 0 L 12.871 0 L 12.871 11.081 L 0 11.081 L 0 0 Z"
                    stroke="#939598" stroke-opacity="1.000000" />
            </svg>
            <p class="paragraph body-text"
                style="width: 52.70rem; height: 0.90rem; font-size: 0.75rem; left: 4.10rem; top: 48.31rem; text-align: left; font-family: 'pro', serif; color: #58595b; font-weight: 400;">
                <span class="position style"
                    style="width: 0.52rem; height: 0.95rem; left: 0.00rem; top: -0.04rem; transform: ScaleX(1.05);">D</span>
                <span class="position style"
                    style="width: 1.31rem; height: 0.95rem; left: 0.53rem; top: -0.04rem; transform: ScaleX(1.05);">ecla</span>
                <span class="position style"
                    style="width: 0.63rem; height: 0.95rem; left: 1.84rem; top: -0.04rem; transform: ScaleX(1.05);">ra</span>
                <span class="position style"
                    style="width: 1.32rem; height: 0.95rem; left: 2.48rem; top: -0.04rem; transform: ScaleX(1.05);">tion</span>
                <span class="position style"
                    style="width: 0.23rem; height: 0.95rem; left: 3.96rem; top: -0.04rem; transform: ScaleX(1.05);">
                    f</span>
                <span class="position style"
                    style="width: 0.69rem; height: 0.95rem; left: 4.18rem; top: -0.04rem; transform: ScaleX(1.05);">or</span>
                <span class="position style"
                    style="width: 0.46rem; height: 0.95rem; left: 5.04rem; top: -0.04rem; transform: ScaleX(1.05);">
                    C</span>
                <span class="position style"
                    style="width: 0.64rem; height: 0.95rem; left: 5.49rem; top: -0.04rem; transform: ScaleX(1.05);">ar</span>
                <span class="position style"
                    style="width: 0.76rem; height: 0.95rem; left: 6.12rem; top: -0.04rem; transform: ScaleX(1.05);">ds</span>
            </p>
            <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAkAAAAJCAYAAADgkQYQAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAAGklEQVQYlWOMiIz+z0AAMBFSMKqIgYGBgQEAr1gCHcU/2B0AAAAASUVORK5CYII="
                class="image"
                style="width: 0.35rem; height: 0.35rem; display: block; z-index: 0; left: 4.99rem; top: 50.49rem;" />
            <p class="paragraph body-text"
                style="width: 51.82rem; height: 0.89rem; font-size: 1.10rem; left: 4.98rem; top: 50.22rem; text-align: left; font-family: pro, serif; font-weight: 300;">
                <span class="position" style="width: 0.37rem; height: 0.89rem; left: 1.15rem; top: 0.01rem;">
                    <span class="style"></span>
                    <span class="style"
                        style="font-family: 'pro', serif; font-size: 1.00rem; transform: ScaleX(1.50);">
                    </span>
                    <span class="style"
                        style="font-family: 'pro', serif; font-size: 0.75rem; color: #58595b; transform: ScaleX(1.05);">T</span>
                </span>
                <span class="position style"
                    style="width: 0.88rem; height: 0.89rem; font-size: 0.75rem; left: 1.52rem; top: 0.01rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">his</span>
                <span class="position style"
                    style="width: 3.53rem; height: 0.89rem; font-size: 0.75rem; left: 2.57rem; top: 0.01rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    declaration</span>
                <span class="position style"
                    style="width: 0.46rem; height: 0.89rem; font-size: 0.75rem; left: 6.27rem; top: 0.01rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    is</span>
                <span class="position style"
                    style="width: 1.81rem; height: 0.89rem; font-size: 0.75rem; left: 6.90rem; top: 0.01rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    made</span>
                <span class="position style"
                    style="width: 0.24rem; height: 0.89rem; font-size: 0.75rem; left: 8.89rem; top: 0.01rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    t</span>
                <span class="position style"
                    style="width: 0.42rem; height: 0.89rem; font-size: 0.75rem; left: 9.13rem; top: 0.01rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">o</span>
                <span class="position style"
                    style="width: 0.61rem; height: 0.89rem; font-size: 0.75rem; left: 9.72rem; top: 0.01rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    M</span>
                <span class="position style"
                    style="width: 1.47rem; height: 0.89rem; font-size: 0.75rem; left: 10.34rem; top: 0.01rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">aldiv</span>
                <span class="position style"
                    style="width: 0.68rem; height: 0.89rem; font-size: 0.75rem; left: 11.81rem; top: 0.01rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">es</span>
                <span class="position style"
                    style="width: 2.15rem; height: 0.89rem; font-size: 0.75rem; left: 12.65rem; top: 0.01rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    Islamic</span>
                <span class="position style"
                    style="width: 1.51rem; height: 0.89rem; font-size: 0.75rem; left: 14.97rem; top: 0.01rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    Bank</span>
                <span class="position style"
                    style="width: 0.40rem; height: 0.89rem; font-size: 0.75rem; left: 16.66rem; top: 0.01rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    P</span>
                <span class="position style"
                    style="width: 0.52rem; height: 0.89rem; font-size: 0.75rem; left: 17.05rem; top: 0.01rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">lc</span>
                <span class="position style"
                    style="width: 0.14rem; height: 0.89rem; font-size: 0.75rem; left: 17.56rem; top: 0.01rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">.</span>
                <span class="position style"
                    style="width: 1.62rem; height: 0.89rem; font-size: 0.75rem; left: 17.87rem; top: 0.01rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    (“MIB</span>
                <span class="position style"
                    style="width: 0.44rem; height: 0.89rem; font-size: 0.75rem; left: 19.49rem; top: 0.01rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">”)</span>
            </p>
            <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAkAAAAJCAYAAADgkQYQAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAAGklEQVQYlWOMiIz+z0AAMBFSMKqIgYGBgQEAr1gCHcU/2B0AAAAASUVORK5CYII="
                class="image"
                style="width: 0.35rem; height: 0.35rem; display: block; z-index: 0; left: 4.99rem; top: 51.28rem;" />
            <p class="paragraph body-text"
                style="width: 51.82rem; height: 1.02rem; font-size: 1.10rem; left: 4.98rem; top: 50.99rem; text-align: left; font-family: pro, serif; font-weight: 300;">
                <span class="position" style="width: 0.40rem; height: 0.89rem; left: 1.15rem; top: 0.14rem;">
                    <span class="style"></span>
                    <span class="style"
                        style="font-family: 'pro', serif; font-size: 1.00rem; transform: ScaleX(1.50);">
                    </span>
                    <span class="style"
                        style="font-family: 'pro', serif; font-size: 0.75rem; color: #58595b; transform: ScaleX(1.05);">B</span>
                </span>
                <span class="position style"
                    style="width: 0.35rem; height: 0.89rem; font-size: 0.75rem; left: 1.56rem; top: 0.14rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">y</span>
                <span class="position style"
                    style="width: 0.89rem; height: 0.89rem; font-size: 0.75rem; left: 2.08rem; top: 0.14rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    sig</span>
                <span class="position style"
                    style="width: 1.44rem; height: 0.89rem; font-size: 0.75rem; left: 2.96rem; top: 0.14rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">ning</span>
                <span class="position style"
                    style="width: 1.40rem; height: 0.89rem; font-size: 0.75rem; left: 4.57rem; top: 0.14rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    belo</span>
                <span class="position style"
                    style="width: 0.57rem; height: 0.89rem; font-size: 0.75rem; left: 5.97rem; top: 0.14rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">w</span>
                <span class="position style"
                    style="width: 1.01rem; height: 0.89rem; font-size: 0.75rem; left: 6.71rem; top: 0.14rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    I/w</span>
                <span class="position style"
                    style="width: 0.38rem; height: 0.89rem; font-size: 0.75rem; left: 7.72rem; top: 0.14rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">e</span>
                <span class="position style"
                    style="width: 0.23rem; height: 0.89rem; font-size: 0.75rem; left: 8.27rem; top: 0.14rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    r</span>
                <span class="position style"
                    style="width: 2.15rem; height: 0.89rem; font-size: 0.75rem; left: 8.50rem; top: 0.14rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">equest</span>
                <span class="position style"
                    style="width: 0.21rem; height: 0.89rem; font-size: 0.75rem; left: 10.82rem; top: 0.14rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    f</span>
                <span class="position style"
                    style="width: 0.65rem; height: 0.89rem; font-size: 0.75rem; left: 11.01rem; top: 0.14rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">or</span>
                <span class="position style"
                    style="width: 0.36rem; height: 0.89rem; font-size: 0.75rem; left: 11.84rem; top: 0.14rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    a</span>
                <span class="position style"
                    style="width: 0.51rem; height: 0.89rem; font-size: 0.75rem; left: 12.37rem; top: 0.14rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    D</span>
                <span class="position style"
                    style="width: 1.23rem; height: 0.89rem; font-size: 0.75rem; left: 12.88rem; top: 0.14rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">ebit</span>
                <span class="position style"
                    style="width: 0.45rem; height: 0.89rem; font-size: 0.75rem; left: 14.28rem; top: 0.14rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    C</span>
                <span class="position style"
                    style="width: 0.59rem; height: 0.89rem; font-size: 0.75rem; left: 14.73rem; top: 0.14rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">ar</span>
                <span class="position style"
                    style="width: 0.43rem; height: 0.89rem; font-size: 0.75rem; left: 15.31rem; top: 0.14rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">d</span>
                <span class="position style"
                    style="width: 0.24rem; height: 0.89rem; font-size: 0.75rem; left: 15.91rem; top: 0.14rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    t</span>
                <span class="position style"
                    style="width: 0.42rem; height: 0.89rem; font-size: 0.75rem; left: 16.15rem; top: 0.14rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">o</span>
                <span class="position style"
                    style="width: 0.82rem; height: 0.89rem; font-size: 0.75rem; left: 16.74rem; top: 0.14rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    be</span>
                <span class="position style"
                    style="width: 1.98rem; height: 0.89rem; font-size: 0.75rem; left: 17.73rem; top: 0.14rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    issued</span>
                <span class="position style"
                    style="width: 0.21rem; height: 0.89rem; font-size: 0.75rem; left: 19.88rem; top: 0.14rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    f</span>
                <span class="position style"
                    style="width: 0.65rem; height: 0.89rem; font-size: 0.75rem; left: 20.08rem; top: 0.14rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">or</span>
                <span class="position style"
                    style="width: 2.02rem; height: 0.89rem; font-size: 0.75rem; left: 20.90rem; top: 0.14rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    me/us</span>
                <span class="position style"
                    style="width: 0.14rem; height: 0.89rem; font-size: 0.75rem; left: 22.91rem; top: 0.14rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">.</span>
            </p>
            <div class="group" style="width: 0.35rem; height: 0.35rem; display: block; left: 4.99rem; top: 52.18rem;">
                <svg viewbox="0.000000, 0.000000, 3.550000, 3.500000" class="graphic"
                    style="width: 0.35rem; height: 0.35rem; display: block; z-index: -10; left: 0.00rem; top: 0.00rem;">
                    <path fill="#58595b" fill-opacity="1.000000" d="M 3.507 0 L 0 0 L 0 3.457 L 3.507 3.457 L 3.507 0 Z"
                        stroke="none" />
                </svg>
            </div>
            <img src="data:image/png;base64,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"
                class="image"
                style="width: 41.61rem; height: 0.73rem; display: block; z-index: 0; left: 6.20rem; top: 52.14rem;" />
            <p class="paragraph body-text"
                style="width: 51.82rem; height: 0.72rem; font-size: 0.30rem; left: 4.98rem; top: 52.14rem; text-align: left; font-family: 'pro', serif;">
            </p>
            <p class="paragraph body-text"
                style="width: 41.69rem; height: 1.80rem; font-size: 0.75rem; left: 6.13rem; top: 52.87rem; text-align: left; font-family: 'pro', serif; color: #58595b; font-weight: 300;">
                <span class="position style"
                    style="width: 0.51rem; height: 0.89rem; left: 0.01rem; top: 0.03rem; transform: ScaleX(1.05);">D</span>
                <span class="position style"
                    style="width: 1.23rem; height: 0.89rem; left: 0.52rem; top: 0.03rem; transform: ScaleX(1.05);">ebit</span>
                <span class="position style"
                    style="width: 0.45rem; height: 0.89rem; left: 1.92rem; top: 0.03rem; transform: ScaleX(1.05);">
                    C</span>
                <span class="position style"
                    style="width: 0.36rem; height: 0.89rem; left: 2.37rem; top: 0.03rem; transform: ScaleX(1.05);">a</span>
                <span class="position style"
                    style="width: 0.65rem; height: 0.89rem; left: 2.72rem; top: 0.03rem; transform: ScaleX(1.05);">rd</span>
                <span class="position style"
                    style="width: 0.14rem; height: 0.89rem; left: 3.37rem; top: 0.03rem; transform: ScaleX(1.05);">.</span>
                <span class="position style"
                    style="width: 1.09rem; height: 0.89rem; left: 3.68rem; top: 0.03rem; transform: ScaleX(1.05);">
                    I/W</span>
                <span class="position style"
                    style="width: 0.38rem; height: 0.89rem; left: 4.75rem; top: 0.03rem; transform: ScaleX(1.05);">e</span>
                <span class="position style"
                    style="width: 0.79rem; height: 0.89rem; left: 5.30rem; top: 0.03rem; transform: ScaleX(1.05);">
                    ag</span>
                <span class="position style"
                    style="width: 0.23rem; height: 0.89rem; left: 6.08rem; top: 0.03rem; transform: ScaleX(1.05);">r</span>
                <span class="position style"
                    style="width: 0.76rem; height: 0.89rem; left: 6.31rem; top: 0.03rem; transform: ScaleX(1.05);">ee</span>
                <span class="position style"
                    style="width: 1.27rem; height: 0.89rem; left: 7.25rem; top: 0.03rem; transform: ScaleX(1.05);">
                    that</span>
                <span class="position style"
                    style="width: 0.65rem; height: 0.89rem; left: 8.68rem; top: 0.03rem; transform: ScaleX(1.05);">
                    m</span>
                <span class="position style"
                    style="width: 1.69rem; height: 0.89rem; left: 9.32rem; top: 0.03rem; transform: ScaleX(1.05);">y/our</span>
                <span class="position style"
                    style="width: 0.51rem; height: 0.89rem; left: 11.19rem; top: 0.03rem; transform: ScaleX(1.05);">
                    D</span>
                <span class="position style"
                    style="width: 1.23rem; height: 0.89rem; left: 11.70rem; top: 0.03rem; transform: ScaleX(1.05);">ebit</span>
                <span class="position style"
                    style="width: 0.94rem; height: 0.89rem; left: 13.10rem; top: 0.03rem; transform: ScaleX(1.05);">
                    car</span>
                <span class="position style"
                    style="width: 1.14rem; height: 0.89rem; left: 14.04rem; top: 0.03rem; transform: ScaleX(1.05);">d(s)</span>
                <span class="position style"
                    style="width: 1.07rem; height: 0.89rem; left: 15.35rem; top: 0.03rem; transform: ScaleX(1.05);">
                    will</span>
                <span class="position style"
                    style="width: 1.36rem; height: 0.89rem; left: 16.59rem; top: 0.03rem; transform: ScaleX(1.05);">
                    only</span>
                <span class="position style"
                    style="width: 0.82rem; height: 0.89rem; left: 18.12rem; top: 0.03rem; transform: ScaleX(1.05);">
                    be</span>
                <span class="position style"
                    style="width: 1.52rem; height: 0.89rem; left: 19.11rem; top: 0.03rem; transform: ScaleX(1.05);">
                    used</span>
                <span class="position style"
                    style="width: 2.05rem; height: 0.89rem; left: 20.80rem; top: 0.03rem; transform: ScaleX(1.05);">
                    subjec</span>
                <span class="position style"
                    style="width: 0.24rem; height: 0.89rem; left: 22.86rem; top: 0.03rem; transform: ScaleX(1.05);">t</span>
                <span class="position style"
                    style="width: 0.24rem; height: 0.89rem; left: 23.28rem; top: 0.03rem; transform: ScaleX(1.05);">
                    t</span>
                <span class="position style"
                    style="width: 0.42rem; height: 0.89rem; left: 23.52rem; top: 0.03rem; transform: ScaleX(1.05);">o</span>
                <span class="position style"
                    style="width: 1.05rem; height: 0.89rem; left: 24.11rem; top: 0.03rem; transform: ScaleX(1.05);">
                    the</span>
                <span class="position style"
                    style="width: 3.25rem; height: 0.89rem; left: 25.33rem; top: 0.03rem; transform: ScaleX(1.05);">
                    applicable</span>
                <span class="position style"
                    style="width: 0.51rem; height: 0.89rem; left: 28.75rem; top: 0.03rem; transform: ScaleX(1.05);">
                    D</span>
                <span class="position style"
                    style="width: 1.23rem; height: 0.89rem; left: 29.27rem; top: 0.03rem; transform: ScaleX(1.05);">ebit</span>
                <span class="position style"
                    style="width: 0.45rem; height: 0.89rem; left: 30.67rem; top: 0.03rem; transform: ScaleX(1.05);">
                    C</span>
                <span class="position style"
                    style="width: 0.59rem; height: 0.89rem; left: 31.11rem; top: 0.03rem; transform: ScaleX(1.05);">ar</span>
                <span class="position style"
                    style="width: 2.48rem; height: 0.89rem; left: 31.69rem; top: 0.03rem; transform: ScaleX(1.05);">dholder</span>
                <span class="position style"
                    style="width: 0.37rem; height: 0.89rem; left: 34.31rem; top: 0.03rem; transform: ScaleX(1.05);">
                    T</span>
                <span class="position style"
                    style="width: 0.61rem; height: 0.89rem; left: 34.63rem; top: 0.03rem; transform: ScaleX(1.05);">er</span>
                <span class="position style"
                    style="width: 0.94rem; height: 0.89rem; left: 35.25rem; top: 0.03rem; transform: ScaleX(1.05);">ms</span>
                <span class="position style"
                    style="width: 1.21rem; height: 0.89rem; left: 36.36rem; top: 0.03rem; transform: ScaleX(1.05);">
                    and</span>
                <span class="position style"
                    style="width: 0.45rem; height: 0.89rem; left: 37.74rem; top: 0.03rem; transform: ScaleX(1.05);">
                    C</span>
                <span class="position style"
                    style="width: 2.98rem; height: 0.89rem; left: 38.19rem; top: 0.03rem; transform: ScaleX(1.05);">onditions</span>
                <span class="position style"
                    style="width: 0.57rem; height: 0.89rem; left: 0.01rem; top: 0.92rem; transform: ScaleX(1.05);">
                    (a</span>
                <span class="position style"
                    style="width: 2.39rem; height: 0.89rem; left: 0.57rem; top: 0.92rem; transform: ScaleX(1.05);">vailable</span>
                <span class="position style"
                    style="width: 0.44rem; height: 0.89rem; left: 3.13rem; top: 0.92rem; transform: ScaleX(1.05);">
                    fr</span>
                <span class="position style"
                    style="width: 1.06rem; height: 0.89rem; left: 3.56rem; top: 0.92rem; transform: ScaleX(1.05);">om</span>
                <span class="position style"
                    style="width: 1.18rem; height: 0.89rem; left: 4.80rem; top: 0.92rem; transform: ScaleX(1.05);">
                    MIB</span>
                <span class="position style"
                    style="width: 0.57rem; height: 0.89rem; left: 6.15rem; top: 0.92rem; transform: ScaleX(1.05);">
                    w</span>
                <span class="position style"
                    style="width: 1.52rem; height: 0.89rem; left: 6.71rem; top: 0.92rem; transform: ScaleX(1.05);">ebsit</span>
                <span class="position style"
                    style="width: 0.59rem; height: 0.89rem; left: 8.23rem; top: 0.92rem; transform: ScaleX(1.05);">e)</span>
                <span class="position style"
                    style="width: 1.21rem; height: 0.89rem; left: 8.99rem; top: 0.92rem; transform: ScaleX(1.05);">
                    and</span>
                <span class="position style"
                    style="width: 1.70rem; height: 0.89rem; left: 10.37rem; top: 0.92rem; transform: ScaleX(1.05);">
                    other</span>
                <span class="position style"
                    style="width: 3.25rem; height: 0.89rem; left: 12.24rem; top: 0.92rem; transform: ScaleX(1.05);">
                    applicable</span>
                <span class="position style"
                    style="width: 2.56rem; height: 0.89rem; left: 15.67rem; top: 0.92rem; transform: ScaleX(1.05);">
                    account</span>
                <span class="position style"
                    style="width: 0.24rem; height: 0.89rem; left: 18.41rem; top: 0.92rem; transform: ScaleX(1.05);">
                    t</span>
                <span class="position style"
                    style="width: 0.61rem; height: 0.89rem; left: 18.65rem; top: 0.92rem; transform: ScaleX(1.05);">er</span>
                <span class="position style"
                    style="width: 0.94rem; height: 0.89rem; left: 19.27rem; top: 0.92rem; transform: ScaleX(1.05);">ms</span>
                <span class="position style"
                    style="width: 1.21rem; height: 0.89rem; left: 20.38rem; top: 0.92rem; transform: ScaleX(1.05);">
                    and</span>
                <span class="position style"
                    style="width: 3.33rem; height: 0.89rem; left: 21.76rem; top: 0.92rem; transform: ScaleX(1.05);">
                    conditions</span>
                <span class="position style"
                    style="width: 1.98rem; height: 0.89rem; left: 25.26rem; top: 0.92rem; transform: ScaleX(1.05);">
                    issued</span>
                <span class="position style"
                    style="width: 0.43rem; height: 0.89rem; left: 27.42rem; top: 0.92rem; transform: ScaleX(1.05);">
                    b</span>
                <span class="position style"
                    style="width: 0.35rem; height: 0.89rem; left: 27.85rem; top: 0.92rem; transform: ScaleX(1.05);">y</span>
                <span class="position style"
                    style="width: 1.05rem; height: 0.89rem; left: 28.37rem; top: 0.92rem; transform: ScaleX(1.05);">
                    the</span>
                <span class="position style"
                    style="width: 1.18rem; height: 0.89rem; left: 29.59rem; top: 0.92rem; transform: ScaleX(1.05);">
                    MIB</span>
                <span class="position style"
                    style="width: 0.65rem; height: 0.89rem; left: 30.94rem; top: 0.92rem; transform: ScaleX(1.05);">
                    as</span>
                <span class="position style"
                    style="width: 3.05rem; height: 0.89rem; left: 31.76rem; top: 0.92rem; transform: ScaleX(1.05);">
                    amended</span>
                <span class="position style"
                    style="width: 0.44rem; height: 0.89rem; left: 34.98rem; top: 0.92rem; transform: ScaleX(1.05);">
                    fr</span>
                <span class="position style"
                    style="width: 1.06rem; height: 0.89rem; left: 35.41rem; top: 0.92rem; transform: ScaleX(1.05);">om</span>
                <span class="position style"
                    style="width: 1.44rem; height: 0.89rem; left: 36.65rem; top: 0.92rem; transform: ScaleX(1.05);">
                    time</span>
                <span class="position style"
                    style="width: 0.24rem; height: 0.89rem; left: 38.26rem; top: 0.92rem; transform: ScaleX(1.05);">
                    t</span>
                <span class="position style"
                    style="width: 0.42rem; height: 0.89rem; left: 38.50rem; top: 0.92rem; transform: ScaleX(1.05);">o</span>
                <span class="position style"
                    style="width: 1.44rem; height: 0.89rem; left: 39.09rem; top: 0.92rem; transform: ScaleX(1.05);">
                    time</span>
                <span class="position style"
                    style="width: 0.14rem; height: 0.89rem; left: 40.52rem; top: 0.92rem; transform: ScaleX(1.05);">.</span>
            </p>
            <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAkAAAAJCAYAAADgkQYQAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAAGklEQVQYlWOMiIz+z0AAMBFSMKqIgYGBgQEAr1gCHcU/2B0AAAAASUVORK5CYII="
                class="image"
                style="width: 0.35rem; height: 0.35rem; display: block; z-index: 0; left: 4.99rem; top: 54.86rem;" />
            <p class="paragraph body-text"
                style="width: 51.82rem; height: 0.89rem; font-size: 1.10rem; left: 4.98rem; top: 54.67rem; text-align: left; font-family: pro, serif; font-weight: 300;">
                <span class="position" style="width: 1.09rem; height: 0.89rem; left: 1.15rem; top: 0.00rem;">
                    <span class="style"></span>
                    <span class="style"
                        style="font-family: 'pro', serif; font-size: 1.00rem; transform: ScaleX(1.50);">
                    </span>
                    <span class="style"
                        style="font-family: 'pro', serif; font-size: 0.75rem; color: #58595b; transform: ScaleX(1.05);">I/W</span>
                </span>
                <span class="position style"
                    style="width: 0.38rem; height: 0.89rem; font-size: 0.75rem; left: 2.22rem; top: 0.00rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">e</span>
                <span class="position style"
                    style="width: 2.12rem; height: 0.89rem; font-size: 0.75rem; left: 2.78rem; top: 0.00rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    accept</span>
                <span class="position style"
                    style="width: 0.24rem; height: 0.89rem; font-size: 0.75rem; left: 5.07rem; top: 0.00rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    t</span>
                <span class="position style"
                    style="width: 0.42rem; height: 0.89rem; font-size: 0.75rem; left: 5.31rem; top: 0.00rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">o</span>
                <span class="position style"
                    style="width: 0.82rem; height: 0.89rem; font-size: 0.75rem; left: 5.90rem; top: 0.00rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    be</span>
                <span class="position style"
                    style="width: 2.12rem; height: 0.89rem; font-size: 0.75rem; left: 6.89rem; top: 0.00rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    bound</span>
                <span class="position style"
                    style="width: 0.43rem; height: 0.89rem; font-size: 0.75rem; left: 9.19rem; top: 0.00rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    b</span>
                <span class="position style"
                    style="width: 0.35rem; height: 0.89rem; font-size: 0.75rem; left: 9.62rem; top: 0.00rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">y</span>
                <span class="position style"
                    style="width: 1.05rem; height: 0.89rem; font-size: 0.75rem; left: 10.14rem; top: 0.00rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    the</span>
                <span class="position style"
                    style="width: 1.06rem; height: 0.89rem; font-size: 0.75rem; left: 11.36rem; top: 0.00rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    List</span>
                <span class="position style"
                    style="width: 0.63rem; height: 0.89rem; font-size: 0.75rem; left: 12.59rem; top: 0.00rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    of</span>
                <span class="position style"
                    style="width: 1.51rem; height: 0.89rem; font-size: 0.75rem; left: 13.39rem; top: 0.00rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    Bank</span>
                <span class="position style"
                    style="width: 1.46rem; height: 0.89rem; font-size: 0.75rem; left: 15.07rem; top: 0.00rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    Char</span>
                <span class="position style"
                    style="width: 1.10rem; height: 0.89rem; font-size: 0.75rem; left: 16.52rem; top: 0.00rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">ges</span>
                <span class="position style"
                    style="width: 1.21rem; height: 0.89rem; font-size: 0.75rem; left: 17.80rem; top: 0.00rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    and</span>
                <span class="position style"
                    style="width: 0.21rem; height: 0.89rem; font-size: 0.75rem; left: 19.18rem; top: 0.00rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    f</span>
                <span class="position style"
                    style="width: 1.06rem; height: 0.89rem; font-size: 0.75rem; left: 19.38rem; top: 0.00rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">ees</span>
                <span class="position style"
                    style="width: 0.65rem; height: 0.89rem; font-size: 0.75rem; left: 20.61rem; top: 0.00rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    as</span>
                <span class="position style"
                    style="width: 3.05rem; height: 0.89rem; font-size: 0.75rem; left: 21.43rem; top: 0.00rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    amended</span>
                <span class="position style"
                    style="width: 0.44rem; height: 0.89rem; font-size: 0.75rem; left: 24.65rem; top: 0.00rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    fr</span>
                <span class="position style"
                    style="width: 1.06rem; height: 0.89rem; font-size: 0.75rem; left: 25.08rem; top: 0.00rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">om</span>
                <span class="position style"
                    style="width: 1.44rem; height: 0.89rem; font-size: 0.75rem; left: 26.32rem; top: 0.00rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    time</span>
                <span class="position style"
                    style="width: 0.24rem; height: 0.89rem; font-size: 0.75rem; left: 27.93rem; top: 0.00rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    t</span>
                <span class="position style"
                    style="width: 0.42rem; height: 0.89rem; font-size: 0.75rem; left: 28.17rem; top: 0.00rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">o</span>
                <span class="position style"
                    style="width: 1.44rem; height: 0.89rem; font-size: 0.75rem; left: 28.76rem; top: 0.00rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    time</span>
                <span class="position style"
                    style="width: 0.14rem; height: 0.89rem; font-size: 0.75rem; left: 30.19rem; top: 0.00rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">.</span>
            </p>
            <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAkAAAAJCAYAAADgkQYQAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAAGklEQVQYlWOMiIz+z0AAMBFSMKqIgYGBgQEAr1gCHcU/2B0AAAAASUVORK5CYII="
                class="image"
                style="width: 0.35rem; height: 0.35rem; display: block; z-index: 0; left: 4.99rem; top: 55.80rem;" />
            <p class="paragraph body-text"
                style="width: 42.84rem; height: 2.30rem; font-size: 1.10rem; left: 4.98rem; top: 55.03rem; text-align: left; font-family: pro, serif; font-weight: 300;">
                <span class="position" style="width: 1.09rem; height: 0.89rem; left: 1.16rem; top: 0.53rem;">
                    <span class="style"></span>
                    <span class="style"
                        style="font-family: 'pro', serif; font-size: 1.00rem; transform: ScaleX(1.05);">
                    </span>
                    <span class="style"
                        style="font-family: 'pro', serif; font-size: 0.75rem; color: #58595b; transform: ScaleX(1.05);">I/W</span>
                </span>
                <span class="position style"
                    style="width: 0.38rem; height: 0.89rem; font-size: 0.75rem; left: 2.23rem; top: 0.53rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">e</span>
                <span class="position style"
                    style="width: 2.12rem; height: 0.89rem; font-size: 0.75rem; left: 2.78rem; top: 0.53rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    accept</span>
                <span class="position style"
                    style="width: 1.27rem; height: 0.89rem; font-size: 0.75rem; left: 5.08rem; top: 0.53rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    that</span>
                <span class="position style"
                    style="width: 1.05rem; height: 0.89rem; font-size: 0.75rem; left: 6.52rem; top: 0.53rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    the</span>
                <span class="position style"
                    style="width: 1.88rem; height: 0.89rem; font-size: 0.75rem; left: 7.74rem; top: 0.53rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    usage</span>
                <span class="position style"
                    style="width: 0.63rem; height: 0.89rem; font-size: 0.75rem; left: 9.79rem; top: 0.53rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    of</span>
                <span class="position style"
                    style="width: 1.05rem; height: 0.89rem; font-size: 0.75rem; left: 10.59rem; top: 0.53rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    the</span>
                <span class="position style"
                    style="width: 0.51rem; height: 0.89rem; font-size: 0.75rem; left: 11.81rem; top: 0.53rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    D</span>
                <span class="position style"
                    style="width: 1.23rem; height: 0.89rem; font-size: 0.75rem; left: 12.32rem; top: 0.53rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">ebit</span>
                <span class="position style"
                    style="width: 0.45rem; height: 0.89rem; font-size: 0.75rem; left: 13.72rem; top: 0.53rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    C</span>
                <span class="position style"
                    style="width: 0.59rem; height: 0.89rem; font-size: 0.75rem; left: 14.17rem; top: 0.53rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">ar</span>
                <span class="position style"
                    style="width: 0.43rem; height: 0.89rem; font-size: 0.75rem; left: 14.75rem; top: 0.53rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">d</span>
                <span class="position style"
                    style="width: 1.07rem; height: 0.89rem; font-size: 0.75rem; left: 15.35rem; top: 0.53rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    will</span>
                <span class="position style"
                    style="width: 0.82rem; height: 0.89rem; font-size: 0.75rem; left: 16.59rem; top: 0.53rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    be</span>
                <span class="position style"
                    style="width: 3.19rem; height: 0.89rem; font-size: 0.75rem; left: 17.58rem; top: 0.53rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    construed</span>
                <span class="position style"
                    style="width: 0.43rem; height: 0.89rem; font-size: 0.75rem; left: 20.95rem; top: 0.53rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    b</span>
                <span class="position style"
                    style="width: 0.35rem; height: 0.89rem; font-size: 0.75rem; left: 21.38rem; top: 0.53rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">y</span>
                <span class="position style"
                    style="width: 1.05rem; height: 0.89rem; font-size: 0.75rem; left: 21.90rem; top: 0.53rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    the</span>
                <span class="position style"
                    style="width: 1.51rem; height: 0.89rem; font-size: 0.75rem; left: 23.12rem; top: 0.53rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    Bank</span>
                <span class="position style"
                    style="width: 0.65rem; height: 0.89rem; font-size: 0.75rem; left: 24.80rem; top: 0.53rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    as</span>
                <span class="position style"
                    style="width: 3.64rem; height: 0.89rem; font-size: 0.75rem; left: 25.63rem; top: 0.53rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    acceptance</span>
                <span class="position style"
                    style="width: 0.63rem; height: 0.89rem; font-size: 0.75rem; left: 29.43rem; top: 0.53rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    of</span>
                <span class="position style"
                    style="width: 1.05rem; height: 0.89rem; font-size: 0.75rem; left: 30.23rem; top: 0.53rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    the</span>
                <span class="position style"
                    style="width: 3.25rem; height: 0.89rem; font-size: 0.75rem; left: 31.45rem; top: 0.53rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    applicable</span>
                <span class="position style"
                    style="width: 0.24rem; height: 0.89rem; font-size: 0.75rem; left: 34.88rem; top: 0.53rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    t</span>
                <span class="position style"
                    style="width: 0.61rem; height: 0.89rem; font-size: 0.75rem; left: 35.12rem; top: 0.53rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">er</span>
                <span class="position style"
                    style="width: 0.94rem; height: 0.89rem; font-size: 0.75rem; left: 35.74rem; top: 0.53rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">ms</span>
                <span class="position style"
                    style="width: 1.21rem; height: 0.89rem; font-size: 0.75rem; left: 36.85rem; top: 0.53rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    and</span>
                <span class="position style"
                    style="width: 3.33rem; height: 0.89rem; font-size: 0.75rem; left: 38.23rem; top: 0.53rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    conditions</span>
                <span class="position style"
                    style="width: 0.65rem; height: 0.89rem; font-size: 0.75rem; left: 41.73rem; top: 0.53rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    as</span>
                <span class="position style"
                    style="width: 1.14rem; height: 0.89rem; font-size: 0.75rem; left: 1.16rem; top: 1.41rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    stat</span>
                <span class="position style"
                    style="width: 0.81rem; height: 0.89rem; font-size: 0.75rem; left: 2.29rem; top: 1.41rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">ed</span>
                <span class="position style"
                    style="width: 1.21rem; height: 0.89rem; font-size: 0.75rem; left: 3.28rem; top: 1.41rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    abo</span>
                <span class="position style"
                    style="width: 0.36rem; height: 0.89rem; font-size: 0.75rem; left: 4.49rem; top: 1.41rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">v</span>
                <span class="position style"
                    style="width: 0.38rem; height: 0.89rem; font-size: 0.75rem; left: 4.84rem; top: 1.41rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">e</span>
                <span class="position style"
                    style="width: 0.14rem; height: 0.89rem; font-size: 0.75rem; left: 5.21rem; top: 1.41rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">.</span>
            </p>
            <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAkAAAAJCAYAAADgkQYQAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAAGklEQVQYlWOMiIz+z0AAMBFSMKqIgYGBgQEAr1gCHcU/2B0AAAAASUVORK5CYII="
                class="image"
                style="width: 0.35rem; height: 0.35rem; display: block; z-index: 0; left: 4.99rem; top: 57.58rem;" />
            <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAkAAAAJCAYAAADgkQYQAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAAGklEQVQYlWOMiIz+z0AAMBFSMKqIgYGBgQEAr1gCHcU/2B0AAAAASUVORK5CYII="
                class="image"
                style="width: 0.35rem; height: 0.35rem; display: block; z-index: 0; left: 4.99rem; top: 58.52rem;" />
            <p class="paragraph body-text"
                style="width: 29.86rem; height: 2.30rem; font-size: 1.10rem; left: 4.98rem; top: 56.80rem; text-align: left; font-family: pro, serif; font-weight: 300;">
                <span class="position" style="width: 1.09rem; height: 0.89rem; left: 1.15rem; top: 0.53rem;">
                    <span class="style"></span>
                    <span class="style"
                        style="font-family: 'pro', serif; font-size: 1.00rem; transform: ScaleX(1.05);">
                    </span>
                    <span class="style"
                        style="font-family: 'pro', serif; font-size: 0.75rem; color: #58595b; transform: ScaleX(1.05);">I/W</span>
                </span>
                <span class="position style"
                    style="width: 0.38rem; height: 0.89rem; font-size: 0.75rem; left: 2.22rem; top: 0.53rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">e</span>
                <span class="position style"
                    style="width: 1.04rem; height: 0.89rem; font-size: 0.75rem; left: 2.78rem; top: 0.53rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    her</span>
                <span class="position style"
                    style="width: 0.82rem; height: 0.89rem; font-size: 0.75rem; left: 3.81rem; top: 0.53rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">eb</span>
                <span class="position style"
                    style="width: 0.35rem; height: 0.89rem; font-size: 0.75rem; left: 4.62rem; top: 0.53rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">y</span>
                <span class="position style"
                    style="width: 1.16rem; height: 0.89rem; font-size: 0.75rem; left: 5.14rem; top: 0.53rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    war</span>
                <span class="position style"
                    style="width: 1.26rem; height: 0.89rem; font-size: 0.75rem; left: 6.31rem; top: 0.53rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">rant</span>
                <span class="position style"
                    style="width: 1.27rem; height: 0.89rem; font-size: 0.75rem; left: 7.73rem; top: 0.53rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    that</span>
                <span class="position style"
                    style="width: 1.05rem; height: 0.89rem; font-size: 0.75rem; left: 9.17rem; top: 0.53rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    the</span>
                <span class="position style"
                    style="width: 1.21rem; height: 0.89rem; font-size: 0.75rem; left: 10.39rem; top: 0.53rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    abo</span>
                <span class="position style"
                    style="width: 0.36rem; height: 0.89rem; font-size: 0.75rem; left: 11.60rem; top: 0.53rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">v</span>
                <span class="position style"
                    style="width: 0.38rem; height: 0.89rem; font-size: 0.75rem; left: 11.95rem; top: 0.53rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">e</span>
                <span class="position style"
                    style="width: 0.79rem; height: 0.89rem; font-size: 0.75rem; left: 12.51rem; top: 0.53rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    inf</span>
                <span class="position style"
                    style="width: 0.65rem; height: 0.89rem; font-size: 0.75rem; left: 13.29rem; top: 0.53rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">or</span>
                <span class="position style"
                    style="width: 2.25rem; height: 0.89rem; font-size: 0.75rem; left: 13.95rem; top: 0.53rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">mation</span>
                <span class="position style"
                    style="width: 0.43rem; height: 0.89rem; font-size: 0.75rem; left: 16.37rem; top: 0.53rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    g</span>
                <span class="position style"
                    style="width: 0.52rem; height: 0.89rem; font-size: 0.75rem; left: 16.80rem; top: 0.53rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">iv</span>
                <span class="position style"
                    style="width: 0.80rem; height: 0.89rem; font-size: 0.75rem; left: 17.31rem; top: 0.53rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">en</span>
                <span class="position style"
                    style="width: 0.59rem; height: 0.89rem; font-size: 0.75rem; left: 18.29rem; top: 0.53rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    in</span>
                <span class="position style"
                    style="width: 1.12rem; height: 0.89rem; font-size: 0.75rem; left: 19.05rem; top: 0.53rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    this</span>
                <span class="position style"
                    style="width: 3.52rem; height: 0.89rem; font-size: 0.75rem; left: 20.35rem; top: 0.53rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    application</span>
                <span class="position style"
                    style="width: 0.46rem; height: 0.89rem; font-size: 0.75rem; left: 24.04rem; top: 0.53rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    is</span>
                <span class="position style"
                    style="width: 1.28rem; height: 0.89rem; font-size: 0.75rem; left: 24.67rem; top: 0.53rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    true</span>
                <span class="position style"
                    style="width: 1.21rem; height: 0.89rem; font-size: 0.75rem; left: 26.12rem; top: 0.53rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    and</span>
                <span class="position style"
                    style="width: 1.00rem; height: 0.89rem; font-size: 0.75rem; left: 27.50rem; top: 0.53rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    cor</span>
                <span class="position style"
                    style="width: 0.23rem; height: 0.89rem; font-size: 0.75rem; left: 28.51rem; top: 0.53rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">r</span>
                <span class="position style"
                    style="width: 0.73rem; height: 0.89rem; font-size: 0.75rem; left: 28.73rem; top: 0.53rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">ec</span>
                <span class="position style"
                    style="width: 0.38rem; height: 0.89rem; font-size: 0.75rem; left: 29.47rem; top: 0.53rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">t.</span>
                <span class="position" style="width: 1.09rem; height: 0.89rem; left: 1.16rem; top: 1.41rem;">
                    <span class="style"
                        style="font-family: 'pro', serif; font-size: 0.75rem; color: #58595b; transform: ScaleX(1.05);">
                    </span>
                    <span class="style" style="font-family: 'pro', serif; font-size: 0.75rem; color: #58595b;"></span>
                    <span class="style"
                        style="font-family: 'pro', serif; font-size: 0.75rem; color: #58595b; font-weight: 400; transform: ScaleX(1.05);">
                    </span>
                    <span class="style"
                        style="font-family: 'pro', serif; font-size: 0.75rem; color: #58595b; transform: ScaleX(1.05);">I/W</span>
                </span>
                <span class="position style"
                    style="width: 0.38rem; height: 0.89rem; font-size: 0.75rem; left: 2.22rem; top: 1.41rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">e</span>
                <span class="position style"
                    style="width: 2.12rem; height: 0.89rem; font-size: 0.75rem; left: 2.78rem; top: 1.41rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    accept</span>
                <span class="position style"
                    style="width: 1.27rem; height: 0.89rem; font-size: 0.75rem; left: 5.07rem; top: 1.41rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    that</span>
                <span class="position style"
                    style="width: 0.51rem; height: 0.89rem; font-size: 0.75rem; left: 6.51rem; top: 1.41rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    D</span>
                <span class="position style"
                    style="width: 1.23rem; height: 0.89rem; font-size: 0.75rem; left: 7.03rem; top: 1.41rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">ebit</span>
                <span class="position style"
                    style="width: 0.45rem; height: 0.89rem; font-size: 0.75rem; left: 8.43rem; top: 1.41rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    C</span>
                <span class="position style"
                    style="width: 0.59rem; height: 0.89rem; font-size: 0.75rem; left: 8.87rem; top: 1.41rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">ar</span>
                <span class="position style"
                    style="width: 1.14rem; height: 0.89rem; font-size: 0.75rem; left: 9.45rem; top: 1.41rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">d(s)</span>
                <span class="position style"
                    style="width: 1.07rem; height: 0.89rem; font-size: 0.75rem; left: 10.77rem; top: 1.41rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    will</span>
                <span class="position style"
                    style="width: 0.82rem; height: 0.89rem; font-size: 0.75rem; left: 12.01rem; top: 1.41rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    be</span>
                <span class="position style"
                    style="width: 1.98rem; height: 0.89rem; font-size: 0.75rem; left: 13.00rem; top: 1.41rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    issued</span>
                <span class="position style"
                    style="width: 0.60rem; height: 0.89rem; font-size: 0.75rem; left: 15.15rem; top: 1.41rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    at</span>
                <span class="position style"
                    style="width: 1.05rem; height: 0.89rem; font-size: 0.75rem; left: 15.93rem; top: 1.41rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    the</span>
                <span class="position style"
                    style="width: 1.26rem; height: 0.89rem; font-size: 0.75rem; left: 17.15rem; top: 1.41rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    sole</span>
                <span class="position style"
                    style="width: 1.47rem; height: 0.89rem; font-size: 0.75rem; left: 18.58rem; top: 1.41rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    discr</span>
                <span class="position style"
                    style="width: 1.63rem; height: 0.89rem; font-size: 0.75rem; left: 20.04rem; top: 1.41rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">etion</span>
                <span class="position style"
                    style="width: 0.63rem; height: 0.89rem; font-size: 0.75rem; left: 21.85rem; top: 1.41rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    of</span>
                <span class="position style"
                    style="width: 1.05rem; height: 0.89rem; font-size: 0.75rem; left: 22.65rem; top: 1.41rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    the</span>
                <span class="position style"
                    style="width: 1.51rem; height: 0.89rem; font-size: 0.75rem; left: 23.87rem; top: 1.41rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    Bank</span>
                <span class="position style"
                    style="width: 0.14rem; height: 0.89rem; font-size: 0.75rem; left: 25.39rem; top: 1.41rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">.</span>
            </p>
            <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAkAAAAJCAYAAADgkQYQAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAAGklEQVQYlWOMiIz+z0AAMBFSMKqIgYGBgQEAr1gCHcU/2B0AAAAASUVORK5CYII="
                class="image"
                style="width: 0.35rem; height: 0.35rem; display: block; z-index: 0; left: 4.99rem; top: 59.42rem;" />
            <p class="paragraph body-text"
                style="width: 43.55rem; height: 2.39rem; font-size: 1.10rem; left: 4.98rem; top: 58.58rem; text-align: left; font-family: pro, serif; font-weight: 300;">
                <span class="position" style="width: 0.37rem; height: 0.89rem; left: 1.16rem; top: 0.62rem;">
                    <span class="style"></span>
                    <span class="style"
                        style="font-family: 'pro', serif; font-size: 1.00rem; transform: ScaleX(1.05);">
                    </span>
                    <span class="style"
                        style="font-family: 'pro', serif; font-size: 0.75rem; color: #58595b; transform: ScaleX(1.05);">T</span>
                </span>
                <span class="position style"
                    style="width: 0.82rem; height: 0.89rem; font-size: 0.75rem; left: 1.52rem; top: 0.62rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">hir</span>
                <span class="position style"
                    style="width: 0.43rem; height: 0.89rem; font-size: 0.75rem; left: 2.33rem; top: 0.62rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">d</span>
                <span class="position style"
                    style="width: 1.03rem; height: 0.89rem; font-size: 0.75rem; left: 2.93rem; top: 0.62rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    par</span>
                <span class="position style"
                    style="width: 0.24rem; height: 0.89rem; font-size: 0.75rem; left: 3.98rem; top: 0.62rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">t</span>
                <span class="position style"
                    style="width: 0.35rem; height: 0.89rem; font-size: 0.75rem; left: 4.23rem; top: 0.62rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">y</span>
                <span class="position style"
                    style="width: 1.84rem; height: 0.89rem; font-size: 0.75rem; left: 4.75rem; top: 0.62rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    collec</span>
                <span class="position style"
                    style="width: 1.26rem; height: 0.89rem; font-size: 0.75rem; left: 6.60rem; top: 0.62rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">ting</span>
                <span class="position style"
                    style="width: 1.05rem; height: 0.89rem; font-size: 0.75rem; left: 8.03rem; top: 0.62rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    the</span>
                <span class="position style"
                    style="width: 0.94rem; height: 0.89rem; font-size: 0.75rem; left: 9.25rem; top: 0.62rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    car</span>
                <span class="position style"
                    style="width: 0.43rem; height: 0.89rem; font-size: 0.75rem; left: 10.18rem; top: 0.62rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">d</span>
                <span class="position style"
                    style="width: 1.60rem; height: 0.89rem; font-size: 0.75rem; left: 10.78rem; top: 0.62rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    must</span>
                <span class="position style"
                    style="width: 0.67rem; height: 0.89rem; font-size: 0.75rem; left: 12.56rem; top: 0.62rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    pr</span>
                <span class="position style"
                    style="width: 1.72rem; height: 0.89rem; font-size: 0.75rem; left: 13.22rem; top: 0.62rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">esent</span>
                <span class="position style"
                    style="width: 1.45rem; height: 0.89rem; font-size: 0.75rem; left: 15.11rem; top: 0.62rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    their</span>
                <span class="position style"
                    style="width: 0.65rem; height: 0.89rem; font-size: 0.75rem; left: 16.73rem; top: 0.62rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    or</span>
                <span class="position style"
                    style="width: 0.59rem; height: 0.89rem; font-size: 0.75rem; left: 17.39rem; top: 0.62rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">ig</span>
                <span class="position style"
                    style="width: 1.11rem; height: 0.89rem; font-size: 0.75rem; left: 17.98rem; top: 0.62rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">inal</span>
                <span class="position style"
                    style="width: 0.68rem; height: 0.89rem; font-size: 0.75rem; left: 19.26rem; top: 0.62rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    ID</span>
                <span class="position style"
                    style="width: 0.45rem; height: 0.89rem; font-size: 0.75rem; left: 20.11rem; top: 0.62rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    C</span>
                <span class="position style"
                    style="width: 0.59rem; height: 0.89rem; font-size: 0.75rem; left: 20.56rem; top: 0.62rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">ar</span>
                <span class="position style"
                    style="width: 0.43rem; height: 0.89rem; font-size: 0.75rem; left: 21.14rem; top: 0.62rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">d</span>
                <span class="position style"
                    style="width: 0.28rem; height: 0.89rem; font-size: 0.75rem; left: 21.74rem; top: 0.62rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    /</span>
                <span class="position style"
                    style="width: 0.40rem; height: 0.89rem; font-size: 0.75rem; left: 22.19rem; top: 0.62rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    P</span>
                <span class="position style"
                    style="width: 2.03rem; height: 0.89rem; font-size: 0.75rem; left: 22.56rem; top: 0.62rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">asspor</span>
                <span class="position style"
                    style="width: 0.24rem; height: 0.89rem; font-size: 0.75rem; left: 24.61rem; top: 0.62rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">t</span>
                <span class="position style"
                    style="width: 1.21rem; height: 0.89rem; font-size: 0.75rem; left: 25.03rem; top: 0.62rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    and</span>
                <span class="position style"
                    style="width: 1.21rem; height: 0.89rem; font-size: 0.75rem; left: 26.41rem; top: 0.62rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    cop</span>
                <span class="position style"
                    style="width: 1.06rem; height: 0.89rem; font-size: 0.75rem; left: 27.61rem; top: 0.62rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">y(s)</span>
                <span class="position style"
                    style="width: 0.63rem; height: 0.89rem; font-size: 0.75rem; left: 28.84rem; top: 0.62rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    of</span>
                <span class="position style"
                    style="width: 1.05rem; height: 0.89rem; font-size: 0.75rem; left: 29.64rem; top: 0.62rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    the</span>
                <span class="position style"
                    style="width: 0.68rem; height: 0.89rem; font-size: 0.75rem; left: 30.86rem; top: 0.62rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    ID</span>
                <span class="position style"
                    style="width: 0.45rem; height: 0.89rem; font-size: 0.75rem; left: 31.71rem; top: 0.62rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    C</span>
                <span class="position style"
                    style="width: 0.59rem; height: 0.89rem; font-size: 0.75rem; left: 32.16rem; top: 0.62rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">ar</span>
                <span class="position style"
                    style="width: 0.43rem; height: 0.89rem; font-size: 0.75rem; left: 32.74rem; top: 0.62rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">d</span>
                <span class="position style"
                    style="width: 0.28rem; height: 0.89rem; font-size: 0.75rem; left: 33.34rem; top: 0.62rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    /</span>
                <span class="position style"
                    style="width: 0.40rem; height: 0.89rem; font-size: 0.75rem; left: 33.79rem; top: 0.62rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    P</span>
                <span class="position style"
                    style="width: 2.03rem; height: 0.89rem; font-size: 0.75rem; left: 34.16rem; top: 0.62rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">asspor</span>
                <span class="position style"
                    style="width: 0.24rem; height: 0.89rem; font-size: 0.75rem; left: 36.22rem; top: 0.62rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">t</span>
                <span class="position style"
                    style="width: 0.63rem; height: 0.89rem; font-size: 0.75rem; left: 36.63rem; top: 0.62rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    of</span>
                <span class="position style"
                    style="width: 1.05rem; height: 0.89rem; font-size: 0.75rem; left: 37.43rem; top: 0.62rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    the</span>
                <span class="position style"
                    style="width: 2.56rem; height: 0.89rem; font-size: 0.75rem; left: 38.65rem; top: 0.62rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    account</span>
                <span class="position style"
                    style="width: 2.05rem; height: 0.89rem; font-size: 0.75rem; left: 41.39rem; top: 0.62rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    holder</span>
                <span class="position style"
                    style="width: 0.60rem; height: 0.89rem; font-size: 0.75rem; left: 1.16rem; top: 1.51rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    at</span>
                <span class="position style"
                    style="width: 1.05rem; height: 0.89rem; font-size: 0.75rem; left: 1.93rem; top: 1.51rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    the</span>
                <span class="position style"
                    style="width: 1.44rem; height: 0.89rem; font-size: 0.75rem; left: 3.15rem; top: 1.51rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    time</span>
                <span class="position style"
                    style="width: 0.63rem; height: 0.89rem; font-size: 0.75rem; left: 4.76rem; top: 1.51rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    of</span>
                <span class="position style"
                    style="width: 1.84rem; height: 0.89rem; font-size: 0.75rem; left: 5.56rem; top: 1.51rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    collec</span>
                <span class="position style"
                    style="width: 1.26rem; height: 0.89rem; font-size: 0.75rem; left: 7.40rem; top: 1.51rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">ting</span>
                <span class="position style"
                    style="width: 0.14rem; height: 0.89rem; font-size: 0.75rem; left: 8.65rem; top: 1.51rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">.</span>
            </p>
            <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAkAAAAKCAYAAABmBXS+AAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAAF0lEQVQYlWOMiIz+z0AAMBFSMKqIeEUA/dMCH73UyAgAAAAASUVORK5CYII="
                class="image"
                style="width: 0.35rem; height: 0.35rem; display: block; z-index: 0; left: 4.99rem; top: 61.17rem;" />
            <p class="paragraph body-text"
                style="width: 43.56rem; height: 3.45rem; font-size: 1.10rem; left: 4.98rem; top: 60.19rem; text-align: left; font-family: pro, serif; font-weight: 300;">
                <span class="position" style="width: 0.38rem; height: 0.95rem; left: 1.16rem; top: 0.73rem;">
                    <span class="style"></span>
                    <span class="style"
                        style="font-family: 'pro', serif; font-size: 1.00rem; transform: ScaleX(1.05);">
                    </span>
                    <span class="style"
                        style="font-family: 'pro', serif; font-size: 0.75rem; color: #58595b; transform: ScaleX(1.05);">F</span>
                </span>
                <span class="position style"
                    style="width: 0.69rem; height: 0.95rem; font-size: 0.75rem; left: 1.52rem; top: 0.73rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">or</span>
                <span class="position style"
                    style="width: 1.14rem; height: 0.95rem; font-size: 0.75rem; left: 2.38rem; top: 0.73rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    thir</span>
                <span class="position style"
                    style="width: 0.44rem; height: 0.95rem; font-size: 0.75rem; left: 3.51rem; top: 0.73rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">d</span>
                <span class="position style"
                    style="width: 1.09rem; height: 0.95rem; font-size: 0.75rem; left: 4.12rem; top: 0.73rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    par</span>
                <span class="position style"
                    style="width: 0.26rem; height: 0.95rem; font-size: 0.75rem; left: 5.23rem; top: 0.73rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">t</span>
                <span class="position style"
                    style="width: 0.37rem; height: 0.95rem; font-size: 0.75rem; left: 5.49rem; top: 0.73rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">y</span>
                <span class="position style"
                    style="width: 0.99rem; height: 0.95rem; font-size: 0.75rem; left: 6.03rem; top: 0.73rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    car</span>
                <span class="position style"
                    style="width: 0.44rem; height: 0.95rem; font-size: 0.75rem; left: 7.02rem; top: 0.73rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">d</span>
                <span class="position style"
                    style="width: 0.35rem; height: 0.95rem; font-size: 0.75rem; left: 7.63rem; top: 0.73rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    c</span>
                <span class="position style"
                    style="width: 1.55rem; height: 0.95rem; font-size: 0.75rem; left: 7.98rem; top: 0.73rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">ollec</span>
                <span class="position style"
                    style="width: 1.32rem; height: 0.95rem; font-size: 0.75rem; left: 9.54rem; top: 0.73rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">tion</span>
                <span class="position style"
                    style="width: 0.14rem; height: 0.89rem; font-size: 0.75rem; left: 10.85rem; top: 0.79rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">:</span>
                <span class="position style"
                    style="width: 0.40rem; height: 0.89rem; font-size: 0.75rem; left: 11.16rem; top: 0.79rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    B</span>
                <span class="position style"
                    style="width: 0.35rem; height: 0.89rem; font-size: 0.75rem; left: 11.57rem; top: 0.79rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">y</span>
                <span class="position style"
                    style="width: 0.89rem; height: 0.89rem; font-size: 0.75rem; left: 12.09rem; top: 0.79rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    sig</span>
                <span class="position style"
                    style="width: 1.44rem; height: 0.89rem; font-size: 0.75rem; left: 12.97rem; top: 0.79rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">ning</span>
                <span class="position style"
                    style="width: 1.40rem; height: 0.89rem; font-size: 0.75rem; left: 14.58rem; top: 0.79rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    belo</span>
                <span class="position style"
                    style="width: 0.57rem; height: 0.89rem; font-size: 0.75rem; left: 15.98rem; top: 0.79rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">w</span>
                <span class="position style"
                    style="width: 0.17rem; height: 0.89rem; font-size: 0.75rem; left: 16.72rem; top: 0.79rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    I</span>
                <span class="position style"
                    style="width: 0.93rem; height: 0.89rem; font-size: 0.75rem; left: 17.06rem; top: 0.79rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    tak</span>
                <span class="position style"
                    style="width: 0.38rem; height: 0.89rem; font-size: 0.75rem; left: 18.00rem; top: 0.79rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">e</span>
                <span class="position style"
                    style="width: 0.96rem; height: 0.89rem; font-size: 0.75rem; left: 18.56rem; top: 0.79rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    full</span>
                <span class="position style"
                    style="width: 0.23rem; height: 0.89rem; font-size: 0.75rem; left: 19.69rem; top: 0.79rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    r</span>
                <span class="position style"
                    style="width: 3.59rem; height: 0.89rem; font-size: 0.75rem; left: 19.91rem; top: 0.79rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">esponsibilit</span>
                <span class="position style"
                    style="width: 0.35rem; height: 0.89rem; font-size: 0.75rem; left: 23.51rem; top: 0.79rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">y</span>
                <span class="position style"
                    style="width: 0.21rem; height: 0.89rem; font-size: 0.75rem; left: 24.03rem; top: 0.79rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    f</span>
                <span class="position style"
                    style="width: 0.65rem; height: 0.89rem; font-size: 0.75rem; left: 24.22rem; top: 0.79rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">or</span>
                <span class="position style"
                    style="width: 1.05rem; height: 0.89rem; font-size: 0.75rem; left: 25.05rem; top: 0.79rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    the</span>
                <span class="position style"
                    style="width: 4.55rem; height: 0.89rem; font-size: 0.75rem; left: 26.27rem; top: 0.79rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    consequences</span>
                <span class="position style"
                    style="width: 1.27rem; height: 0.89rem; font-size: 0.75rem; left: 30.99rem; top: 0.79rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    that</span>
                <span class="position style"
                    style="width: 1.00rem; height: 0.89rem; font-size: 0.75rem; left: 32.43rem; top: 0.79rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    ma</span>
                <span class="position style"
                    style="width: 0.35rem; height: 0.89rem; font-size: 0.75rem; left: 33.43rem; top: 0.79rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">y</span>
                <span class="position style"
                    style="width: 0.59rem; height: 0.89rem; font-size: 0.75rem; left: 33.95rem; top: 0.79rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    ar</span>
                <span class="position style"
                    style="width: 0.84rem; height: 0.89rem; font-size: 0.75rem; left: 34.54rem; top: 0.79rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">ise</span>
                <span class="position style"
                    style="width: 0.44rem; height: 0.89rem; font-size: 0.75rem; left: 35.56rem; top: 0.79rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    fr</span>
                <span class="position style"
                    style="width: 1.06rem; height: 0.89rem; font-size: 0.75rem; left: 35.99rem; top: 0.79rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">om</span>
                <span class="position style"
                    style="width: 2.65rem; height: 0.89rem; font-size: 0.75rem; left: 37.22rem; top: 0.79rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    handing</span>
                <span class="position style"
                    style="width: 0.42rem; height: 0.89rem; font-size: 0.75rem; left: 40.04rem; top: 0.79rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    o</span>
                <span class="position style"
                    style="width: 0.36rem; height: 0.89rem; font-size: 0.75rem; left: 40.46rem; top: 0.79rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">v</span>
                <span class="position style"
                    style="width: 0.61rem; height: 0.89rem; font-size: 0.75rem; left: 40.81rem; top: 0.79rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">er</span>
                <span class="position style"
                    style="width: 0.65rem; height: 0.89rem; font-size: 0.75rem; left: 41.60rem; top: 0.79rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    m</span>
                <span class="position style"
                    style="width: 0.35rem; height: 0.89rem; font-size: 0.75rem; left: 42.24rem; top: 0.79rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">y</span>
                <span class="position style"
                    style="width: 0.51rem; height: 0.89rem; font-size: 0.75rem; left: 1.16rem; top: 1.68rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    D</span>
                <span class="position style"
                    style="width: 1.23rem; height: 0.89rem; font-size: 0.75rem; left: 1.68rem; top: 1.68rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">ebit</span>
                <span class="position style"
                    style="width: 0.45rem; height: 0.89rem; font-size: 0.75rem; left: 3.08rem; top: 1.68rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    C</span>
                <span class="position style"
                    style="width: 0.59rem; height: 0.89rem; font-size: 0.75rem; left: 3.52rem; top: 1.68rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">ar</span>
                <span class="position style"
                    style="width: 0.43rem; height: 0.89rem; font-size: 0.75rem; left: 4.10rem; top: 1.68rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">d</span>
                <span class="position style"
                    style="width: 0.24rem; height: 0.89rem; font-size: 0.75rem; left: 4.71rem; top: 1.68rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    t</span>
                <span class="position style"
                    style="width: 0.42rem; height: 0.89rem; font-size: 0.75rem; left: 4.95rem; top: 1.68rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">o</span>
                <span class="position style"
                    style="width: 0.36rem; height: 0.89rem; font-size: 0.75rem; left: 5.54rem; top: 1.68rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    a</span>
                <span class="position style"
                    style="width: 1.06rem; height: 0.89rem; font-size: 0.75rem; left: 6.07rem; top: 1.68rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    thir</span>
                <span class="position style"
                    style="width: 0.43rem; height: 0.89rem; font-size: 0.75rem; left: 7.12rem; top: 1.68rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">d</span>
                <span class="position style"
                    style="width: 1.03rem; height: 0.89rem; font-size: 0.75rem; left: 7.72rem; top: 1.68rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    par</span>
                <span class="position style"
                    style="width: 0.24rem; height: 0.89rem; font-size: 0.75rem; left: 8.77rem; top: 1.68rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">t</span>
                <span class="position style"
                    style="width: 0.35rem; height: 0.89rem; font-size: 0.75rem; left: 9.02rem; top: 1.68rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">y</span>
                <span class="position style"
                    style="width: 0.65rem; height: 0.89rem; font-size: 0.75rem; left: 9.54rem; top: 1.68rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    as</span>
                <span class="position style"
                    style="width: 2.68rem; height: 0.89rem; font-size: 0.75rem; left: 10.36rem; top: 1.68rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    nominat</span>
                <span class="position style"
                    style="width: 0.81rem; height: 0.89rem; font-size: 0.75rem; left: 13.04rem; top: 1.68rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">ed</span>
                <span class="position style"
                    style="width: 1.21rem; height: 0.89rem; font-size: 0.75rem; left: 14.02rem; top: 1.68rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    and</span>
                <span class="position style"
                    style="width: 2.09rem; height: 0.89rem; font-size: 0.75rem; left: 15.40rem; top: 1.68rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    author</span>
                <span class="position style"
                    style="width: 0.48rem; height: 0.89rem; font-size: 0.75rem; left: 17.50rem; top: 1.68rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">iz</span>
                <span class="position style"
                    style="width: 0.81rem; height: 0.89rem; font-size: 0.75rem; left: 17.98rem; top: 1.68rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">ed</span>
                <span class="position style"
                    style="width: 0.59rem; height: 0.89rem; font-size: 0.75rem; left: 18.96rem; top: 1.68rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    in</span>
                <span class="position style"
                    style="width: 1.12rem; height: 0.89rem; font-size: 0.75rem; left: 19.72rem; top: 1.68rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    this</span>
                <span class="position style"
                    style="width: 2.09rem; height: 0.89rem; font-size: 0.75rem; left: 21.02rem; top: 1.68rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    author</span>
                <span class="position style"
                    style="width: 0.41rem; height: 0.89rem; font-size: 0.75rem; left: 23.11rem; top: 1.68rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">it</span>
                <span class="position style"
                    style="width: 0.35rem; height: 0.89rem; font-size: 0.75rem; left: 23.53rem; top: 1.68rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">y</span>
                <span class="position style"
                    style="width: 1.21rem; height: 0.89rem; font-size: 0.75rem; left: 24.05rem; top: 1.68rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    abo</span>
                <span class="position style"
                    style="width: 0.36rem; height: 0.89rem; font-size: 0.75rem; left: 25.26rem; top: 1.68rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">v</span>
                <span class="position style"
                    style="width: 0.38rem; height: 0.89rem; font-size: 0.75rem; left: 25.61rem; top: 1.68rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">e</span>
                <span class="position style"
                    style="width: 1.21rem; height: 0.89rem; font-size: 0.75rem; left: 26.17rem; top: 1.68rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    and</span>
                <span class="position style"
                    style="width: 0.17rem; height: 0.89rem; font-size: 0.75rem; left: 27.55rem; top: 1.68rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    I</span>
                <span class="position style"
                    style="width: 1.04rem; height: 0.89rem; font-size: 0.75rem; left: 27.89rem; top: 1.68rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    her</span>
                <span class="position style"
                    style="width: 0.82rem; height: 0.89rem; font-size: 0.75rem; left: 28.91rem; top: 1.68rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">eb</span>
                <span class="position style"
                    style="width: 0.35rem; height: 0.89rem; font-size: 0.75rem; left: 29.73rem; top: 1.68rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">y</span>
                <span class="position style"
                    style="width: 3.18rem; height: 0.89rem; font-size: 0.75rem; left: 30.25rem; top: 1.68rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    indemnify</span>
                <span class="position style"
                    style="width: 1.05rem; height: 0.89rem; font-size: 0.75rem; left: 33.60rem; top: 1.68rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    the</span>
                <span class="position style"
                    style="width: 1.51rem; height: 0.89rem; font-size: 0.75rem; left: 34.82rem; top: 1.68rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    Bank</span>
                <span class="position style"
                    style="width: 2.27rem; height: 0.89rem; font-size: 0.75rem; left: 36.51rem; top: 1.68rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    against</span>
                <span class="position style"
                    style="width: 0.78rem; height: 0.89rem; font-size: 0.75rem; left: 38.95rem; top: 1.68rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    an</span>
                <span class="position style"
                    style="width: 0.35rem; height: 0.89rem; font-size: 0.75rem; left: 39.72rem; top: 1.68rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">y</span>
                <span class="position style"
                    style="width: 1.86rem; height: 0.89rem; font-size: 0.75rem; left: 40.24rem; top: 1.68rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    liabilit</span>
                <span class="position style"
                    style="width: 0.35rem; height: 0.89rem; font-size: 0.75rem; left: 42.11rem; top: 1.68rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">y</span>
                <span class="position style"
                    style="width: 0.65rem; height: 0.89rem; font-size: 0.75rem; left: 42.63rem; top: 1.68rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    or</span>
                <span class="position style"
                    style="width: 1.85rem; height: 0.89rem; font-size: 0.75rem; left: 1.16rem; top: 2.56rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    losses</span>
                <span class="position style"
                    style="width: 1.93rem; height: 0.89rem; font-size: 0.75rem; left: 3.19rem; top: 2.56rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    which</span>
                <span class="position style"
                    style="width: 1.00rem; height: 0.89rem; font-size: 0.75rem; left: 5.29rem; top: 2.56rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    ma</span>
                <span class="position style"
                    style="width: 0.35rem; height: 0.89rem; font-size: 0.75rem; left: 6.29rem; top: 2.56rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">y</span>
                <span class="position style"
                    style="width: 0.59rem; height: 0.89rem; font-size: 0.75rem; left: 6.81rem; top: 2.56rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    ar</span>
                <span class="position style"
                    style="width: 0.84rem; height: 0.89rem; font-size: 0.75rem; left: 7.40rem; top: 2.56rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">ise</span>
                <span class="position style"
                    style="width: 0.65rem; height: 0.89rem; font-size: 0.75rem; left: 8.41rem; top: 2.56rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    as</span>
                <span class="position style"
                    style="width: 0.36rem; height: 0.89rem; font-size: 0.75rem; left: 9.24rem; top: 2.56rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    a</span>
                <span class="position style"
                    style="width: 0.23rem; height: 0.89rem; font-size: 0.75rem; left: 9.77rem; top: 2.56rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    r</span>
                <span class="position style"
                    style="width: 1.51rem; height: 0.89rem; font-size: 0.75rem; left: 9.99rem; top: 2.56rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">esult</span>
                <span class="position style"
                    style="width: 0.63rem; height: 0.89rem; font-size: 0.75rem; left: 11.67rem; top: 2.56rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    of</span>
                <span class="position style"
                    style="width: 0.71rem; height: 0.89rem; font-size: 0.75rem; left: 12.47rem; top: 2.56rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    ac</span>
                <span class="position style"
                    style="width: 1.26rem; height: 0.89rem; font-size: 0.75rem; left: 13.19rem; top: 2.56rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">ting</span>
                <span class="position style"
                    style="width: 0.84rem; height: 0.89rem; font-size: 0.75rem; left: 14.62rem; top: 2.56rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    on</span>
                <span class="position style"
                    style="width: 0.65rem; height: 0.89rem; font-size: 0.75rem; left: 15.63rem; top: 2.56rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    m</span>
                <span class="position style"
                    style="width: 0.35rem; height: 0.89rem; font-size: 0.75rem; left: 16.27rem; top: 2.56rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">y</span>
                <span class="position style"
                    style="width: 2.13rem; height: 0.89rem; font-size: 0.75rem; left: 16.79rem; top: 2.56rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    instruc</span>
                <span class="position style"
                    style="width: 1.54rem; height: 0.89rem; font-size: 0.75rem; left: 18.93rem; top: 2.56rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">tions</span>
                <span class="position style"
                    style="width: 0.43rem; height: 0.89rem; font-size: 0.75rem; left: 20.64rem; top: 2.56rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    b</span>
                <span class="position style"
                    style="width: 0.35rem; height: 0.89rem; font-size: 0.75rem; left: 21.07rem; top: 2.56rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">y</span>
                <span class="position style"
                    style="width: 1.05rem; height: 0.89rem; font-size: 0.75rem; left: 21.59rem; top: 2.56rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    the</span>
                <span class="position style"
                    style="width: 1.51rem; height: 0.89rem; font-size: 0.75rem; left: 22.81rem; top: 2.56rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">
                    Bank</span>
                <span class="position style"
                    style="width: 0.14rem; height: 0.89rem; font-size: 0.75rem; left: 24.34rem; top: 2.56rem; font-family: 'pro', serif; color: #58595b; transform: ScaleX(1.05);">.</span>
            </p>
            <p class="paragraph body-text"
                style="width: 53.25rem; height: 1.17rem; left: 3.55rem; top: 65.75rem; text-align: left; color: #231f20; font-weight: 300;">
                <span class="position style"
                    style="width: 4.13rem; height: 1.04rem; left: 0.00rem; top: 0.14rem;">Signature(s)</span>
                <span class="position style" style="width: 0.68rem; height: 1.04rem; left: 4.31rem; top: 0.14rem;">
                    of</span>
                <span class="position style" style="width: 2.80rem; height: 1.04rem; left: 5.15rem; top: 0.14rem;">
                    account</span>
                <span class="position style" style="width: 2.25rem; height: 1.04rem; left: 8.13rem; top: 0.14rem;">
                    holder</span>
                <span class="position" style="width: 15.68rem; height: 1.04rem; left: 11.37rem; top: 0.14rem;">
                    <span class="style"> </span>
                    <span class="style" style="text-decoration: underline;"> </span>
                </span>
                <span class="position style" style="width: 1.61rem; height: 1.04rem; left: 34.26rem; top: 0.05rem;">
                    Date</span>
                <span class="position" style="width: 15.68rem; height: 1.04rem; left: 36.41rem; top: 0.05rem;">
                    <span class="style"> </span>
                    <span class="style" style="text-decoration: underline;"> </span>
                </span>
            </p>
            <p class="paragraph body-text"
                style="width: 53.25rem; height: 1.17rem; left: 3.55rem; top: 69.50rem; text-align: left; color: #231f20; font-weight: 300;">
                <span class="position style"
                    style="width: 4.13rem; height: 1.04rem; left: 0.00rem; top: 0.13rem;">Signature(s)</span>
                <span class="position style" style="width: 0.68rem; height: 1.04rem; left: 4.31rem; top: 0.13rem;">
                    of</span>
                <span class="position style" style="width: 2.80rem; height: 1.04rem; left: 5.15rem; top: 0.13rem;">
                    account</span>
                <span class="position style" style="width: 2.25rem; height: 1.04rem; left: 8.13rem; top: 0.13rem;">
                    holder</span>
                <span class="position" style="width: 15.68rem; height: 1.04rem; left: 11.37rem; top: 0.13rem;">
                    <span class="style"> </span>
                    <span class="style" style="text-decoration: underline;"> </span>
                </span>
                <span class="position style" style="width: 1.61rem; height: 1.04rem; left: 34.26rem; top: 0.04rem;">
                    Date</span>
                <span class="position" style="width: 15.68rem; height: 1.04rem; left: 36.41rem; top: 0.04rem;">
                    <span class="style"> </span>
                    <span class="style" style="text-decoration: underline;"> </span>
                </span>
            </p>
            <p class="paragraph body-text"
                style="width: 53.41rem; height: 0.97rem; font-size: 0.70rem; left: 3.39rem; top: 72.28rem; text-align: left; font-family: 'pro', serif; color: #231f20; font-weight: 600;">
                <span class="position style"
                    style="width: 0.39rem; height: 0.85rem; left: 0.00rem; top: 0.12rem;">P</span>
                <span class="position style"
                    style="width: 1.54rem; height: 0.85rem; left: 0.38rem; top: 0.12rem;">lease</span>
                <span class="position style" style="width: 1.25rem; height: 0.85rem; left: 2.07rem; top: 0.12rem;">
                    visit</span>
                <span class="position" style="width: 1.05rem; height: 0.85rem; left: 3.46rem; top: 0.12rem;">
                    <span class="style"> </span>
                    <a href="http://www.mib.com.mv/" class="link">
                        <span class="style">ww</span>
                    </a>
                </span>
                <a href="http://www.mib.com.mv/" class="link">
                </a>
                <a href="http://www.mib.com.mv/" class="link">
                    <span class="position style"
                        style="width: 0.52rem; height: 0.85rem; left: 4.52rem; top: 0.12rem;">w</span>
                </a>
                <a href="http://www.mib.com.mv/" class="link">
                    <span class="position style"
                        style="width: 1.34rem; height: 0.85rem; left: 5.01rem; top: 0.12rem;">.mib</span>
                </a>
                <a href="http://www.mib.com.mv/" class="link">
                    <span class="position style"
                        style="width: 2.22rem; height: 0.85rem; left: 6.34rem; top: 0.12rem;">.com.m</span>
                </a>
                <a href="http://www.mib.com.mv/" class="link">
                    <span class="position style"
                        style="width: 0.35rem; height: 0.85rem; left: 8.55rem; top: 0.12rem;">v</span>
                </a>
                <span class="position style" style="width: 0.22rem; height: 0.85rem; left: 9.05rem; top: 0.12rem;">
                    f</span>
                <span class="position style"
                    style="width: 0.64rem; height: 0.85rem; left: 9.26rem; top: 0.12rem;">or</span>
                <span class="position style" style="width: 0.37rem; height: 0.85rem; left: 10.02rem; top: 0.12rem;">
                    T</span>
                <span class="position style"
                    style="width: 0.61rem; height: 0.85rem; left: 10.34rem; top: 0.12rem;">er</span>
                <span class="position style"
                    style="width: 0.88rem; height: 0.85rem; left: 10.95rem; top: 0.12rem;">ms</span>
                <span class="position style" style="width: 1.16rem; height: 0.85rem; left: 11.98rem; top: 0.12rem;">
                    and</span>
                <span class="position style" style="width: 0.41rem; height: 0.85rem; left: 13.28rem; top: 0.12rem;">
                    C</span>
                <span class="position style"
                    style="width: 2.88rem; height: 0.85rem; left: 13.68rem; top: 0.12rem;">onditions</span>
            </p>
            <table class="table"
                style="width: 52.82rem; height: 9.93rem; table-layout: fixed; z-index: 0; position: absolute; left: 3.39rem; top: 73.37rem; border-collapse: collapse;">
                <tbody>
                    <tr>
                        <td style="border: none; height: 0.0rem; width: 26.655rem;" />
                        <td style="border: none; height: 0.0rem; width: 8.945rem;" />
                        <td style="border: none; height: 0.0rem; width: 8.150rem;" />
                        <td style="border: none; height: 0.0rem; width: 9.070rem;" />
                    </tr>
                    <tr>
                        <td rowspan="1" colspan="4" class="cell"
                            style="border-bottom: 0.05rem solid#939598; background: #006b4e; width: 52.820rem; height: 2.080rem; vertical-align: top;">
                            <p class="paragraph table-paragraph"
                                style="width: 52.35rem; height: 1.72rem; font-size: 1.00rem; left: 0.47rem; top: 0.03rem; text-align: left; font-family: 'pro', serif; color: #ffffff; font-weight: 600;">
                                <span class="position style"
                                    style="width: 1.78rem; height: 1.29rem; left: 0.00rem; top: 0.46rem;">FOR</span>
                                <span class="position style"
                                    style="width: 2.41rem; height: 1.29rem; left: 1.98rem; top: 0.46rem;"> BANK</span>
                                <span class="position style"
                                    style="width: 1.74rem; height: 1.29rem; left: 4.59rem; top: 0.46rem;"> USE</span>
                                <span class="position style"
                                    style="width: 2.33rem; height: 1.29rem; left: 6.53rem; top: 0.46rem;"> ONLY</span>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td rowspan="1" colspan="1" class="cell"
                            style="border-bottom: 0.05rem solid#939598; background: #f1f2f2; width: 26.655rem; height: 1.940rem; border-top: 0.05rem solid#939598; border-left: 0.05rem solid#939598; vertical-align: top; border-right: 0.05rem solid#939598;">
                            <p class="paragraph table-paragraph"
                                style="width: 26.16rem; height: 1.46rem; font-size: 0.85rem; left: 0.49rem; top: 2.16rem; text-align: left; color: #58595b; font-weight: 400;">
                                <span class="position style"
                                    style="width: 1.87rem; height: 1.02rem; left: -0.02rem; top: 0.44rem;">Form</span>
                                <span class="position style"
                                    style="width: 0.51rem; height: 1.02rem; left: 2.03rem; top: 0.44rem;"> &amp;</span>
                                <span class="position style"
                                    style="width: 4.03rem; height: 1.02rem; left: 2.72rem; top: 0.44rem;">
                                    Supporting</span>
                                <span class="position style"
                                    style="width: 4.11rem; height: 1.02rem; left: 6.93rem; top: 0.44rem;">
                                    Documents</span>
                            </p>
                        </td>
                        <td rowspan="1" colspan="1" class="cell"
                            style="border-bottom: 0.05rem solid#939598; background: #f1f2f2; width: 8.945rem; height: 1.940rem; border-top: 0.05rem solid#939598; border-left: 0.05rem solid#939598; vertical-align: top; border-right: 0.05rem solid#939598;">
                            <div class="group"
                                style="width: 2.49rem; height: 0.62rem; display: block; left: 29.90rem; top: 2.80rem;">
                                <svg viewbox="0.000000, 0.000000, 24.900000, 6.250000" class="graphic"
                                    style="width: 2.49rem; height: 0.62rem; display: block; z-index: 10; left: 0.00rem; top: 0.00rem;">
                                    <path fill="#58595b" fill-opacity="1.000000"
                                        d="M 21.751 0.357 L 21.164 0.357 L 20.629 0.408 L 20.179 0.485 L 20.179 6.111 L 20.553 6.153 L 20.986 6.179 L 21.521 6.179 L 22.3117 6.12548 L 23.0058 5.96763 L 23.593 5.70945 L 23.7593 5.584 L 21.1723 5.584 L 20.918 5.55 L 20.918 1.02 L 21.113 0.977 L 21.402 0.943 L 23.893 0.943 L 23.6465 0.765797 L 23.1214 0.541875 L 22.5093 0.408 L 22.5538 0.408 L 21.751 0.357 Z"
                                        stroke="none" />
                                    <path fill="#58595b" fill-opacity="1.000000"
                                        d="M 23.893 0.943 L 21.785 0.943 L 22.8086 1.09833 L 23.5349 1.54138 L 23.9662 2.23773 L 24.1012 3.128 L 24.105 3.153 L 23.9474 4.18548 L 23.4814 4.94863 L 22.7175 5.4217 L 21.666 5.584 L 23.7593 5.584 L 24.063 5.355 L 24.4141 4.91619 L 24.6726 4.3945 L 24.8324 3.79631 L 24.885 3.153 L 24.887 3.128 L 24.8325 2.48119 L 24.6736 1.927 L 24.4175 1.45906 L 24.071 1.071 L 23.893 0.943 Z"
                                        stroke="none" />
                                    <path fill="#58595b" fill-opacity="1.000000"
                                        d="M 18.887 0.4 L 18.139 0.4 L 18.139 6.128 L 18.887 6.128 L 18.887 0.4 Z"
                                        stroke="none" />
                                    <path fill="#58595b" fill-opacity="1.000000"
                                        d="M 12.18 2.584 L 11.441 2.584 L 11.441 6.128 L 12.18 6.128 L 12.18 2.584 Z"
                                        stroke="none" />
                                    <path fill="#58595b" fill-opacity="1.000000"
                                        d="M 14.653 2.584 L 13.914 2.584 L 13.914 6.128 L 14.653 6.128 L 14.653 2.584 Z"
                                        stroke="none" />
                                    <path fill="#58595b" fill-opacity="1.000000"
                                        d="M 15.639 2.014 L 10.871 2.014 L 10.871 2.584 L 15.639 2.584 L 15.639 2.014 Z"
                                        stroke="none" />
                                    <path fill="#58595b" fill-opacity="1.000000"
                                        d="M 13.2228 0.119 L 12.4381 0.119 L 12.138 0.247 L 11.9 0.485 L 11.577 0.816 L 11.441 1.309 L 11.441 2.014 L 12.18 2.014 L 12.18 1.198 L 12.3365 0.714 L 12.342 0.697 L 13.4001 0.697 L 13.523 0.247 L 13.395 0.179 L 13.2228 0.119 Z"
                                        stroke="none" />
                                    <path fill="#58595b" fill-opacity="1.000000"
                                        d="M 15.656 1.15463e-14 L 15.044 1.15463e-14 L 14.643 0.119 L 14.6618 0.119 L 14.39 0.383 L 14.041 0.714 L 13.914 1.241 L 13.914 2.014 L 14.653 2.014 L 14.653 1.156 L 14.815 0.603 L 15.9433 0.603 L 16.0122 0.179 L 16.022 0.119 L 15.886 0.06 L 15.656 1.15463e-14 Z"
                                        stroke="none" />
                                    <path fill="#58595b" fill-opacity="1.000000"
                                        d="M 13.4001 0.697 L 13.098 0.697 L 13.259 0.748 L 13.3869 0.816 L 13.3675 0.816 L 13.3861 0.748 L 13.3954 0.714 L 13.4001 0.697 Z"
                                        stroke="none" />
                                    <path fill="#58595b" fill-opacity="1.000000"
                                        d="M 15.9433 0.603 L 15.665 0.603 L 15.809 0.646 L 15.928 0.697 L 15.9363 0.646 L 15.9433 0.603 Z"
                                        stroke="none" />
                                    <path fill="#58595b" fill-opacity="1.000000"
                                        d="M 9.85646 2.482 L 9.32535 2.482 L 9.417 3.094 L 9.417 3.51 L 8.36023 3.60591 L 7.58462 3.89925 L 7.10695 4.38047 L 6.944 5.04 L 6.944 5.635 L 7.369 6.221 L 8.789 6.221 L 9.23 5.932 L 9.41728 5.669 L 8.015 5.669 L 7.692 5.448 L 7.692 4.156 L 8.619 4.012 L 10.157 4.012 L 10.157 3.60591 L 10.0895 2.99803 L 9.85646 2.482 Z"
                                        stroke="none" />
                                    <path fill="#58595b" fill-opacity="1.000000"
                                        d="M 10.1624 5.609 L 9.485 5.609 L 9.545 6.128 L 10.216 6.128 L 10.174 5.847 L 10.1653 5.669 L 10.1624 5.609 Z"
                                        stroke="none" />
                                    <path fill="#58595b" fill-opacity="1.000000"
                                        d="M 10.157 4.012 L 8.619 4.012 L 9.434 4.029 L 9.426 4.896 L 9.4 4.981 L 9.281 5.329 L 8.933 5.669 L 9.41728 5.669 L 9.46 5.609 L 10.1624 5.609 L 10.157 4.012 Z"
                                        stroke="none" />
                                    <path fill="#58595b" fill-opacity="1.000000"
                                        d="M 8.593 1.929 L 8.075 1.929 L 7.56984 2.07597 L 7.233 2.286 L 7.403 2.788 L 7.692 2.592 L 8.092 2.482 L 9.85646 2.482 L 9.84675 2.4605 L 9.36809 2.07597 L 8.593 1.929 Z"
                                        stroke="none" />
                                    <path fill="#58595b" fill-opacity="1.000000"
                                        d="M 5.355 2.584 L 4.624 2.584 L 4.624 5.312 L 4.709 5.677 L 4.913 5.907 L 5.091 6.102 L 5.372 6.221 L 6.009 6.221 L 6.239 6.17 L 6.383 6.119 L 6.35659 5.677 L 6.35205 5.601 L 5.499 5.601 L 5.355 5.312 L 5.355 2.584 Z"
                                        stroke="none" />
                                    <path fill="#58595b" fill-opacity="1.000000"
                                        d="M 6.349 5.55 L 6.239 5.584 L 6.12 5.601 L 6.35205 5.601 L 6.349 5.55 Z"
                                        stroke="none" />
                                    <path fill="#58595b" fill-opacity="1.000000"
                                        d="M 6.426 2.014 L 3.986 2.014 L 3.986 2.584 L 6.426 2.584 L 6.426 2.014 Z"
                                        stroke="none" />
                                    <path fill="#58595b" fill-opacity="1.000000"
                                        d="M 5.355 1.028 L 4.624 1.249 L 4.624 2.014 L 5.355 2.014 L 5.355 1.028 Z"
                                        stroke="none" />
                                    <path fill="#58595b" fill-opacity="1.000000"
                                        d="M 0.195 5.227 L 7.10543e-15 5.847 L 0.306 6.051 L 0.909 6.221 L 1.462 6.221 L 2.34398 6.08641 L 2.96712 5.724 L 3.05325 5.601 L 1.003 5.601 L 0.527 5.44 L 0.195 5.227 Z"
                                        stroke="none" />
                                    <path fill="#58595b" fill-opacity="1.000000"
                                        d="M 2.55 0.306 L 1.972 0.306 L 1.2125 0.423531 L 0.62775 0.749 L 0.251875 1.24172 L 0.119 1.861 L 0.223922 2.40527 L 0.524875 2.84912 L 1.00114 3.20692 L 1.632 3.493 L 2.405 3.782 L 2.711 4.097 L 2.711 5.202 L 2.269 5.601 L 3.05325 5.601 L 3.33264 5.202 L 3.459 4.564 L 3.36809 3.99883 L 3.0955 3.54737 L 2.64141 3.18348 L 2.006 2.881 L 1.215 2.575 L 0.867 2.312 L 0.867 1.385 L 1.164 0.918 L 3.11687 0.918 L 3.229 0.586 L 2.983 0.442 L 2.55 0.306 Z"
                                        stroke="none" />
                                    <path fill="#58595b" fill-opacity="1.000000"
                                        d="M 3.11687 0.918 L 2.465 0.918 L 2.847 1.079 L 3.025 1.19 L 3.11687 0.918 Z"
                                        stroke="none" />
                                </svg>
                            </div>
                        </td>
                        <td rowspan="1" colspan="1" class="cell"
                            style="border-bottom: 0.05rem solid#939598; background: #f1f2f2; width: 8.150rem; height: 1.940rem; border-top: 0.05rem solid#939598; border-left: 0.05rem solid#939598; vertical-align: top; border-right: 0.05rem solid#939598;">
                            <p class="paragraph table-paragraph"
                                style="width: 8.08rem; height: 1.46rem; font-size: 0.85rem; left: 35.66rem; top: 2.16rem; text-align: center; color: #58595b; font-weight: 400;">
                                <span class="position style"
                                    style="width: 2.30rem; height: 1.02rem; left: 2.87rem; top: 0.44rem;">Initials</span>
                            </p>
                        </td>
                        <td rowspan="1" colspan="1" class="cell"
                            style="border-bottom: 0.05rem solid#939598; background: #f1f2f2; width: 9.070rem; height: 1.940rem; border-top: 0.05rem solid#939598; border-left: 0.05rem solid#939598; vertical-align: top; border-right: 0.05rem solid#939598;">
                            <p class="paragraph table-paragraph"
                                style="width: 9.01rem; height: 1.47rem; font-size: 0.85rem; left: 43.81rem; top: 2.16rem; text-align: center; color: #58595b; font-weight: 400;">
                                <span class="position style"
                                    style="width: 1.68rem; height: 1.02rem; left: 3.64rem; top: 0.44rem;">Date</span>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td rowspan="1" colspan="1" class="cell"
                            style="border-bottom: 0.05rem solid#939598; background: #f1f2f2; width: 26.655rem; height: 1.940rem; border-top: 0.05rem solid#939598; border-left: 0.05rem solid#939598; vertical-align: top; border-right: 0.05rem solid#939598;">
                            <p class="paragraph table-paragraph"
                                style="width: 26.16rem; height: 1.43rem; font-size: 0.85rem; left: 0.49rem; top: 4.10rem; text-align: left; color: #58595b; font-weight: 400;">
                                <span class="position style"
                                    style="width: 3.20rem; height: 1.02rem; left: -0.02rem; top: 0.41rem;">Received</span>
                                <span class="position style"
                                    style="width: 0.86rem; height: 1.02rem; left: 3.36rem; top: 0.41rem;"> By</span>
                            </p>
                        </td>
                        <td rowspan="1" colspan="1" class="cell"
                            style="border-bottom: 0.05rem solid#939598; background: #f1f2f2; width: 8.945rem; height: 1.940rem; border-top: 0.05rem solid#939598; border-left: 0.05rem solid#939598; vertical-align: top; border-right: 0.05rem solid#939598;">
                        </td>
                        <td rowspan="1" colspan="1" class="cell"
                            style="border-bottom: 0.05rem solid#939598; background: #f1f2f2; width: 8.150rem; height: 1.940rem; border-top: 0.05rem solid#939598; border-left: 0.05rem solid#939598; vertical-align: top; border-right: 0.05rem solid#939598;">
                        </td>
                        <td rowspan="1" colspan="1" class="cell"
                            style="border-bottom: 0.05rem solid#939598; background: #f1f2f2; width: 9.070rem; height: 1.940rem; border-top: 0.05rem solid#939598; border-left: 0.05rem solid#939598; vertical-align: top; border-right: 0.05rem solid#939598;">
                        </td>
                    </tr>
                    <tr>
                        <td rowspan="1" colspan="1" class="cell"
                            style="border-bottom: 0.05rem solid#939598; background: #f1f2f2; width: 26.655rem; height: 1.945rem; border-top: 0.05rem solid#939598; border-left: 0.05rem solid#939598; vertical-align: top; border-right: 0.05rem solid#939598;">
                            <p class="paragraph table-paragraph"
                                style="width: 26.16rem; height: 1.43rem; font-size: 0.85rem; left: 0.49rem; top: 6.04rem; text-align: left; color: #58595b; font-weight: 400;">
                                <span class="position style"
                                    style="width: 3.08rem; height: 1.02rem; left: -0.02rem; top: 0.41rem;">Checked</span>
                                <span class="position style"
                                    style="width: 0.86rem; height: 1.02rem; left: 3.23rem; top: 0.41rem;"> By</span>
                            </p>
                        </td>
                        <td rowspan="1" colspan="1" class="cell"
                            style="border-bottom: 0.05rem solid#939598; background: #f1f2f2; width: 8.945rem; height: 1.945rem; border-top: 0.05rem solid#939598; border-left: 0.05rem solid#939598; vertical-align: top; border-right: 0.05rem solid#939598;">
                        </td>
                        <td rowspan="1" colspan="1" class="cell"
                            style="border-bottom: 0.05rem solid#939598; background: #f1f2f2; width: 8.150rem; height: 1.945rem; border-top: 0.05rem solid#939598; border-left: 0.05rem solid#939598; vertical-align: top; border-right: 0.05rem solid#939598;">
                        </td>
                        <td rowspan="1" colspan="1" class="cell"
                            style="border-bottom: 0.05rem solid#939598; background: #f1f2f2; width: 9.070rem; height: 1.945rem; border-top: 0.05rem solid#939598; border-left: 0.05rem solid#939598; vertical-align: top; border-right: 0.05rem solid#939598;">
                        </td>
                    </tr>
                    <tr>
                        <td rowspan="1" colspan="1" class="cell"
                            style="border-bottom: 0.05rem solid#939598; background: #f1f2f2; width: 26.655rem; height: 2.005rem; border-top: 0.05rem solid#939598; border-left: 0.05rem solid#939598; vertical-align: top; border-right: 0.05rem solid#939598;">
                            <p class="paragraph table-paragraph"
                                style="width: 26.16rem; height: 1.48rem; font-size: 0.85rem; left: 0.49rem; top: 7.99rem; text-align: left; color: #58595b; font-weight: 400;">
                                <span class="position style"
                                    style="width: 3.95rem; height: 1.02rem; left: -0.02rem; top: 0.46rem;">Authorized</span>
                                <span class="position style"
                                    style="width: 0.86rem; height: 1.02rem; left: 4.11rem; top: 0.46rem;"> By</span>
                            </p>
                        </td>
                        <td rowspan="1" colspan="1" class="cell"
                            style="border-bottom: 0.05rem solid#939598; background: #f1f2f2; width: 8.945rem; height: 2.005rem; border-top: 0.05rem solid#939598; border-left: 0.05rem solid#939598; vertical-align: top; border-right: 0.05rem solid#939598;">
                        </td>
                        <td rowspan="1" colspan="1" class="cell"
                            style="border-bottom: 0.05rem solid#939598; background: #f1f2f2; width: 8.150rem; height: 2.005rem; border-top: 0.05rem solid#939598; border-left: 0.05rem solid#939598; vertical-align: top; border-right: 0.05rem solid#939598;">
                        </td>
                        <td rowspan="1" colspan="1" class="cell"
                            style="border-bottom: 0.05rem solid#939598; background: #f1f2f2; width: 9.070rem; height: 2.005rem; border-top: 0.05rem solid#939598; border-left: 0.05rem solid#939598; vertical-align: top; border-right: 0.05rem solid#939598;">
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="page" style="background: #ffffff; width: 59.55rem; height: 84.20rem; z-index: 0;">
            <svg viewbox="0.000000, -0.125000, 1.000000, 1.000000" class="graphic"
                style="width: 0.10rem; height: 0.10rem; display: block; z-index: 10; left: 7.74rem; top: 3.91rem;">
                <path stroke-width="0.250000" fill="none" d="M 0 0 L 1 0" stroke="#58595b" stroke-opacity="1.000000" />
            </svg>
            <svg viewbox="0.000000, -0.125000, 1.000000, 1.000000" class="graphic"
                style="width: 0.10rem; height: 0.10rem; display: block; z-index: 10; left: 35.91rem; top: 3.91rem;">
                <path stroke-width="0.250000" fill="none" d="M 0 0 L 1 0" stroke="#58595b" stroke-opacity="1.000000" />
            </svg>
            <p class="paragraph body-text"
                style="width: 53.51rem; height: 1.32rem; font-size: 0.75rem; left: 3.84rem; top: 2.79rem; text-align: left; font-family: 'pro', serif; color: #58595b; font-weight: 600;">
                <span class="position style"
                    style="width: 1.25rem; height: 0.96rem; left: 0.00rem; top: 0.36rem; transform: ScaleX(1.05);">Full Name: <?= filter_var($record['employee_name'], FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?></span>
                
                <span class="position" style="width: 17.17rem; height: 0.96rem; left: 4.20rem; top: 0.36rem;">
                    <span class="style" style="transform: ScaleX(1.05);"> </span>
                    <span class="style" style="text-decoration: underline;"> </span>
                    <span class="style"> </span>
                </span>
                <span class="position style"
                    style="width: 0.75rem; height: 0.96rem; left: 28.05rem; top: 0.36rem; transform: ScaleX(1.05);">ID Card No: <?= filter_var($record['passport_nic_no'], FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?></span>
                
                <span class="position" style="width: 11.07rem; height: 0.96rem; left: 32.38rem; top: 0.36rem;">
                    <span class="style" style="transform: ScaleX(1.05);"> </span>
                    <span class="style" style="text-decoration: underline;"> </span>
                </span>
            </p>
            <div class="textbox"
                style="background: #006b4e; width: 52.36rem; height: 2.12rem; display: block; z-index: 0; left: 3.61rem; top: 6.29rem;">
                <p class="paragraph body-text"
                    style="width: 51.77rem; height: 1.74rem; font-size: 1.00rem; left: 0.60rem; top: -0.00rem; text-align: left; font-family: 'pro', serif; color: #ffffff; font-weight: 600;">
                    <span class="position style"
                        style="width: 2.97rem; height: 1.29rem; left: 0.00rem; top: 0.48rem;">TERMS</span>
                    <span class="position style" style="width: 1.84rem; height: 1.29rem; left: 3.17rem; top: 0.48rem;">
                        AND</span>
                    <span class="position style" style="width: 5.52rem; height: 1.29rem; left: 5.21rem; top: 0.48rem;">
                        CONDITIONS</span>
                </p>
            </div>
            <p class="paragraph body-text"
                style="width: 53.60rem; height: 1.52rem; font-size: 0.90rem; left: 3.75rem; top: 8.54rem; text-align: left; color: #58595b; font-weight: 300;">
                <span class="position style"
                    style="width: 0.22rem; height: 1.11rem; left: 0.00rem; top: 0.42rem;">I</span>
                <span class="position style" style="width: 1.20rem; height: 1.11rem; left: 0.40rem; top: 0.42rem;">
                    her</span>
                <span class="position style"
                    style="width: 1.31rem; height: 1.11rem; left: 1.58rem; top: 0.42rem;">eby</span>
                <span class="position style" style="width: 1.17rem; height: 1.11rem; left: 3.08rem; top: 0.42rem;">
                    agr</span>
                <span class="position style"
                    style="width: 1.07rem; height: 1.11rem; left: 4.24rem; top: 0.42rem;">ee:</span>
            </p>
            <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAkAAAAJCAYAAADgkQYQAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAAGklEQVQYlWPMzSv8z0AAMBFSMKqIgYGBgQEAAiYCXdDSmJcAAAAASUVORK5CYII="
                class="image"
                style="width: 0.36rem; height: 0.36rem; display: block; z-index: 0; left: 5.27rem; top: 10.49rem;" />
            <p class="paragraph body-text"
                style="width: 52.08rem; height: 2.51rem; font-size: 1.10rem; left: 5.27rem; top: 10.02rem; text-align: left; font-family: pro, serif; font-weight: 300;">
                <span class="position" style="width: 1.39rem; height: 1.11rem; left: 1.09rem; top: 0.14rem;">
                    <span class="style"></span>
                    <span class="style" style="font-family: 'pro', serif; font-size: 1.00rem;"> </span>
                    <span class="style"
                        style="font-family: 'pro', serif; font-size: 0.80rem; color: #58595b;">That the information and documents presented for identification purposes may be verified by the Bank’s employee having an <br>appropriate
authority.</span>
                </span>
                
                <span class="position style"
                    style="width: 0.20rem; height: 1.11rem; font-size: 0.90rem; left: 4.39rem; top: 1.34rem; font-family: 'pro', serif; color: #58595b;">.</span>
            </p>
            <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAkAAAAJCAYAAADgkQYQAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAAGklEQVQYlWPMzSv8z0AAMBFSMKqIgYGBgQEAAiYCXdDSmJcAAAAASUVORK5CYII="
                class="image"
                style="width: 0.36rem; height: 0.36rem; display: block; z-index: 0; left: 5.27rem; top: 12.89rem;" />
            <p class="paragraph body-text"
                style="width: 52.08rem; height: 2.44rem; font-size: 1.10rem; left: 5.27rem; top: 12.49rem; text-align: left; font-family: pro, serif; font-weight: 300;">
                <span class="position" style="width: 1.39rem; height: 1.11rem; left: 1.09rem; top: 0.07rem;">
                    <span class="style"></span>
                    <span class="style" style="font-family: 'pro', serif; font-size: 1.00rem;"> </span>
                    <span class="style"
                        style="font-family: 'pro', serif; font-size: 0.80rem; color: #58595b;">That the details furnished above are true and correct to the best of my knowledge and belief and I undertake to inform the Bank<br> of any
changes therein, immediately.</span>
                </span>
                
            </p>
            <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAkAAAAJCAYAAADgkQYQAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAAGklEQVQYlWPMzSv8z0AAMBFSMKqIgYGBgQEAAiYCXdDSmJcAAAAASUVORK5CYII="
                class="image"
                style="width: 0.36rem; height: 0.36rem; display: block; z-index: 0; left: 5.27rem; top: 15.28rem;" />
            <p class="paragraph body-text"
                style="width: 52.08rem; height: 1.15rem; font-size: 1.10rem; left: 5.27rem; top: 14.91rem; text-align: left; font-family: pro, serif; font-weight: 300;">
                <span class="position" style="width: 1.39rem; height: 1.11rem; left: 1.09rem; top: 0.05rem;">
                    <span class="style"></span>
                    <span class="style" style="font-family: 'pro', serif; font-size: 1.00rem;"> </span>
                    <span class="style"
                        style="font-family: 'pro', serif; font-size: 0.80rem; color: #58595b;">That information provided can be used only by the bank for customer relationship purposes.</span>
                </span>
                
            </p>
            <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAkAAAAJCAYAAADgkQYQAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAAGklEQVQYlWPMzSv8z0AAMBFSMKqIgYGBgQEAAiYCXdDSmJcAAAAASUVORK5CYII="
                class="image"
                style="width: 0.36rem; height: 0.36rem; display: block; z-index: 0; left: 5.27rem; top: 16.53rem;" />
            <p class="paragraph body-text"
                style="width: 52.05rem; height: 2.51rem; font-size: 1.10rem; left: 5.27rem; top: 16.02rem; text-align: left; font-family: pro, serif; font-weight: 300;">
                <span class="position" style="width: 0.47rem; height: 1.11rem; left: 1.09rem; top: 0.14rem;">
                    <span class="style"></span>
                    <span class="style" style="font-family: 'pro', serif; font-size: 1.00rem;"> </span>
                    <span class="style" style="font-family: 'pro', serif; font-size: 0.80rem; color: #58595b;">To be bound by the terms and conditions which apply, and which may from time to time change to account(s) opened and services<br> requested
by me with the Bank.</span>
                </span>
                
                <span class="position style"
                    style="width: 0.20rem; height: 1.11rem; font-size: 0.90rem; left: 8.50rem; top: 1.34rem; font-family: 'pro', serif; color: #58595b;">.</span>
            </p>
            <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAkAAAAJCAYAAADgkQYQAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAAGklEQVQYlWPMzSv8z0AAMBFSMKqIgYGBgQEAAiYCXdDSmJcAAAAASUVORK5CYII="
                class="image"
                style="width: 0.36rem; height: 0.36rem; display: block; z-index: 0; left: 5.27rem; top: 18.96rem;" />
            <p class="paragraph body-text"
                style="width: 52.08rem; height: 2.44rem; font-size: 1.10rem; left: 5.27rem; top: 18.49rem; text-align: left; font-family: pro, serif; font-weight: 300;">
                <span class="position" style="width: 1.39rem; height: 1.11rem; left: 1.09rem; top: 0.07rem;">
                    <span class="style"></span>
                    <span class="style" style="font-family: 'pro', serif; font-size: 1.00rem;"> </span>
                    <span class="style"
                        style="font-family: 'pro', serif; font-size: 0.80rem; color: #58595b;">That having read the terms and conditions of this form (Information form for Personal Banking Customers) and agree to abide <br>by and be
bound by the same including any changes therein from time to time.</span>
                </span>
               
            </p>
            <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAkAAAAJCAYAAADgkQYQAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAAGklEQVQYlWPMzSv8z0AAMBFSMKqIgYGBgQEAAiYCXdDSmJcAAAAASUVORK5CYII="
                class="image"
                style="width: 0.36rem; height: 0.36rem; display: block; z-index: 0; left: 5.27rem; top: 21.34rem;" />
            <p class="paragraph body-text"
                style="width: 52.08rem; height: 1.15rem; font-size: 1.10rem; left: 5.27rem; top: 20.91rem; text-align: left; font-family: pro, serif; font-weight: 300;">
                <span class="position" style="width: 1.39rem; height: 1.11rem; left: 1.09rem; top: 0.05rem;">
                    <span class="style"></span>
                    <span class="style"
                        style="font-family: 'pro', serif; font-size: 1.00rem; transform: ScaleX(1.50);">
                    </span>
                    <span class="style"
                        style="font-family: 'pro', serif; font-size: 0.80rem; color: #58595b;">That in case any of the above information is found to be false or untrue or misleading or misrepresenting, I am aware that I will be <br>liable for it.<br><br></span>
                </span>
               
                <span class="position style"
                    style="width: 0.20rem; height: 1.11rem; font-size: 0.90rem; left: 51.29rem; top: 0.05rem; font-family: 'pro', serif; color: #58595b;">.</span>
            </p>
            <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAkAAAAJCAYAAADgkQYQAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAAGklEQVQYlWPMzSv8z0AAMBFSMKqIgYGBgQEAAiYCXdDSmJcAAAAASUVORK5CYII="
                class="image"
                style="width: 0.36rem; height: 0.36rem; display: block; z-index: 0; left: 5.27rem; top: 22.63rem;" />
            <p class="paragraph body-text"
                style="width: 52.08rem; height: 2.51rem; font-size: 1.10rem; left: 5.27rem; top: 22.01rem; text-align: left; font-family: pro, serif; font-weight: 300;">
                <span class="position" style="width: 0.22rem; height: 1.11rem; left: 1.09rem; top: 0.14rem;">
                    <span class="style"></span>
                    <span class="style" style="font-family: 'pro', serif; font-size: 1.00rem;"> </span>
                    <span class="style" style="font-family: 'pro', serif; font-size: 0.80rem; color: #58595b;">I hereby declare and accept that the information I had previously provided to the Bank shall be accepted as the most current <br>and relevant
information in reference to those parts of the form which I have not provided new or additional information.</span>
                </span>
               
            </p>
            <div class="group" style="width: 52.30rem; height: 11.33rem; display: block; left: 3.55rem; top: 26.30rem;">
                <svg viewbox="0.000000, 0.000000, 522.550000, 112.850000" class="graphic"
                    style="width: 52.25rem; height: 11.29rem; display: block; z-index: -10; left: 0.02rem; top: 0.02rem;">
                    <path stroke-width="0.497000" fill="none"
                        d="M 0 112.802 L 522.547 112.802 L 522.547 0 L 0 0 L 0 112.802 Z" stroke="#939598"
                        stroke-opacity="1.000000" />
                </svg>
                <svg viewbox="0.000000, -0.100000, 156.850000, 1.000000" class="graphic"
                    style="width: 15.68rem; height: 0.10rem; display: block; z-index: -10; left: 4.75rem; top: 10.36rem;">
                    <path stroke-width="0.200000" fill="none" d="M 156.825 0 L 0 0" stroke="#abacac"
                        stroke-opacity="1.000000" />
                </svg>
                <svg viewbox="0.000000, -0.100000, 156.850000, 1.000000" class="graphic"
                    style="width: 15.68rem; height: 0.10rem; display: block; z-index: -10; left: 30.30rem; top: 10.36rem;">
                    <path stroke-width="0.200000" fill="none" d="M 156.825 0 L 0 0" stroke="#abacac"
                        stroke-opacity="1.000000" />
                </svg>
                <svg viewbox="-0.125000, 0.000000, 1.000000, 112.850000" class="graphic"
                    style="width: 0.10rem; height: 11.29rem; display: block; z-index: -10; left: 25.37rem; top: 0.02rem;">
                    <path stroke-width="0.250000" fill="none" d="M 0 0 L 0 112.802" stroke="#a7a9ac"
                        stroke-opacity="1.000000" />
                </svg>
                <div class="textbox"
                    style="width: 3.41rem; height: 1.04rem; display: block; z-index: -10; left: 26.17rem; top: 9.80rem;">
                    <p class="paragraph body-text"
                        style="width: 3.41rem; height: 1.04rem; z-index: -10; left: 0.00rem; top: 0.00rem; text-align: left; color: #616262; font-weight: 300;">
                        <span class="position style"
                            style="width: 2.19rem; height: 1.04rem; left: 0.00rem; top: -0.00rem;">Signature</span>
                       
                    </p>
                </div>
                <div class="textbox"
                    style="width: 12.14rem; height: 0.96rem; display: block; z-index: -10; left: 26.52rem; top: 0.83rem;">
                    <p class="paragraph body-text"
                        style="width: 12.14rem; height: 0.96rem; z-index: -10; left: 0.00rem; top: -0.00rem; text-align: left; font-family: 'pro', serif; color: #58595b; font-weight: 300;">
                        <span class="position style"
                            style="width: 0.18rem; height: 0.96rem; left: 0.00rem; top: 0.00rem;">If updating the specimen signature:</span>
                        
                    </p>
                </div>
                <div class="textbox"
                    style="width: 3.41rem; height: 1.04rem; display: block; z-index: -10; left: 0.62rem; top: 9.80rem;">
                    <p class="paragraph body-text"
                        style="width: 3.41rem; height: 1.04rem; z-index: -10; left: 0.00rem; top: 0.00rem; text-align: left; color: #616262; font-weight: 300;">
                        <span class="position style"
                            style="width: 2.91rem; height: 1.04rem; left: -0.00rem; top: -0.00rem;">Signature</span>
                        
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
<script>
    window.print();
</script>