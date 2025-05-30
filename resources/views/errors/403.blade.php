<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <title>403 - 權限不足</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { 
            text-align: center; 
            padding: 150px; 
            font-family: sans-serif; 
            background-color: #fff5f5;
        }
        h1 { 
            font-size: 50px; 
            color: #e53e3e; /* 紅色 */
            margin-bottom: 20px;
        }
        p  { 
            font-size: 20px; 
            color: #4a5568; 
            margin-bottom: 40px;
        }
        a  { 
            color: #3182ce; /* 藍色 */
            text-decoration: none; 
            font-weight: bold; 
            border: 2px solid #3182ce;
            padding: 10px 20px;
            border-radius: 5px;
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        a:hover {
            background-color: #3182ce;
            color: #fff;
        }
    </style>
</head>
<body>
    <h1>403 - 權限不足</h1>
    <p>抱歉，您沒有權限訪問此頁面。</p>
    <p><a href="{{ route('dashboard') }}">回首頁</a></p>
</body>
</html>
