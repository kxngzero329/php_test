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
    
    <style>
        /* Your existing CSS from main.css converted to inline */
        :root {
            --header-bg: #06c3a7; 
            --button-text: #ffffff;
            --bg-color: #ebfffd;
            --panel-bg: #ffffff;
            --text-color: #064e44;
            --subtext-color: #4b6b66;
            --accent-color: #06c3a7;
            --input-bg: rgba(255, 255, 255, 0.95);
            --border-color: rgba(6, 195, 167, 0.3);
            --shadow: 0 4px 20px rgba(6, 195, 167, 0.15);
            --hover-shadow: 0 8px 30px rgba(6, 195, 167, 0.25);
        }

        [data-theme="dark"] {
            --header-bg: #243238; 
            --button-text: #ebfffd; 
            --bg-color: #1f292e;
            --panel-bg: #243238;
            --text-color: #ebfffd;
            --subtext-color: #c8d5d4;
            --accent-color: #06c3a7;
            --input-bg: #2c3b41;
            --border-color: rgba(235, 255, 253, 0.2);
            --shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            --hover-shadow: 0 8px 30px rgba(0, 0, 0, 0.4);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: "Inter", sans-serif;
            background-color: var(--bg-color);
            color: var(--text-color);
            transition: background-color 0.4s ease, color 0.4s ease;
            line-height: 1.6;
            min-height: 100vh;
        }

        /* Theme Toggle Styles */
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

        /* Add all other CSS styles from your main.css here */
        /* ... (include all the CSS from your main.css file) ... */
    </style>
</head>
<body>
    <!-- 🌗 Theme Toggle -->
    <label class="theme-toggle">
        <input type="checkbox" <?php echo (isset($_SESSION['theme']) && $_SESSION['theme'] === 'dark') ? 'checked' : ''; ?> onchange="toggleTheme()" />
        <span class="slider">
            <div class="star star_1"></div>
            <div class="star star_2"></div>
            <div class="star star_3"></div>
            <svg viewBox="0 0 16 16" class="cloud">
                <path
                    transform="scale(0.06)"
                    fill="#fff"
                    d="m391.84 540.91c-.421-.329-.949-.524-1.523-.524-1.351 0-2.451 1.084-2.485 2.435-1.395.526-2.388 1.88-2.388 3.466 0 1.874 1.385 3.423 3.182 3.667v.034h12.73v-.006c1.775-.104 3.182-1.584 3.182-3.395 0-1.747-1.309-3.186-2.994-3.379.007-.106.011-.214.011-.322 0-2.707-2.271-4.901-5.072-4.901-2.073 0-3.856 1.202-4.643 2.925"
                ></path>
            </svg>
        </span>
    </label>

<script>
function toggleTheme() {
    const currentTheme = document.documentElement.getAttribute('data-theme');
    const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
    
    document.documentElement.setAttribute('data-theme', newTheme);
    
    // Send to server to store in session
    fetch('/api/theme?theme=' + newTheme)
        .then(response => response.json())
        .then(data => {
            console.log('Theme updated:', data);
        });
}
</script>