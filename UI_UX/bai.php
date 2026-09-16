<?php

$bai = isset($_GET['bai'])
    ? (int) $_GET['bai']
    : 1;

if ($bai < 1 || $bai > 7) {
    $bai = 1;
}

$cppFile =
    __DIR__
    . "/../Bai_tap/Bai_"
    . $bai
    . ".cpp";

$sourceExists = file_exists($cppFile);

if ($sourceExists) {
    $sourceCode = file_get_contents($cppFile);
} else {
    $sourceCode =
        "Không tìm thấy file Bai_"
        . $bai
        . ".cpp";
}

function runProcess(array $command, string $input = ''): array
{
    $descriptorSpec = [
        0 => ['pipe', 'r'],
        1 => ['pipe', 'w'],
        2 => ['pipe', 'w']
    ];

    $process = proc_open(
        $command,
        $descriptorSpec,
        $pipes
    );

    if (!is_resource($process)) {
        return [
            1,
            '',
            'Không thể chạy chương trình.'
        ];
    }

    fwrite($pipes[0], $input);
    fclose($pipes[0]);

    stream_set_blocking($pipes[1], false);
    stream_set_blocking($pipes[2], false);

    $stdout = '';
    $stderr = '';

    $maxOutput = 1024 * 1024;
    $outputTooLarge = false;

    while (true) {

        $status = proc_get_status($process);

        $outChunk = fread($pipes[1], 8192);
        $errChunk = fread($pipes[2], 8192);

        if ($outChunk !== false && $outChunk !== '') {

            $remaining =
                $maxOutput - strlen($stdout);

            if ($remaining > 0) {

                $stdout .= substr(
                    $outChunk,
                    0,
                    $remaining
                );

            } else {

                $outputTooLarge = true;

            }

        }

        if ($errChunk !== false && $errChunk !== '') {

            $remaining =
                $maxOutput - strlen($stderr);

            if ($remaining > 0) {

                $stderr .= substr(
                    $errChunk,
                    0,
                    $remaining
                );

            } else {

                $outputTooLarge = true;

            }

        }

        if (!$status['running']) {
            break;
        }

        usleep(10000);

    }

    while (!feof($pipes[1])) {

        $chunk = fread(
            $pipes[1],
            8192
        );

        if ($chunk === false || $chunk === '') {
            break;
        }

        $remaining =
            $maxOutput - strlen($stdout);

        if ($remaining > 0) {

            $stdout .= substr(
                $chunk,
                0,
                $remaining
            );

        } else {

            $outputTooLarge = true;
            break;

        }

    }

    while (!feof($pipes[2])) {

        $chunk = fread(
            $pipes[2],
            8192
        );

        if ($chunk === false || $chunk === '') {
            break;
        }

        $remaining =
            $maxOutput - strlen($stderr);

        if ($remaining > 0) {

            $stderr .= substr(
                $chunk,
                0,
                $remaining
            );

        } else {

            $outputTooLarge = true;
            break;

        }

    }

    fclose($pipes[1]);
    fclose($pipes[2]);

    $exitCode =
        proc_close($process);

    if ($outputTooLarge) {

        $stdout .=
            "\n\n[Output đã bị giới hạn vì chương trình in quá nhiều dữ liệu.]";

    }

    return [
        $exitCode,
        $stdout,
        $stderr
    ];
}

$inputData = '';
$outputData = '';
$hasRun = false;
$runSuccess = false;

