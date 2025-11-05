<!DOCTYPE html>
<html lang="en" data-theme="<?php echo isset($_SESSION['theme']) ? $_SESSION['theme'] : 'light'; ?>">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo $pageTitle ?? 'ClockIt'; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Poppins:wght@600;700&display=swap" rel="stylesheet">
    
    <!-- Your Custom CSS -->
    <link rel="stylesheet" href="/attendance_tracker/assets/css/main.css">
    
    <style>
        /* Theme Toggle Styles (keep these inline) */
        .theme-toggle {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 999;
            width: 3.8em;
            height: 2em;
        }

        .theme-toggle input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .theme-toggle .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #064e44;
            transition: 0.4s;
            border-radius: 30px;
        }

        .theme-toggle .slider:before {
            position: absolute;
            content: "";
            height: 1.6em;
            width: 1.6em;
            border-radius: 20px;
            left: 0.2em;
            bottom: 0.2em;
            background-color: #f7b733;
            transition: 0.4s;
        }

        .theme-toggle input:checked + .slider {
            background-color: #00a6ff;
        }

        .theme-toggle input:checked + .slider:before {
            transform: translateX(1.8em);
            box-shadow: inset 15px -4px 0px 15px #ffcf48;
        }

        .theme-toggle .star {
            background-color: #fff;
            border-radius: 50%;
            position: absolute;
            width: 5px;
            height: 5px;
        }

        .theme-toggle .star_1 {
            left: 2.5em;
            top: 0.5em;
        }
        .theme-toggle .star_2 {
            left: 2.2em;
            top: 1.2em;
        }
        .theme-toggle .star_3 {
            left: 3em;
            top: 0.9em;
        }

        .theme-toggle input:checked ~ .slider .star {
            opacity: 0;
        }

        .theme-toggle .cloud {
            width: 3.5em;
            position: absolute;
            bottom: -1.4em;
            left: -1.1em;
            opacity: 0;
            transition: all 0.4s;
        }

        .theme-toggle input:checked ~ .slider .cloud {
            opacity: 1;
        }

        /* Page fade transitions */
        .fade-enter-active,
        .fade-leave-active {
            transition: opacity 0.4s ease;
        }
        .fade-enter-from,
        .fade-leave-to {
            opacity: 0;
        }
    </style>
</head>