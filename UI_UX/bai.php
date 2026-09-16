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
            'Không thể chạy tiến trình.'
        ];
    }

    if ($input !== '') {
        fwrite($pipes[0], $input);
    }

    fclose($pipes[0]);

    $stdout =
        stream_get_contents($pipes[1]);

    fclose($pipes[1]);

    $stderr =
        stream_get_contents($pipes[2]);

    fclose($pipes[2]);

    $exitCode =
        proc_close($process);

    return [
        $exitCode,
        $stdout,
        $stderr
    ];
}

$inputData = '';
$outputData = '';
$hasRun = false;

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
        Bài <?php echo $bai; ?> | C++
    </title>

    <link
        rel="stylesheet"
        href="style.css"
    >

</head>

<body>

<header class="header">

    <div class="logo">

        <span>
            &lt;/&gt;
        </span>

        Code Learning

    </div>

    <nav>

        <a href="index.html">
            Trang chủ
        </a>

        <a href="index.html#lessons">
            Bài tập
        </a>

        <a href="index.html#about">
            Giới thiệu
        </a>

    </nav>

</header>

<main class="exercise-page">

    <div class="breadcrumb">

        <a href="index.html">
            Trang chủ
        </a>

        <span>
            ›
        </span>

        <span>
            Bài <?php echo $bai; ?>
        </span>

    </div>

    <div class="exercise-container">

        <div class="exercise-header">

            <div>

                <p class="exercise-label">
                    C++ EXERCISE
                </p>

                <h1>
                    Bài <?php echo $bai; ?>
                </h1>

                <p class="exercise-path">
                    Bai_tap/Bai_<?php echo $bai; ?>.cpp
                </p>

            </div>

            <div class="cpp-badge">
                C++
            </div>

        </div>

        <div class="source-window">

            <div class="source-header">

                <div class="dots">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>

                <p>
                    Bai_<?php echo $bai; ?>.cpp
                </p>

            </div>

            <pre><code><?php
echo htmlspecialchars(
    $sourceCode,
    ENT_QUOTES,
    'UTF-8'
);
?></code></pre>

        </div>

        <div class="runner">

            <div class="runner-title">

                <div>

                    <p class="exercise-label">
                        PROGRAM INPUT
                    </p>

                    <h2>
                        Chạy chương trình
                    </h2>

                </div>

                <span class="terminal-badge">
                    Terminal
                </span>

            </div>

            <form
                method="POST"
                action="bai.php?bai=<?php echo $bai; ?>"
            >

                <label for="input_data">
                    Input
                </label>

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
                    ▶ Run C++
                </button>

            </form>

            <div class="output-container">

                <div class="output-header">

                    <span>
                        Output
                    </span>

                    <?php if ($hasRun): ?>

                        <span class="run-status">
                            Executed
                        </span>

                    <?php endif; ?>

                </div>

                <pre class="program-output"><?php

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
                    ← Bài <?php echo $bai - 1; ?>
                </a>

            <?php else: ?>

                <a
                    class="nav-btn"
                    href="index.html"
                >
                    ← Trang chủ
                </a>

            <?php endif; ?>

            <?php if ($bai < 7): ?>

                <a
                    class="nav-btn nav-next"
                    href="bai.php?bai=<?php echo $bai + 1; ?>"
                >
                    Bài <?php echo $bai + 1; ?> →
                </a>

            <?php else: ?>

                <a
                    class="nav-btn nav-next"
                    href="index.html"
                >
                    Hoàn thành ✓
                </a>

            <?php endif; ?>

        </div>

    </div>

</main>

<footer>

    <div class="footer-logo">
        &lt;/&gt; Code Learning
    </div>

    <p>
        Tự học lập trình © 2026
    </p>

</footer>

</body>

</html>