if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['run'])
) {

    $hasRun = true;

    $inputData =
        $_POST['input_data'] ?? '';

    if (strlen($inputData) > 10000) {

        $inputData =
            substr(
                $inputData,
                0,
                10000
            );

    }

    if (!$sourceExists) {

        $outputData =
            "Không tìm thấy file Bai_"
            . $bai
            . ".cpp";

    } else {

        $tempFile =
            tempnam(
                sys_get_temp_dir(),
                "cpp_bai_"
            );

        if ($tempFile === false) {

            $outputData =
                "Không thể tạo file tạm.";

        } else {

            if (file_exists($tempFile)) {
                unlink($tempFile);
            }

            $binaryFile =
                $tempFile;

            [
                $compileCode,
                $compileOutput,
                $compileError
            ] = runProcess(
                [
                    'g++',
                    '-std=c++17',
                    '-O0',
                    '-g',
                    '-Wall',
                    '-Wextra',
                    $cppFile,
                    '-o',
                    $binaryFile
                ]
            );

            if ($compileCode !== 0) {

                $outputData =
                    "COMPILE ERROR:\n\n"
                    . $compileError;

            } else {

                [
                    $runCode,
                    $runOutput,
                    $runError
                ] = runProcess(
                    [
                        'timeout',
                        '3s',
                        $binaryFile
                    ],
                    $inputData
                );

                $outputData =
                    $runOutput;

                if ($runError !== '') {

                    if ($outputData !== '') {
                        $outputData .= "\n";
                    }

                    $outputData .=
                        $runError;

                }

                if ($runCode === 124) {

                    $outputData .=
                        "\n\nProgram stopped: Time limit exceeded.";

                } elseif (
                    $runCode !== 0
                    && $runCode !== 124
                ) {

                    $outputData .=
                        "\n\nProgram exited with code "
                        . $runCode
                        . ".";

                } else {

                    $runSuccess = true;

                }

                if (
                    trim($outputData) === ''
                    && $runCode === 0
                ) {

                    $outputData =
                        "Program finished without output.";

                }

            }

            if (file_exists($binaryFile)) {
                unlink($binaryFile);
            }

        }

    }

}

?>

<!DOCTYPE html>

<html lang="vi">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Bài <?php echo $bai; ?> | Code Learning
    </title>

    <link
        rel="stylesheet"
        href="style.css"
    >

</head>

<body>

<header class="header">

    <a href="index.html" class="logo">

        <div class="logo-icon">
            &lt;/&gt;
        </div>

        <div class="logo-text">
            <strong>Code</strong>
            <span>Learning</span>
        </div>

    </a>

    <nav class="nav-menu">

        <a
            href="index.html"
            class="nav-item"
        >

            <svg viewBox="0 0 24 24">
                <path d="M3 11L12 3L21 11"/>
                <path d="M5 10V21H19V10"/>
            </svg>

            <span>Trang chủ</span>

        </a>

        <a
            href="index.html#lessons"
            class="nav-item active"
        >

            <svg viewBox="0 0 24 24">
                <path d="M4 5H20"/>
                <path d="M4 12H20"/>
                <path d="M4 19H20"/>
            </svg>

            <span>Bài tập</span>

        </a>

        <a
            href="index.html#about"
            class="nav-item"
        >

            <svg viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="9"/>
                <path d="M12 11V17"/>
                <path d="M12 7H12.01"/>
            </svg>

            <span>Giới thiệu</span>

        </a>

    </nav>

    <div class="online-status">
        <span class="status-dot"></span>
        <span>Online</span>
    </div>

</header>

<main class="exercise-page">

    <div class="exercise-page-glow"></div>

    <div class="breadcrumb">

        <a href="index.html">
            Trang chủ
        </a>

        <span>/</span>

        <a href="index.html#lessons">
            Bài tập
        </a>

        <span>/</span>

        <strong>
            Bài <?php echo $bai; ?>
        </strong>

    </div>

    <div class="exercise-layout">

        <section class="exercise-main">

            <div class="exercise-heading">

                <div>

                    <div class="section-label">
                        <span></span>
                        C++ EXERCISE
                    </div>

                    <h1>
                        Bài <?php echo $bai; ?>
                    </h1>

                    <p>
                        Bai_tap/Bai_<?php echo $bai; ?>.cpp
                    </p>

                </div>

                <div class="language-badge">
                    C++
                </div>

            </div>

            <div class="source-window">

                <div class="source-header">

                    <div class="terminal-dots">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>

                    <div class="source-filename">
                        Bai_<?php echo $bai; ?>.cpp
                    </div>

                    <div class="source-type">
                        Source
                    </div>

                </div>

                <pre><code><?php
