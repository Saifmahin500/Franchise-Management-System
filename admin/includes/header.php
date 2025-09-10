<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            display: flex;
        }

        /* Sidebar styling */
        .sidebar {
            width: 250px;
            background: #173831;
            color: #fff;
            flex-shrink: 0;
        }

        .sidebar h4 {
            padding: 20px;
            margin: 0;
            font-size: 1.2rem;
        }

        .sidebar a {
            color: #fff;
            font-size: 16px;
            font-weight: bold;
            text-decoration: none;
            display: block;
            padding: 12px 20px;
            transition: all 0.3s;
        }

        .sidebar a:hover {
            background: #DBF0DD;
            color: black;
        }

        .content {
            flex-grow: 1;
            background: #f8f9fa;
            display: flex;
            flex-direction: column;
        }

        .topbar {
            background: #fff;
            padding: 12px 20px;
            border-bottom: 1px solid #ddd;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .topbar .profile {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .menu-title {
            margin-left: 8px;
        }

        .card {
            color: rgb(0, 13, 10);
        }

        #dcard {
            background: #DBF0DD;
        }

        .btn_b {
            background-color: #173831;
            color: #fff;
            font-weight: bold;
        }

        .btn_b:hover {
            background-color: #DBF0DD;
            color: #000;
        }
       
    </style>
</head>

<body>
    