echo htmlspecialchars(
    $sourceCode,
    ENT_QUOTES,
    'UTF-8'
);
?></code></pre>

            </div>

            <div class="runner-card">

                <div class="runner-heading">

                    <div>

                        <div class="section-label">
                            <span></span>
                            PROGRAM RUNNER
                        </div>

                        <h2>
                            Chạy chương trình
                        </h2>

                    </div>

                    <div class="runner-status">

                        <span></span>

                        Ready

                    </div>

                </div>

                <form
                    method="POST"
                    action="bai.php?bai=<?php echo $bai; ?>"
                >

                    <div class="input-heading">

                        <label for="input_data">
                            Input
                        </label>

                        <span>
                            stdin
                        </span>

                    </div>

                    <textarea
                        id="input_data"
                        name="input_data"
                        placeholder="Nhập input cho chương trình..."
                    ><?php
echo htmlspecialchars(
    $inputData,
    ENT_QUOTES,
    'UTF-8'
);
?></textarea>

                    <button
                        type="submit"
                        name="run"
                        value="1"
                        class="run-button"
                    >

                        <svg viewBox="0 0 24 24">
                            <path d="M8 5L18 12L8 19Z"/>
                        </svg>

                        Run C++

                    </button>

                </form>

                <div class="output-window">

                    <div class="output-top">

                        <div>

                            <span class="output-symbol">
                                &gt;_
                            </span>

                            Output

                        </div>

                        <?php if ($hasRun): ?>

                            <?php if ($runSuccess): ?>

                                <span class="output-success">
                                    Success
                                </span>

                            <?php else: ?>

                                <span class="output-error">
                                    Error
                                </span>

                            <?php endif; ?>

                        <?php else: ?>

                            <span class="output-idle">
                                Waiting
                            </span>

                        <?php endif; ?>

                    </div>

                    <pre><?php

if ($hasRun) {

    echo htmlspecialchars(
        $outputData,
        ENT_QUOTES,
        'UTF-8'
    );

} else {

    echo "Output của chương trình sẽ hiển thị tại đây.";

}

?></pre>

                </div>

            </div>

            <div class="exercise-navigation">

                <?php if ($bai > 1): ?>

                    <a
                        class="nav-btn"
                        href="bai.php?bai=<?php echo $bai - 1; ?>"
                    >
                        <span>←</span>
                        Bài <?php echo $bai - 1; ?>
                    </a>

                <?php else: ?>

                    <a
                        class="nav-btn"
                        href="index.html"
                    >
                        <span>←</span>
                        Trang chủ
                    </a>

                <?php endif; ?>

                <?php if ($bai < 7): ?>

                    <a
                        class="nav-btn nav-next"
                        href="bai.php?bai=<?php echo $bai + 1; ?>"
                    >
                        Bài <?php echo $bai + 1; ?>
                        <span>→</span>
                    </a>

                <?php else: ?>

                    <a
                        class="nav-btn nav-next"
                        href="index.html"
                    >
                        Hoàn thành
                        <span>✓</span>
                    </a>

                <?php endif; ?>

            </div>

        </section>

        <aside class="exercise-sidebar">

            <div class="sidebar-card">

                <div class="sidebar-title">
                    Bài tập
                </div>

                <?php for ($i = 1; $i <= 7; $i++): ?>

                    <a
                        href="bai.php?bai=<?php echo $i; ?>"
                        class="sidebar-lesson <?php echo $bai === $i ? 'selected' : ''; ?>"
                    >

                        <span class="sidebar-number">
                            <?php echo str_pad($i, 2, '0', STR_PAD_LEFT); ?>
                        </span>

                        <span>
                            Bài <?php echo $i; ?>
                        </span>

                        <span class="sidebar-arrow">
                            →
                        </span>

                    </a>

                <?php endfor; ?>

            </div>

            <div class="sidebar-info">

                <div class="sidebar-info-icon">
                    &lt;/&gt;
                </div>

                <strong>
                    C++ Runner
                </strong>

                <p>
                    Source được compile bằng g++ và chạy
                    trực tiếp trong container.
                </p>

                <div class="sidebar-tech">

                    <span>g++</span>
                    <span>Docker</span>
                    <span>PHP</span>

                </div>

            </div>

        </aside>

    </div>

</main>

<footer class="footer">

    <a href="index.html" class="footer-brand">

        <div class="footer-logo-icon">
            &lt;/&gt;
        </div>

        <div>
            <strong>Code Learning</strong>
            <span>C++ Programming Exercises</span>
        </div>

    </a>

    <p>
        © 2026 Code Learning
    </p>

    <a href="#" class="back-top">
        ↑
    </a>

</footer>

</body>

</html>